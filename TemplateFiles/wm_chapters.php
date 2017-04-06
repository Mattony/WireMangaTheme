<?php namespace ProcessWire;

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

// determine images display mode
if($settings->wm_all_images == 0) {
	$show_all_chapter_images = 0;
}
if($settings->wm_all_images == 1) {
	$show_all_chapter_images = 1;
}
if($page->parent->wm_images_mode->id == 1) {
	$show_all_chapter_images = 0;
}
if($page->parent->wm_images_mode->id == 2) {
	$show_all_chapter_images = 1;
}

// redirect to a page displaying single image 
// or all chapter images on the same page
if(!$input->urlSegment1 && $show_all_chapter_images == 0) {
	$session->redirect($page->url . "1/");
}
elseif($input->urlSegment1 && $show_all_chapter_images == 1) {
	$session->redirect($page->url);
}
// load file for the chosen display mode
if($show_all_chapter_images == 1) {
	$content = $files->render(__DIR__ . '/views/ReaderAll.php');
}
else {
	$content = $files->render(__DIR__ . '/views/Reader.php');
}

include("_main.php");