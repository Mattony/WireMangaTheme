<?php namespace ProcessWire;

$bodyClass .= " manga-home";
$ch_limit = $settings->wm_chapters_limit ? $settings->wm_chapters_limit : 12;
$lm_limit = $settings->wm_lmanga_limit   ? $settings->wm_lmanga_limit   :  5;
$pm_limit = $settings->wm_pmanga_limit   ? $settings->wm_pmanga_limit   :  5;

$vars = array(
    "hideAdultManga" => $hideAdultManga,
	"ch_limit" => $ch_limit,
	"lm_limit" => $lm_limit,
	"pm_limit" => $pm_limit,
);

/*------------------------------------------------------------------------------
 	$files->render(string $filename, array $vars = [], array $options = []);
	Returns the output of $filename
	and sends an associative array of variables to $filename

	https://processwire.com/api/ref/files/render/
------------------------------------------------------------------------------*/
$content = $files->render(__DIR__ . "/views/Home.php", $vars);

include("_main.php");