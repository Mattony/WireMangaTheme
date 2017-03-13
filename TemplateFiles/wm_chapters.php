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

// redirect to first image in chapter
// if it is accessed directly
if(!$input->urlSegment1) {
	$session->redirect($page->url . "1/");
}
$content = $files->render(__DIR__ . '/views/Reader.php');

include("_main.php");