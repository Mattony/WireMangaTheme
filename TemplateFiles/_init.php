<?php namespace ProcessWire;

include(__DIR__ . "/_func.php");
include(__DIR__ . "/classes/account.php");
$account = new Account();
$test = "test";
// added to selectors to hide adult manga
$hideAdultChapters = $user->wm_hide_adult ? "parent.wm_adult!=1" : "";
$hideAdultManga = $user->wm_hide_adult ? "wm_adult!=1" : "";

/**
 * SEO
 */
$seoDesc = $page->seo_description ? $page->seo_description : $pages->get('/')->seo_description;
$seoTitle = $page->wm_seo_title 
		? "{$page->wm_seo_title} {$settings->wm_site_title_sep} {$settings->wm_site_name}" 
		: "{$page->title} {$settings->wm_site_title_sep} {$settings->wm_site_name}";
$relPrev = "";
$relNext = "";

/**
 * Assets
 */
// make php variables available to javascript inside the config object
$config->js("ajaxUrl", $pages->get("/ajax//")->httpUrl);
$config->js("pageID", $page->id);
if($page->template->name === "wm_chapter") {
	$config->js("parentURL", $page->parent->httpUrl);
	$config->js("currentChapter", $page->name);
}
$jsVars = json_encode($config->js());

// assets added inside the head element
$headerAssets = "
<link rel='stylesheet' href='{$config->urls->templates}assets/css/font-awesome.min.css' />
<link rel='stylesheet' href='{$config->urls->templates}assets/css/uikit.min.css' />
<link rel='stylesheet' href='{$config->urls->templates}assets/css/style.css' />
<script src='{$config->urls->templates}assets/js/jquery-3.2.1.min.js'></script>
<script> var config = {$jsVars}; </script>";

// assets added before the closing body tag
$footerAssets = "
<script src='{$config->urls->templates}assets/js/uikit.min.js'></script>
<script src='{$config->urls->templates}assets/js/wm.js'></script>\n";

/**
 * Body Classes
 */
$adminClass = $user->isSuperuser() ? "admin" : null;
$loggedin = $user->isLoggedin() ? "logged-in" : "logged-out";
$bodyClass = "{$loggedin} page-{$page->id} {$adminClass}";

/**
 * Header
 */
$logo = null;
if( $settings->wm_logo->first() ) {
	$logo = "<a class='uk-navbar-item uk-logo' href='{$config->urls->httpRoot}'><img src='{$settings->wm_logo->first()->size(250, 80)->url}'></a>";
}
$menu = menuBuilder();

/**
 * Footer
 */
$footer = "<div class='footer--inner'></div>";


/**
 * Pagination Options
 */
$paginationOptions = [
	"nextItemLabel"     => "Next",
	"previousItemLabel" => "Prev",
	"listMarkup"        => "<nav class='uk-pagination'>{out}</nav>",
	"itemMarkup"        => "<div class='{class}'>{out}</div>",
	"linkMarkup"        => "<a href='{url}' class=''>{out}</a>",
	"currentItemClass"  => "uk-active",
	"numPageLinks"      => 6,
	"separatorItemClass" => "sep"
];

if( file_exists(__DIR__ . "/_user-settings.php")) {
	include_once(__DIR__ . "/_user-settings.php");
}