<?php namespace ProcessWire;
if(!$input->urlSegment1) {
	$session->redirect($page->url."profile");
}

elseif($input->urlSegment1 == "register") {
	$content = $files->render(__DIR__ . "/views/account/Register.php", ["account" => $account]);
}

elseif($input->urlSegment1 == "login") {
	$content = $files->render(__DIR__ . "/views/account/Login.php", ["account" => $account]);
}

elseif($input->urlSegment1 == "logout") {
	$content = $files->render(__DIR__ . "/views/account/Logout.php", ["account" => $account]);
}

elseif($input->urlSegment1 == "profile") {
	$content = $files->render(__DIR__ . "/views/account/Profile.php", ["account" => $account]);
}

elseif($input->urlSegment1 == "edit-profile") {
	$content = $files->render(__DIR__ . "/views/account/EditProfile.php", ["account" => $account]);
	$footerAssets .= "<script src='{$config->urls->templates}_defaults/assets/js/cropper.min.js'></script>";
	$footerAssets .= "<script src='{$config->urls->templates}_defaults/assets/js/cropper-setup.js'></script>";
	$headerAssets .= "<link rel='stylesheet' href='{$config->urls->templates}_defaults/assets/css/cropper.min.css' type='text/css'>";
}

elseif($input->urlSegment1 == "password-reset") {
	$content = $files->render(__DIR__ . "/views/account/PasswordReset.php", ["account" => $account]);
	$headerAssets .= "<link type='text/css' href='/wire/modules/Inputfield/InputfieldPassword/InputfieldPassword.css?v=101-1477228454' rel='stylesheet'>";
	$footerAssets .= "<script type='text/javascript' src='/wire/modules/Inputfield/InputfieldPassword/complexify/jquery.complexify.min.js'></script>";
	$footerAssets .= "<script type='text/javascript' src='/wire/modules/Inputfield/InputfieldPassword/complexify/jquery.complexify.banlist.js'></script>";
	$footerAssets .= "<script type='text/javascript' src='/wire/modules/Jquery/JqueryCore/xregexp.js?v=1477228454'></script>";
	$footerAssets .= "<script type='text/javascript' src='/wire/modules/Inputfield/InputfieldPassword/InputfieldPassword.js?v=101-1477228454'></script>";
}
elseif($input->urlSegment1 == "activate") {
	$content = $files->render(__DIR__ . "/views/account/Activate.php", ["account" => $account]);
}

include("_main.php");
