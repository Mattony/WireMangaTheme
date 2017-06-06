<?php namespace ProcessWire;
if($user->wm_activation_code == 0) {
	$session->redirect($config->urls->httpRoot . "user/profile/");
}

$message = ["class" => "", "text" => ""];

$form = "";
// get variables are set
if(isset($input->get->user) && isset($input->get->hash)) {
	if(!$account->activateUserAccount($input->get->user, $input->get->hash)) {
		echo "<div class='message error'>Something went wrong!</div>";
	}
	echo "<div class='message success'>Your account was activated!</div>";
} else { // get variables are not set
	if($user->isLoggedin()) {
		if($input->post->submit_resend){
			$account->sendActivationMail($user->email, $user->name, $user->activation_code);
		}
		if($input->post->submit_delete){
			$u = $users->get($user->name);
			$session->logout();
			$users->delete($u);
		}
		// show resend email form with optional email change
		echo "<section class='uk-width-large uk-margin-auto'>";
		echo "<form method='post'>";
		echo "<input type='submit' name='submit_resend' value='Resend activation email' class='uk-button uk-button-primary'> ";
		echo "<input type='submit' name='submit_delete' value='Delete account' class='uk-button uk-button-danger'>";
		echo "</form>";
		echo "</section>";
	} else { // user is not logged in show login form
		$account->loginUser(); // process login form if submitted
		echo "<section id=wire'page-{$page->id}' class='uk-width-large uk-margin-auto'>";
			echo "<h1>Login</h1>"; ?>
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
		<?php echo "</section>";
	}
}


