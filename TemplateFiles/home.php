<?php namespace ProcessWire;

$bodyClass .= " manga-home";

$vars = array(
    "chapters" => $wm->latestChapters(12, $hideAdultChapters),
    "hideAdultManga" => $hideAdultManga
);
$content = $files->render(__DIR__ . "/views/Home.php", $vars);

include("_main.php");