<?php namespace ProcessWire;

if(!$config->ajax) {
	$session->redirect($config->urls->root);
}

/**
 * Search
 */
if($input->post->action === 'ajaxSearch') {
	$keywords = $sanitizer->selectorValue($input->post->keywords);
	$results = $pages->find("template=wm_manga_single, name%={$keywords}, $hideAdultManga");
	$out = "";
	$out .= "<li class='header-search-item header-search-close'><a href='' ><i class='fa fa-times' aria-hidden='true'></i> Close</a></li>";
	foreach($results as $r) {
		$out .= "<li class='header-search-item'><a href='{$r->url}'>{$r->title}</a></li>";
	}
	echo json_encode(["success" => true, "message" => "", "html" => $out]);
}


/**
 * Get comments
 */
if($input->post->action === 'showComments') {
	$id = (int) $input->post->pageID;
	$p = $pages->get("template=wm_manga_single, id={$id}");
	$commentForm = getCommentsForm($p, $p->wm_comments);
	$comments = getComments($p);
	$commentsHtml = $commentForm . $comments;
	$result = [
		"success" => true,
		"html" => $commentsHtml
	];
	echo json_encode($result);
}

/**
 * Get chapters
 */
if($input->post->action === 'showChapters') {
	$id = (int) $input->post->pageID;
	$p = $pages->get("template=wm_manga_single, id={$id}");
	$chapters = $cache->get("chapters:" . $p->id, $cache::expireNever, function() use($p, $wmt){
		return $wmt->chapterListMarkup($p);
	});
	$result = [
		"success" => true,
		"html" => $chapters
	];
	echo json_encode($result);
}

/**
 * Get manga info for manga directory
 */
if($input->post->action === "showInfo") {
	$id = (int) $input->post->pageID;
	$p = $pages->get("template=wm_manga_single, id={$id}");
	$author = getTerms($p->wm_author, ", ");
	$genre  = getTerms($p->wm_genre, "", "uk-label");
	$desc   = substr(strip_tags($p->wm_description), 0, 500) . "...";

	// Build html output
	$out  = "<div class='dir-left uk-width-1-4@m uk-text-center'>";
	$out .= "<a href='{$p->url}'><img src='{$p->wm_cover->first()->size(250,250)->url}'></a>";
	$out .= "</div>";

	$out .= "<div class='dir-right uk-width-3-4@m'>";
	$out .= "<h3 class='dir-manga-author'> by {$author}</h3>";
	$out .= "<div class='dir-manga-description'>{$desc}</div>";
	$out .= "<div class='dir-manga-genre'>{$genre}</div>";
	$out .= "</div>";
	echo json_encode($out);
}


/**
 * (Un)Subscribe users to manga
 */
if($input->post->action === "subscribe") {
	$id = (int) $input->post->pageID;
	if($user->isLoggedin()) {
		$out = subscribeUserToManga($pages->get($id), $user);
	}
	echo json_encode($out);
}

if($input->post->action === "unsubscribe") {
	$id = (int) $input->post->pageID;
	if($user->isLoggedin()) {
		$out = unsubscribeUserFromManga($pages->get($id), $user);
	}
	echo json_encode($out);
}
