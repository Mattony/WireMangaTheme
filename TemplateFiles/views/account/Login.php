<?php namespace ProcessWire;
if($user->isLoggedin()) {
	$session->redirect($config->urls->httpRoot . "user/profile/");
}
?>

<section id="page-<?= $page->id ?>" class="uk-width-large uk-margin-auto">
<?= $registration_message ?>
<?= $login_error ?>
	<h1><?= $page->title ?></h1>
	<form method="post" class="login-form">
			<div class="form-group uk-margin-bottom">
				<label for="user" class="uk-form-label">Username</label>
				<input type="text" name="user" id="user" class="uk-input" placeholder="Username">
			</div>
			<div class="form-group uk-margin-bottom">
				<label for="pass" class="uk-form-label">Password</label>
				<input type="password" name="pass" id="pass" class="uk-input" placeholder="Password">
			</div>
			<div class="form-group uk-margin-bottom"><input type="submit" name="submit" id="" class="uk-button" value="Login">
			<a href="<?= $page->parent->httpUrl ?>user/password-reset" class="uk-button uk-button-danger">Forgot password?</a></div>
		</form>
</section>
