<?php namespace ProcessWire;
$bodyClass .= " manga-search";
$keyword = $sanitizer->selectorValue($input->get->s);
$results = $pages->find("template=wm_manga_single, title%={$keyword}, $hideAdultManga");
$vars = array(
    "results" => $results,
);
$content = $files->render(__DIR__ . '/views/Search.php', $vars);

include("_main.php");