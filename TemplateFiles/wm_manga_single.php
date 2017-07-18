<?php namespace ProcessWire;

$bodyClass .= " manga-single";

// get the chached chapter list
// cache is cleared only when the page is saved
// and rebuilt immediately after to prevent
// long loading times on manga with many chapters
//
// if the user is isSuperuser and the GET variable clearChapterList
// is set to "yes" the cache for the current manga will be cleared
// http://domain.tld/manga-name/?clearChapterList=yes
if($user->isSuperuser() && $input->get->clearChapterList == "yes") {
	$cache->delete("chapters:" . $page->id);
}
$chapters = $cache->get("chapters:" . $page->id, $cache::expireNever, function() use($page, $wmt){
	return $wmt->chapterListMarkup($page);
});

if($fredi = $modules->get("Fredi")) {
	$headerAssets .= $fredi->renderScript();
}

$headerAssets .= "<script type='text/javascript' src='{$config->urls->templates}assets/js/comments.js'></script>";

$commentForm = getCommentsForm($page, $page->wm_comments);
$comments = $commentForm . getComments($page);

// variables sent to the view file
$vars = array(
	"chapters"    => $chapters,
	"authors"     => getTerms($page->wm_author, ",", ""),
	"artists"     => getTerms($page->wm_artist, ",", ""),
	"genres"      => getTerms($page->wm_genre, ",", ""),
	"type"        => getTerms($page->wm_type, ",", ""),
	"mangaStatus" => getTerms($page->wm_manga_status, ",", ""),
	"scanlation"  => getTerms($page->wm_scan_status , ",", ""),
	"alt_titles"  => $page->wm_alt_titles,
	"description" => $page->wm_description,
	"fredi"       => $fredi,
	"chaptersIsActive" => "loaded active",
	"commentsIsActive" => "",
	"comments" => ""
	
);

if($input->get->show === "comments") {
	$vars["comments"] = $comments;
	$vars["chapters"] = "";
	$vars["chaptersIsActive"] = "";
	$vars["commentsIsActive"] = "loaded active";
}

if($input->post->manga_subscribe && $user->isLoggedin()) {
	subscribeUserToManga($page, $user);
}
if($input->post->manga_unsubscribe && $user->isLoggedin()) {
	unsubscribeUserFromManga($page, $user);
}
// if manga is marked as adult
// and user didn't enable the display of adult content
// show the adult content warning
$content = adultNotice($page, $page);
if(!$content) {
	$content = $files->render(__DIR__ . "/views/MangaSingle.php", $vars);
	viewCounter();
}

include("_main.php");

