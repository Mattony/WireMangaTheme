<?php namespace ProcessWire;
// change images display mode 
$valid_values = [0, 1];
if($input->post->submit_rs && in_array($input->post->display_mode, $valid_values)) {
	if($user->isLoggedin()) {
		$user->of(false);
		$user->wm_all_images = $input->post->display_mode;
		$user->save();
	}
	else {
		$session->set("display_mode", $input->post->display_mode);
	}
}
// determine images display mode
if($user->isLoggedin()) {
	$show_all_chapter_images = $user->wm_all_images;
}
if(!$user->isLoggedin()) {
	$show_all_chapter_images = $session->get("display_mode");
}

// get javascript configuration
$oldConfigJS = "<script> var config = {$jsVars}; </script>";
// add new javascript options
$config->js("parentURL", $page->parent->httpUrl);
$config->js("currentChapter", $page->name);
$jsVars = json_encode($config->js());
// create new javascript configuration
$newConfigJS = "<script> var config = {$jsVars}; </script>";

$bodyClass .= " manga-reader";
$footerAssets .= "<script src='{$config->urls->templates}assets/js/manga-reader.js'></script>";
//over write old js config with new one
$headerAssets = str_replace($oldConfigJS, $newConfigJS, $headerAssets);

// redirect to a page displaying single image 
// or all chapter images on the same page
if(!$input->urlSegment1 && $show_all_chapter_images == 0) {
	$session->redirect($page->url . "1/");
}
elseif($input->urlSegment1 && $show_all_chapter_images == 1) {
	$session->redirect($page->url);
}

$vars = array(
    "show_all_chapter_images"    => $show_all_chapter_images,
);
$content = $files->render(__DIR__ . '/views/Reader.php', $vars);

include("_main.php");