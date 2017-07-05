<?php namespace ProcessWire;

$bodyClass .= " manga-archive";

if($page->parent->name === "author") {
	$field = "wm_author";
	$taxonomyTitle = "Author";
}
elseif($page->parent->name === "artist") {
	$field = "wm_artist";
	$taxonomyTitle = "Artist";
}
elseif($page->parent->name === "genre") {
	$field = "wm_genre";
	$taxonomyTitle = "Genre";
}
elseif($page->parent->name === "type") {
	$field = "wm_type";
	$taxonomyTitle = "Type";
}
elseif($page->parent->name === "manga-status") {
	$field = "wm_manga_status";
	$taxonomyTitle = "Manga Status";
}
elseif($page->parent->name === "scan-status") {
	$field = "wm_scan_status";
	$taxonomyTitle = "Scanlation Status";
}



$results = $pages->find("template=wm_manga_single, limit=24, {$field}={$page->name}, sort=-published, sort=name");

if($input->pageNum){
	$limit = 24;
	if ($input->pageNum > 1) {
		$relPrev = "<link rel='prev' href='{$page->httpUrl}{$config->pageNumUrlPrefix}".($input->pageNum-1)."' />\n";
	}
	if ($input->pageNum * $limit < $results->getTotal()) {
		$relNext = "<link rel='next' href='{$page->httpUrl}{$config->pageNumUrlPrefix}".($input->pageNum+1)."' />";
	}
}

$vars = array(
	"page" => $page,
	"results" => $results,
	"pagination" => $results->renderPager($paginationOptions),
	"taxonomyTitle" =>$taxonomyTitle,
);

$content = $files->render(__DIR__ . "/views/Terms.php", $vars);

include("_main.php");