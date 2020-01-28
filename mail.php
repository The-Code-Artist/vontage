<?php
/*
    * Title         : AJAX_Mail_Script
    * Date          : 11-Apr-2019
    * Author        : Marothi Mahlake
    * Description	: Process an AJAX request from an HTML contact form and send an email to STELC.
	* File name		: mail.php
*/
// TODO: Process the contact form upon submission.
$request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

if ($request_method == "POST") {
	// TODO: Enforce strict AJAX request form submission.
	$request_type = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');

	if (!isset($request_type) && strtolower($request_type) != 'xmlhttprequest') {
		$output = json_encode(array(
			'text' => 'Please submit form data via AJAX request only.'
		));
		die($output);
	}

	// TODO: Retrieve the submitted form data.
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	$contact = filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING);
	$service_request = filter_input(INPUT_POST, 'service_request', FILTER_SANITIZE_STRING);
	$message_body = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

	// NOTE: The logic below is for input data validation purposes.
	// Name validation.
	if (empty($name)) {
		$output = json_encode(array(
			'text' => 'Please enter your name.'
		));
		die($output);
	} else if (strlen($name) < 6) {
		$output = json_encode(array(
			'text' => 'Your name can contain at least six characters.'
		));
		die($output);
	}

	// Email address validation.
	if (empty($email)) {
		$output = json_encode(array(
			'text' => 'Please enter your email address.'
		));
		die($output);
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$output = json_encode(array(
			'text' => 'Please enter a valid email address.'
		));
		die($output);
	} else if (strlen($email) < 7) {
		$output = json_encode(array(
			'text' => 'The email address must contain at least 7 characters.'
		));
		die($output);
	}

	// Contact number validation.
	if (empty($contact)) {
		$output = json_encode(array(
			'text' => 'Please enter your contact phone number.'
		));
		die($output);
	} else if (strlen($contact) < 10) {
		$output = json_encode(array(
			'text' => 'The contact number must at least be 10 digits long.'
		));
		die($output);
	}

	// Service request validation.
	if (empty($service_request)) {
		$output = json_encode(array(
			'text' => 'Please specify a service request type.'
		));
		die($output);
	}

	// Message validation.
	if (empty($message_body)) {
		$output = json_encode(array(
			'text' => 'Please type in your message.'
		));
		die($output);
	} else if (strlen($message_body) < 20) {
		$output = json_encode(array(
			'text' => 'The message must at least contain 20 characters.'
		));
		die($output);
	}

	// Email centric variable setup...
	$subject = "Vontage contact form message";
	$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	$message .= '<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--[if !mso]><!-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<!--<![endif]-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" />
	<title>Vontage Contact Email</title>
	<style type="text/css">
		* {
			-webkit-font-smoothing: antialiased;
		}

		body {
			Margin: 0;
			padding: 0;
			min-width: 100%;
			font-family: Arial, sans-serif;
			-webkit-font-smoothing: antialiased;
			mso-line-height-rule: exactly;
		}

		table {
			border-spacing: 0;
			color: #333333;
			font-family: Arial, sans-serif;
		}

		img {
			border: 0;
		}

		.wrapper {
			width: 100%;
			table-layout: fixed;
			-webkit-text-size-adjust: 100%;
			-ms-text-size-adjust: 100%;
		}

		.webkit {
			max-width: 600px;
		}

		.outer {
			Margin: 0 auto;
			width: 100%;
			max-width: 600px;
		}

		.full-width-image img {
			width: 100%;
			max-width: 600px;
			height: auto;
		}

		.inner {
			padding: 10px;
		}

		p {
			Margin: 0;
			padding-bottom: 10px;
		}

		.h1 {
			font-size: 21px;
			font-weight: bold;
			Margin-top: 15px;
			Margin-bottom: 5px;
			font-family: Arial, sans-serif;
			-webkit-font-smoothing: antialiased;
		}

		.h2 {
			font-size: 18px;
			font-weight: bold;
			Margin-top: 10px;
			Margin-bottom: 5px;
			font-family: Arial, sans-serif;
			-webkit-font-smoothing: antialiased;
		}

		.one-column .contents {
			text-align: left;
			font-family: Arial, sans-serif;
			-webkit-font-smoothing: antialiased;
		}

		.one-column p {
			font-size: 14px;
			Margin-bottom: 10px;
			font-family: Arial, sans-serif;
			-webkit-font-smoothing: antialiased;
		}

		.two-column {
			text-align: center;
			font-size: 0;
		}

		.two-column .column {
			width: 100%;
			max-width: 300px;
			display: inline-block;
			vertical-align: top;
		}

		.contents {
			width: 100%;
		}

		.two-column .contents {
			font-size: 14px;
			text-align: left;
		}

		.two-column img {
			width: 100%;
			max-width: 280px;
			height: auto;
		}

		.two-column .text {
			padding-top: 10px;
		}

		.three-column {
			text-align: center;
			font-size: 0;
			padding-top: 10px;
			padding-bottom: 10px;
		}

		.three-column .column {
			width: 100%;
			max-width: 200px;
			display: inline-block;
			vertical-align: top;
		}

		.three-column .contents {
			font-size: 14px;
			text-align: center;
		}

		.three-column img {
			width: 100%;
			max-width: 180px;
			height: auto;
		}

		.three-column .text {
			padding-top: 10px;
		}

		.img-align-vertical img {
			display: inline-block;
			vertical-align: middle;
		}

		@media only screen and (max-device-width: 480px) {

			table[class=hide],
			img[class=hide],
			td[class=hide] {
				display: none !important;
			}

			.contents1 {
				width: 100%;
			}

			.contents1 {
				width: 100%;
			}
		}

		a {
			color: #F0C270;
		}
	</style>
	<!--[if (gte mso 9)|(IE)]>
	<style type="text/css">
		table {border-collapse: collapse !important;}
	</style>
	<![endif]-->
