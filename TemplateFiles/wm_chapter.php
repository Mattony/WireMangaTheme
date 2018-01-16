<?php namespace ProcessWire;
// save and return reader settings
$readerSettings = readerSettings();
$prev_page = null;
$next_page = null;
$pagesList = null;

if(!$readerSettings["show_all_ch_images"]) {
	$prev_page = "<a href='{$reader->prevPage()}' title='Previous Page'><i class='fa fa-chevron-left' aria-hidden='true'></i></a>";
	$next_page = "<a href='{$reader->nextPage()}' title='Next Page'><i class='fa fa-chevron-right' aria-hidden='true'></i></a>";
	$pagesList = $reader->pagesList();
}

$options = ["One image per page", "All chapter images on one page"];

$footerAssets .= "<script src='{$config->urls->templates}assets/js/manga-reader.js'></script>\n";
if($readerSettings["show_all_ch_images"]) {
	$footerAssets .= "<script src='{$config->urls->templates}assets/js/lazysizes.min.js' async=''></script>\n";
}
$bodyClass .= " manga-reader";

$max_img_width = $user->isLoggedin() ? $user->wm_max_img_width : $session->get("max_img_width");
$vars = array(
	"show_all_ch_images" => $readerSettings["show_all_ch_images"],
	"width"     => $readerSettings["width"],
	"prev_page" => $prev_page,
	"next_page" => $next_page,
	"pagesList" => $pagesList,
	"options"   => $options,
);
$content = adultNotice($page, $page->parent);
if(!$content) {
	$content = $files->render(__DIR__ . '/views/Reader.php', $vars);
}

include("_main.php");
