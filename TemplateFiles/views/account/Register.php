<?php namespace ProcessWire; 
if($user->isLoggedin()) {
	$session->redirect($config->urls->httpRoot . "user/profile/");
}
$passField  = $fields->get("pass");
$inputfield = $passField->getInputfield($page, $passField);
$passLength = $inputfield->minlength;
$requirements = $inputfield->requirements;
$passReq = str_replace("other", "special character", implode(", ", $requirements));
?>

<section id="page-<?= $page->id ?>" class="uk-width-large uk-margin-auto">
<?php
	if($input->post->submit) {
		$register = $account->register();
		if(!$register) {
			echo $session->get("registration_message");
		} else {
			$activate_message = null;
			if($settings->wm_user_activate) {
				$activate_message = " Check your email for the activation link. You can login to resend the email or delete the account.";
			}
			$session->set("registration_message", "<div class='message success'>Your account has been created!{$activate_message}</div>");
			$session->redirect($config->urls->httpRoot . "user/login/");
		}
	}
?>
	<h1><?= $page->title?></h1>
	<form method="post" class="form registration-form">
		<div class="form-group uk-margin-bottom">
			<label for="username" class="uk-form-label">Username</label>
			<input type="text" name="username" id="username" class="uk-input">
			<em>Allowed characters: lowercase letters (a-z), digits (0-9), underscore (_), hyphen (-) and period (.), 
			don"t use underscore, hyphen and period one after the other.</em>
		</div>

		<div class="form-group uk-margin-bottom">
			<label for="email" class="uk-form-label">Email</label>
			<input type="text" name="email" id="email" class="uk-input">
		</div>

		<div class="form-group uk-margin-bottom">
			<label for="password" class="uk-form-label">Password</label>
			<input type="password" name="password" id="password" class="uk-input">
			<br><em>Requirements: at least <?= $passLength ?> characters long, <?= $passReq ?>.</em>
		</div>
		<div class="form-group uk-margin-bottom">
			<label for="_password" class="uk-form-label">Confirm Password</label>
			<input type="password" name="_password" id="_password" class="uk-input">
		</div>

		<div class="form-group uk-margin-bottom"><input type="submit" name="submit" class="uk-button uk-button-primary" value="Register"></div>
	</form>
</section>

<?php $session->remove("registration_message"); ?>
