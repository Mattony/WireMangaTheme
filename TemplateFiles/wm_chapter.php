<?php namespace ProcessWire;
// save and return reader settings
$readerSettings = readerSettings();

$footerAssets .= "<script src='{$config->urls->templates}assets/js/manga-reader.js'></script>\n";
if($readerSettings["show_all_ch_images"]) {
	$footerAssets .= "<script src='{$config->urls->templates}assets/js/lazysizes.min.js' async=''></script>\n";
}
$bodyClass .= " manga-reader";

$max_img_width = $user->isLoggedin() ? $user->wm_max_img_width : $session->get("max_img_width");
$vars = array(
    "show_all_ch_images" => $readerSettings["show_all_ch_images"],
	"width" => $readerSettings["width"]
);
$content = adultNotice($page, $page->parent);
if(!$content) {
	$content = $files->render(__DIR__ . '/views/Reader.php', $vars);
}

include("_main.php");