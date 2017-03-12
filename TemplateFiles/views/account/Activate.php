<?php namespace ProcessWire;
if($user->wm_account_status == "active") {
	$session->redirect($page->httpRoot . "user/profile/");
}
$message = ["class" => "", "text" => ""];

$form = "";
// get variables are set
if(isset($input->get->user) && isset($input->get->hash)) {
	$username = $sanitizer->pageName("{$input->get->user}");
	$u = $pages->get("template=user, name={$username}");
	// get variables are valid
	if($u->id && strcmp($u->accountStatus, $hash) === 0) {
		$u->of(false);
		$u->accountStatus = "active";
		$u->save();
	}
	// get variables are invalid
	else {
		$message = ["class" => "uk-text-warning", "text" => "Something went wrong!"];
	}
}
// get variables are not set
else {
	// user is logged in
	// show resend email form with optional email change
	if($user->isLoggedin()) {
		// form was submitted
		if($input->post->submit){
			// with valid email
			if($input->post->email && $sanitizer->email($input->post->email)) {
				$user->of(false);
				$user->email = $input->post->email;
				$user->save();
				$message = resendActivationMail() ?
					["class" => "uk-text-success", "text" => "The email was sent1."] :
					["class" => "uk-text-warning", "text" => "The email was sent2."];
			}
			// with invalid email
			elseif($input->post->email && !$sanitizer->email($input->post->email)) {
				$message = ["class" => "uk-text-warning", "text" => "The email address is not valid."];
			}
			// with no email
			elseif(!$input->post->email) {
				resendActivationMail();
				$message = ["class" => "uk-text-success", "text" => "The email was sent0."];
			}
		}
		$form .= "<form action='' method='post' class=''>";
		$form .= "<label for='email' class='uk-form-label'>Change email (optional)</label>";
		$form .= "<input type='text' name='email' id='email' placeholder='{$user->email}' class='uk-input uk-margin-bottom'>";
		$form .= "<input type='submit' name='submit' value='Resend activation email.' class='uk-input'>";
		$form .= "</form>";
	}
	// user is not logged in
	// show login form
	else {
		$account->loginUser(); // process login form if submitted
		echo "<section id=wire'page-{$page->id}' class='uk-width-large uk-margin-auto'>";
			echo "<h1>Login</h1>";
			echo $account->loginForm('', 'uk-margin-bottom', 'uk-input', 'uk-form-label');
			echo "<div><a href='{$page->parent->httpUrl}password-reset/'>Forgot password?</a></div>";
		echo "</section>";
	}
	echo "false";
}

echo "<section class='uk-width-large uk-margin-auto'>";
echo "<div class='{$message["class"]} uk-margin-bottom'>{$message["text"]}</div>";
echo $form;
echo "</section>";

// send the email
function resendActivationMail() {
	$user = wire("user");
	$config = wire("config");
	$activationLink = $config->urls->httpRoot."user/activate/?user=".$user->name."&hash=".$user->accountStatus;

	// resend activation email
	$emailMessage  = "Hi {$user->name}!";
	$emailMessage .= "<br>To activate your account click the link below.";
	$emailMessage .= "<br><a href='{$activationLink}'>{$activationLink}</a>";
	$emailMessage .= "<br><br>If you didn't request this email then someone else is trying to use your email address.";
	$mail = wireMail();
	$mail->to($user->email)->from($config->wm_site_email); // all calls can be chained
	$mail->subject("Email verification @ {$config->wm_site_name}");
	$mail->bodyHTML($emailMessage);
	return $mail->send();
}
