<?php namespace ProcessWire;


$bodyClass .= " manga-home";

$vars = array(
    "chapters" => $pages->find("template=wm_chapter, sort=-created, limit=12 {$hideAdultChapters}"),
    "hideAdultManga" => $hideAdultManga
);
$content = $files->render(__DIR__ . "/views/Home.php", $vars);

include("_main.php");