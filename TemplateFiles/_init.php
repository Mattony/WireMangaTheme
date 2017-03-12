<?php namespace ProcessWire;

include(__DIR__ . "/_func.php");
include(__DIR__ . "/classes/account.php");
$account = new Account();
// added to selectors to hide adult manga
$hideAdult = $user->wm_hide_adult ? "wm_adult!=1" : "";

/*------------------------------------------------------------------------------
	# SEO
------------------------------------------------------------------------------*/
$seoDesc = ($page->seo_description) ? $page->seo_description : $pages->get('/')->seo_description;
$seoTitle = $page->wm_seo_title 
		? "{$page->wm_seo_title} {$settings->wm_site_title_sep} {$settings->wm_site_name}" 
		: "{$page->title} {$settings->wm_site_title_sep} {$settings->wm_site_name}";
$relPrev = "";
$relNext = "";

/*------------------------------------------------------------------------------
	# Assets
------------------------------------------------------------------------------*/
// make php variables available to javascript inside the config object
// config.ajaxUrl - url for ajax call_user_func
// config.pageID - id of the current page
$config->js("ajaxUrl", $pages->get("/ajax//")->httpUrl);
$config->js("pageID", $page->id);
$jsVars = json_encode($config->js());

// assets added inside the head element
$headerAssets = "
<link rel='stylesheet' href='{$config->urls->templates}assets/css/font-awesome.min.css' />
<link rel='stylesheet' href='{$config->urls->templates}assets/css/uikit.min.css' />
<link rel='stylesheet' href='{$config->urls->templates}assets/css/style.css' />

<script src='{$config->urls->templates}assets/js/jquery-3.1.1.min.js'></script>
<script> var config = {$jsVars}; </script>";

// assets added before the closing body tag
$footerAssets = "
<script src='{$config->urls->templates}assets/js/theia-sticky-sidebar.min.js'></script>
<script src='{$config->urls->templates}assets/js/uikit.min.js'></script>
<script src='{$config->urls->templates}assets/js/uikit-icons.min.js'></script>
<script src='{$config->urls->templates}assets/js/headroom.min.js'></script>
<script src='{$config->urls->templates}assets/js/wm.js'></script>";

/*------------------------------------------------------------------------------
	# Body Classes
------------------------------------------------------------------------------*/
$adminClass = "admin";
$loggedin = $user->isLoggedin() ? "logged-in" : "logged-out";
$bodyClass = "{$loggedin} page-{$page->id} {$adminClass}";


/*------------------------------------------------------------------------------
	# Menu
------------------------------------------------------------------------------*/
$classes = [
	"menuClass" => "header--menu uk-navbar-nav",
	"subMenuWrapperClass" => "uk-navbar-dropdown",
	"subMenuClass" => "header--sub-menu uk-nav uk-navbar-dropdown-nav",
	"menuItem" => "header--menu-item",
];
$largeScreenMenu = menuBuilder($classes);

$classes = [
	"menuClass" => "header--menu small-screen uk-navbar-nav",
	"subMenuWrapperClass" => "header--sub-menu-warpper",
	"subMenuClass" => "header--sub-menu",
	"menuItem" => "header--menu-item",
];
$smallScreenMenu = menuBuilder($classes);

/*------------------------------------------------------------------------------
	# Sidebar
------------------------------------------------------------------------------*/
$hasSidebar = false;
// no sidebar for following templates
if($page->template->name == "home" || $page->template->name == "terms") {
	$bodyClass .= " has-sidebar"; // add a 'has-sidebar' class to the body element
	$hasSidebar = true;
	$sidebar = include(__DIR__ . '/layout/_sidebar.php');
}
else {
	$sidebar = "";
}


/*------------------------------------------------------------------------------
	# Footer
------------------------------------------------------------------------------*/
$footer = "<div class='footer--inner'></div>";


/*------------------------------------------------------------------------------
	# Pagination Options
------------------------------------------------------------------------------*/
$paginationOptions = [
	"nextItemLabel"     => "Next",
	"previousItemLabel" => "Prev",
	"listMarkup"        => "<nav class='pagination'>{out}</nav>",
	"itemMarkup"        => "<div class='{class}'>{out}</div>",
	"linkMarkup"        => "<a href='{url}' class=''>{out}</a>",
	"currentItemClass"  => "current",
	"numPageLinks"      => 6,
	"separatorItemClass" => "sep"
];
