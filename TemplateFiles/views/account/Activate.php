<?php namespace ProcessWire;
if($user->wm_activation_code === "0" || $settings->wm_user_activate) {
	$session->redirect($config->urls->httpRoot . "user/profile/");
}

function resendActivationEmail($account) {
	$user = wire("user");
	$session = wire("session");
	$cache = wire("cache");
	$locks = wire("config")->paths->assets."locks/";
	$lockFile = "{$locks}rae-{$user->id}";
	if($account->sendActivationMail($user->email, $user->name, $user->activation_code)) {
		$session->set("activate_resend", "<div class='message success'>Activation email resent.</div>");
		$cache->save("rae-{$user->id}", time(), 600);
		$session->redirect($page->httpUrl."activate/");
	} else {
		wire("session")->set("activate_resend", "<div class='message error'>Resending activation email failed.</div>");
		$session->redirect($page->httpUrl."activate/");
	}
}

$message = ["class" => "", "text" => ""];

$form = "";
// get variables are set
if(isset($input->get->user) && isset($input->get->hash)) {
	if(!$account->activateUserAccount($input->get->user, $input->get->hash)) {
		$session->set("registration_message", "<div class='message error'>Something went wrong!</div>");
		$session->redirect($config->urls->httpRoot . "user/login/");
	}
	$session->set("registration_message", "<div class='message success'>Your account was activated!</div>");
	$session->redirect($config->urls->httpRoot . "user/login/");
} else { // get variables are not set
	if($user->isLoggedin()) {
		if($input->post->submit_resend) {
			$locks    = "{$config->paths->assets}locks/";
			$lockFile = "{$locks}rae-{$user->id}";
			if($cache->get("rae-{$user->id}")) {
				$lockTime = $cache->get("rae-{$user->id}");
				$unlockTime = $lockTime + 600;
				$timeToUnlock = $unlockTime - time();
				$session->set("activate_resend", "<div class='message error'>You have to wait {$timeToUnlock} seconds before requesting another email.</div>");
				if($unlockTime < time()) {
					resendActivationEmail($account);
				}
				$session->redirect($page->httpUrl."activate/");
			} else {
				resendActivationEmail($account);
			}
		}
		if($input->post->submit_delete) {
			$u = $users->get($user->name);
			$session->logout();
			$users->delete($u);
		}
		// show resend email form with optional email change
		echo "<section class='uk-width-xxlarge uk-text-center uk-margin-auto'>";
			echo $session->get("activate_resend");
			echo "<div class='uk-margin'>Your account is not active. <br>Check your email for the activation email or resend it using the button below.<br><br> Your email is {$user->email}</div>";
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
	$session->remove("activate_resend");
}