<?php namespace ProcessWire;
if($page->name == "user" && !$input->urlSegment1) {
	$session->redirect($page->url."profile");
}

/*-----------------------------------------------------------*/
/*  Registration page                                        */
/*-----------------------------------------------------------*/
elseif($input->urlSegment1 === "register") {
	$bodyClass .= " register";
	$content = $files->render(__DIR__ . "/views/account/Register.php", ["account" => $account]);
}

/*-----------------------------------------------------------*/
/*  Login page                                               */
/*-----------------------------------------------------------*/
elseif($input->urlSegment1 === "login") {
	$bodyClass .= " login";
	$reg_mess = $session->get("registration_message");
	$session->remove("registration_message");
	$vars = [
		"account" => $account,
		"registration_message" => $reg_mess,
		"login_error" => null
	];
	
	//process login form
	if($input->post->submit ) {
		if( $this->wire("input")->post->user && $this->wire("input")->post->pass) {
			$username = $this->wire("sanitizer")->pageName($this->wire("input")->post->user);
			$pass = $this->wire("input")->post->pass;
			$u = $this->wire("users")->get($username);
			try {
				$this->wire("session")->login($username, $pass);
			} catch (WireException $e) {
				$vars["login_error"] = "<div class='message error'>Too many failed login attempts.<br>" . $e->getMessage() . "</div>";
			}
		}
	}
	$content = $files->render(__DIR__ . "/views/account/Login.php", $vars);
}

/*-----------------------------------------------------------*/
/*  Logout page                                              */
/*-----------------------------------------------------------*/
elseif($input->urlSegment1 === "logout") {
	$session->logout();
	$session->redirect($config->urls->httpRoot);
}

/*-----------------------------------------------------------*/
/*  Profile page                                             */
/*-----------------------------------------------------------*/
elseif($input->urlSegment1 === "profile") {
	if($input->post->manga_unsubscribe && $user->isLoggedin()) {
		unsubscribeUserFromManga($pages->get($input->post->manga_id), $user);
	}
	if(!$user->isLoggedin()) {
		$session->redirect($config->urls->httpRoot . "user/login/");
	}
	if($user->wm_activation_code && $settings->wm_user_activate && !$user->isSuperuser()) {
		$session->redirect($config->urls->httpRoot . "user/activate/");
	}
	$bodyClass .= " profile";
	$content = $files->render(__DIR__ . "/views/account/Profile.php", ["account" => $account]);
}

/*-----------------------------------------------------------*/
/*  Edit Profile page                                        */
/*-----------------------------------------------------------*/
elseif($input->urlSegment1 === "edit-profile") {
	// redirect to login page if user is not logged in
	if(!$user->isLoggedin()) {
		$session->redirect($config->urls->httpRoot . "user/login/");
	}
	if($user->wm_activation_code && $settings->wm_user_activate && !$user->isSuperuser()) {
		$session->redirect($config->urls->httpRoot . "user/activate/");
	}
	if($input->post->submit) {
		$edit = $account->editProfile();
		$session->redirect($page->url . "edit-profile");
	}
	if($session->get("edit_succes")) {
		$session_message = "<div class='message success'>{$session->get("edit_succes")}</div>";
		$session->remove("edit_succes");
	}
	if($session->get("edit_error")) {
		$session_message = "<div class='message error'>{$session->get("edit_error")}</div>";
		$session->remove("edit_error");
	}
	if($session->get("email_changed")) {
		$session_message = "<div class='message success'>{$session->get("email_changed")}</div>";
		$session->remove("email_changed");
	}
	
	$bodyClass    .= " edit-profile";
	$headerAssets .= "<link rel='stylesheet' href='{$config->urls->templates}assets/css/cropper.min.css' type='text/css'>";
	$footerAssets .= "<script src='{$config->urls->templates}assets/js/cropper.min.js'></script>";
	$footerAssets .= "<script src='{$config->urls->templates}assets/js/cropper-setup.js'></script>";

	$passRules = $fields->get("pass");
	$passLength = $passRules->minlength ? $passRules->minlength : 6;
	$passReq = str_replace("other", "special character", implode(", ", $passRules->requirements));
	$profileImage = $user->wm_profile_image->first() ? "<img src='{$user->wm_profile_image->first()->size(190, 190)->url}' id='current-profile-image'>" : "";
	$vars = [
		"account"      => $account,
		"passLength"   => $passLength,
		"passReq"      => $passReq,
		"profileImage" => $profileImage,
		"session_message" => $session_message,
	];
	$content = $files->render(__DIR__ . "/views/account/EditProfile.php", $vars);
}

/*-----------------------------------------------------------*/
/*  Account Activation page                                  */
/*-----------------------------------------------------------*/
elseif($input->urlSegment1 === "activate") {
	$bodyClass .= " activate";
	$content = $files->render(__DIR__ . "/views/account/Activate.php", ["account" => $account]);
}

/*-----------------------------------------------------------*/
/*  Email Confirmation page                                  */
/*-----------------------------------------------------------*/
elseif($input->urlSegment1 === "confirm") {
	$content = $files->render(__DIR__ . "/views/account/Confirm.php", ["account" => $account]);
}

/*-----------------------------------------------------------*/
/*  Password Reset page                                      */
/*-----------------------------------------------------------*/
elseif($input->urlSegment1 === "password-reset") {
	$bodyClass .= " password-reset";
	$url = $config->urls->root;
	$headerAssets .= "<link type='text/css' href='{$url}wire/modules/Inputfield/InputfieldPassword/InputfieldPassword.css' rel='stylesheet'>";
	$footerAssets .= "<script type='text/javascript' src='{$url}wire/modules/Inputfield/InputfieldPassword/complexify/jquery.complexify.min.js'></script>";
	$footerAssets .= "<script type='text/javascript' src='{$url}wire/modules/Inputfield/InputfieldPassword/complexify/jquery.complexify.banlist.js'></script>";
	$footerAssets .= "<script type='text/javascript' src='{$url}wire/modules/Jquery/JqueryCore/xregexp.js'></script>";
	$footerAssets .= "<script type='text/javascript' src='{$url}wire/modules/Inputfield/InputfieldPassword/InputfieldPassword.js'></script>";
	$content = $files->render(__DIR__ . "/views/account/PasswordReset.php", ["account" => $account]);
}


include("_main.php");
