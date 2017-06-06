<?php namespace ProcessWire;

$bodyClass .= " manga-directory";

// all alphabet letter to check the query variable
// and build the letter navigation
$aToZ = ["#", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
$q = strtolower($input->get->q);
// if query variable is set and in the $aToZ array
// get manga starting with that query variable
$q = $sanitizer->selectorValue($q);
if($q && array_search($q, $aToZ) !== false) {
	$results = $pages->find("template=wm_manga_single, name^={$q}, sort=title, limit=100, $hideAdultManga");
	$noResults = ($results->count) ? "" : "<div class='directory--no-results'>No results!</div>";
} else if($q && $q == "#") {
	$numbers = "0|1|2|3|4|5|6|7|8|9";
	$results = $pages->find("template=wm_manga_single, name^={$numbers}, sort=title, limit=100, $hideAdultManga");
	$noResults = ($results->count) ? "" : "<div class='directory--no-results'>No results!</div>";
} else {
	$results = $pages->find("template=wm_manga_single, sort=created, limit=100, $hideAdultManga");
	$noResults = ($results->count) ? "" : "<div class='directory--no-results'>No results!</div>";
}

$vars = array(
	'aToZ' => $aToZ,
	'results' => $results,
	'noResults' => $noResults,
	'pagination' => $results->renderPager($paginationOptions),
);

/*------------------------------------------------------------------------------
 	$files->render(string $filename, array $vars = [], array $options = []);
	Returns the output of $filename
	and sends an associative array of variables to $filename

	https://processwire.com/api/ref/files/render/
------------------------------------------------------------------------------*/
$content = $files->render(__DIR__ . '/views/Manga.php', $vars);

include("_main.php");