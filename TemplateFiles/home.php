<?php namespace ProcessWire;


$bodyClass .= " manga-home";

$vars = array(
    "chapters" => $pages->find("template=wm_chapter, sort=-created, limit=12 {$hideAdultChapters}"),
    "hideAdultManga" => $hideAdultManga
);

/*------------------------------------------------------------------------------
 	$files->render(string $filename, array $vars = [], array $options = []);
	Returns the output of $filename
	and sends an associative array of variables to $filename

	https://processwire.com/api/ref/files/render/
------------------------------------------------------------------------------*/
$content = $files->render(__DIR__ . "/views/Home.php", $vars);

include("_main.php");