</head>';
	$message .= '<body style="Margin:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;min-width:100%;background-color:#ececec;">
<center class="wrapper" style="width:100%;table-layout:fixed;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;background-color:#ececec;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#ececec;" bgcolor="#ececec;">
    <tr>
      <td width="100%"><div class="webkit" style="max-width:600px;Margin:0 auto;"> 
          
          <!--[if (gte mso 9)|(IE)]>

						<table width="600" align="center" cellpadding="0" cellspacing="0" border="0" style="border-spacing:0" >
							<tr>
								<td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
								<![endif]--> 
          
          <!-- ======= start main body ======= -->
          <table class="outer" align="center" cellpadding="0" cellspacing="0" border="0" style="border-spacing:0;Margin:0 auto;width:100%;max-width:600px;">
            <tr>
              <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;"><!-- ======= start header ======= -->
                
                <table border="0" width="100%" cellpadding="0" cellspacing="0"  >
                  <tr>
                    <td>
						<table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
                        <tbody>
                          <tr>
                            <td align="center">
								<center>
                                <table border="0" align="center" width="100%" cellpadding="0" cellspacing="0" style="Margin: 0 auto;">
                                  <tbody>
                                    <tr>
                                      <td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-spacing:0">
                                          <tr>
                                            <td>&nbsp;</td>
                                          </tr>
                                          <tr>
                                            <td align="center">&nbsp;</td>
                                          </tr>
                                          <tr>
                                            <td height="6" bgcolor="#000" class="contents" style="width:100%; border-top-left-radius:10px; border-top-right-radius:10px"></td>
                                          </tr>
                                        </table></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </center></td>
                          </tr>
                        </tbody>
                      </table></td>
                  </tr>
                </table>
                <table border="0" width="100%" cellpadding="0" cellspacing="0"  >
                  <tr>
                    <td><table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
                        <tbody>
                          <tr>
                            <td align="center"><center>
                                <table border="0" align="center" width="100%" cellpadding="0" cellspacing="0" style="Margin: 0 auto;">
                                  <tbody>
                                    <tr>
                                      <td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" bgcolor="#FFFFFF"><!-- ======= start header ======= -->
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                          <tr>
                                            <td class="two-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-align:center;font-size:0;"><!--[if (gte mso 9)|(IE)]>
													<table width="100%" style="border-spacing:0" >
													<tr>
													<td width="20%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
													<![endif]-->
                                              
                                              <div class="column" style="width:100%;max-width:110px;display:inline-block;vertical-align:top;">
                                                <table class="contents" style="border-spacing:0; width:100%"  bgcolor="#ffffff">
                                                </table>
                                              </div>
                                              
                                              <!--[if (gte mso 9)|(IE)]>
													</td><td width="80%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
													<![endif]-->
                                              
                                              <div class="column" style="width:100%;max-width:415px;display:inline-block;vertical-align:top;">
                                                <table width="100%" style="border-spacing:0" bgcolor="#ffffff">
                                                  <tr>
                                                    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="hide">
                                                        <tr>
                                                          <td height="60">&nbsp;</td>
                                                        </tr>
                                                      </table></td>
                                                  </tr>
                                                  <tr>
                                                    <td class="inner" style="padding-top:10px;padding-bottom:10px; padding-right:10px;padding-left:10px;"><table class="contents" style="border-spacing:0; width:100%" bgcolor="#FFFFFF">
                                                      </table></td>
                                                  </tr>
                                                </table>
                                              </div>
                                              
                                              <!--[if (gte mso 9)|(IE)]>
													</td>
													</tr>
													</table>
													<![endif]--></td>
                                          </tr>
                                          <tr>
                                            <td align="left" style="padding-left:40px"><table border="0" cellpadding="0" cellspacing="0" style="border-bottom:2px solid #ddd" align="left">
                                                <tr>
                                                  <td height="20" width="30" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
                                                </tr>
                                              </table></td>
                                          </tr>
                                          <tr>
                                            <td>&nbsp;</td>
                                          </tr>
                                        </table></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </center></td>
                          </tr>
                        </tbody>
                      </table></td>
                  </tr>
                </table>
                
                <!-- ======= end header ======= --> 
                
                <!-- ======= start hero image ======= --><!-- ======= end hero image ======= --> 
                
                <!-- ======= start hero article ======= -->
                
                <table class="one-column" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-spacing:0" bgcolor="#FFFFFF">
                  <tr>
                    <td align="left" style="padding:0px 40px 40px 40px"><p style="color:#5b5f65; font-size:28px; text-align:left; font-family: Verdana, Geneva, sans-serif">Hi Vontage Lifestyle, </p>
                      <p style="color:#5b5f65; font-size:16px; text-align:left; font-family: Verdana, Geneva, sans-serif">A prospective client has attempted to contact you through the website\'s contact form. The details are listed below:<br />
                        <hr />
						<b>Name: </b>' . $name . '<br />
						<b>Email: </b>' . $email . '<br />
						<b>Contact: </b>' . $contact . '<br />
						<b>Request: </b>' . $service_request . '<br />
						<b>Message: </b>' . $message_body . '
					</p>                      
                    </td>
                  </tr>
                </table>
                
                <!-- ======= end hero article ======= -->  
                
                <!-- ======= start footer ======= -->
                
               <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100%" cellpadding="0" cellspacing="0" border="0"  bgcolor="#000">
      <tr>
        <td height="40" align="center" bgcolor="#000" class="one-column">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" bgcolor="#000" class="one-column" style="padding-top:0;padding-bottom:0;padding-right:10px;padding-left:10px;"><font style="font-size:13px; text-decoration:none; color:#ffffff; font-family: Verdana, Geneva, sans-serif; text-align:center">Vontage Lavish Lifestyle (Pty) Ltd</font></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#000" class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" bgcolor="#000" class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;"><table width="150" border="0" cellspacing="0" cellpadding="0">
          <tr>
			<td width="33" align="center">
				<a href="https://www.facebook.com/vontage.menoe" target="_blank">
					<i class="fa fa-facebook"></i>
				</a>
		</td>
            <td width="34" align="center">
				<a href="https://twitter.com/VontageLavish" target="_blank">
					<i class="fa fa-twitter"></i>
				</a>
		</td>
            <td width="33" align="center">
				<a href="https://www.instagram.com/vontagelifestyle" target="_blank">
					<i class="fa fa-instagram"></i>
				</a>
		</td>
          </tr>
		</table>
	</td>
      </tr>
      <tr>
        <td align="center" bgcolor="#000" class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" bgcolor="#000" class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">&nbsp;</td>
      </tr>
      <tr>
        <td height="6" bgcolor="#000" class="contents1" style="width:100%; border-bottom-left-radius:10px; border-bottom-right-radius:10px"></td>
      </tr>
      </table></td>
  </tr>
  <tr>
    <td><table width="100%" cellpadding="0" cellspacing="0" border="0"> 
      <tr>
        <td height="6" bgcolor="#000" class="contents" style="width:100%; border-bottom-left-radius:10px; border-bottom-right-radius:10px"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>

               <!-- ======= end footer ======= --></td>
            </tr>
          </table>
          <!--[if (gte mso 9)|(IE)]>
					</td>
				</tr>
			</table>
			<![endif]--> 
        </div></td>
    </tr>
  </table>
</center>
</body>';

	$message .= '</html>';
	$to = "info@vontagelifestyle.com";
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'To: ' . $to . "\r\n";
	$headers .= 'From: noreply@vontagelifestyle.com' . "\r\n";

	if (mail($to, $subject, $message, $headers)) {
		$output = json_encode(array(
			'text' => 'Your message has successfully been sent to Vontage.'
		));
		die($output);
	} else {
		$output = json_encode(array(
			'text' => 'Sorry, we could not send your message to Vontage.'
		));
		die($output);
	}
}
