<?php namespace ProcessWire;
if($page->name !== "manga-directory") {
	$session->redirect("{$config->urls->httpRoot}manga-directory/");
}
$bodyClass .= " manga-directory";

// all alphabet letter to check the query variable
// and build the letter navigation
$aToZ = ["#", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
$q = strtolower($input->urlSegment1);
$limit = 100;
// if query variable is set and in the $aToZ array
// get manga starting with that query variable
$q = $sanitizer->selectorValue($q);
if($q && array_search($q, $aToZ) !== false) {
	$results = $pages->find("template=wm_manga_single, title^={$q}, sort=title, limit={$limit}, $hideAdultManga");
	$noResults = ($results->count) ? "" : "<div class='directory--no-results'>No results!</div>";
} else if($q && $q == "number") {
	$numbers = "0|1|2|3|4|5|6|7|8|9";
	$results = $pages->find("template=wm_manga_single, name^={$numbers}, sort=title, limit={$limit}, $hideAdultManga");
	$noResults = ($results->count) ? "" : "<div class='directory--no-results'>No results!</div>";
} else {
	$results = $pages->find("template=wm_manga_single, sort=created, limit={$limit}, $hideAdultManga");
	$noResults = ($results->count) ? "" : "<div class='directory--no-results'>No results!</div>";
}

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

$vars = array(
	'aToZ' => $aToZ,
	'results' => $results,
	'noResults' => $noResults,
	'pagination' => $results->renderPager($paginationOptions),
);

$content = $files->render(__DIR__ . '/views/Manga.php', $vars);

include("_main.php");