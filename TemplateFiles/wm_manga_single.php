<?php namespace ProcessWire;

// if displaying of adult content was accepted, set a session variable
// that prevents the adult content warning from appearing again during a session
if($input->post->showAdult) {
	$session->set("adult", true);
	$session->redirect($page->httpUrl);
}

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
$comments = getComments($page);
$commentsHtml = $commentForm . $comments;

// variables sent to the view file
$vars = array(
    "chapters"    => $chapters,
    "authors"     => getTerms($page->wm_authors, ",", ""),
    "artists"     => getTerms($page->wm_artists, ",", ""),
    "genres"      => getTerms($page->wm_genres , ",", ""),
    "type"        => getTerms($page->wm_type  , ",", ""),
    "mangaStatus" => getTerms($page->wm_manga_status, ",", ""),
    "scanlation"  => getTerms($page->wm_scan_status , ",", ""),
    "alt_titles"  => $page->wm_alt_titles,
    "description" => $page->wm_description,
    "fredi"       => $fredi,
	"comments"    => $commentsHtml
);

// if manga is marked as adult
// and user didn't enable the display of adult content
// show the adult content warning
if($page->wm_adult && !$session->get("adult") && !$user->wm_adult_warning_off) {
	$out  = "<div class='uk-flex'>";
		$out .= "<div class='uk-width-xxlarge uk-margin-auto uk-text-center uk-text-lead'>";
		$out .= "This manga contains adult content.<br>";
		$out .= "Clicking the below button will remove this warning and remember your choice for the duration of your session. ";
		$out .= "<form method='post'>";
		$out .= "<input type='submit' name='showAdult' value='OK' class='uk-input uk-margin-top uk-button uk-button-danger'>";
		$out .= "</form>";
		$out .= "</div>";
	$out .= "</div>";
	$content = $out;
}
else {
	$content = $files->render(__DIR__ . "/views/MangaSingle.php", $vars);
}
include("_main.php");

// increase views by one if it is a new session
if($session->get('viewed_'.$page->id) !== 1) {
	// prevent clearing of chapter list cache
	// by setting a session variable
	$session->set('dontRefreshCache_'.$page->id, 1);

	// increment views and save page
	$page->of(false);
	$page->views++;
	$page->save("views");

	// clear session variable so cache can be cleared
	$session->set('dontRefreshCache_'.$page->id, 0);
	// set page as viewed so it doesn't increment again this session
	$session->set('viewed_'.$page->id, 1);
}
