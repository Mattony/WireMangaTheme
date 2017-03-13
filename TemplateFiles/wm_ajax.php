<?php namespace ProcessWire;

if(!$config->ajax) {
	$session->redirect($config->urls->root);
}

/*
 *------------------------------------------------------------------------------
 *	Search
 *------------------------------------------------------------------------------
 */
if($config->ajax && $input->post->action == 'ajaxSearch') {
	$keywords = $sanitizer->selectorValue($input->post->keywords);
	$results = $pages->find("template=wm_manga_single, name%={$keywords}, $hideAdult");
	$out = "";
	foreach($results as $r) {
		$out .= "<li class='header-search--item'><a href='{$r->url}'>{$r->title}</a></li>";
	}
	$out .= "<li class='header-search--item header-search--close'><a href='' uk-icon='icon: close'></a></li>";
	return json_encode(["success" => true, "message" => "", "html" => $out]);
}


/*
 *------------------------------------------------------------------------------
 *	Get comments
 *------------------------------------------------------------------------------
 */
if($config->ajax && $input->post->action == 'showComments') {
	$id = (int) $this->input->post->pageID;
	$p = $pages->get("template=wm_manga_single, id={$id}");
	$commentForm = getCommentsForm($p, $p->wm_comments);
	$comments = getComments($p);
	$commentsHtml = $commentForm . $comments;
	$result = [
		"success" => true,
		"html" => $commentsHtml
	];
	return json_encode($result);
}

/*
 *------------------------------------------------------------------------------
 *	Get chapters
 *------------------------------------------------------------------------------
 */
if($config->ajax && $input->post->action == 'showChapters') {
	$id = (int) $this->input->post->pageID;
	$p = $pages->get("template=wm_manga_single, id={$id}");
	$chapters = $cache->get("chapters:".$p->id, function() use($p){
		return chapterListForCache($p);
	});
	$result = [
		"success" => true,
		"html" => $chapters
	];
	return json_encode($result);
}

/*------------------------------------------------------------------------------
 # Get manga info for manga directory
------------------------------------------------------------------------------*/
if($this->input->post->action == "showInfo") {
	$id = (int) $this->input->post->pageID;
	$p = $pages->get("template=wm_manga_single, id={$id}");
	$author = getTerms($p->wm_authors, ", ");
	$genre  = getTerms($p->wm_genres, "", "uk-label");
	$desc   = substr(strip_tags($p->wm_description), 0, 500) . "...";

	// Build html output
	$out  = "<div class='directory--left uk-width-1-4@m uk-text-center'>";
	$out .= "<a href='{$p->url}'><img src='{$p->wm_cover->first()->size(250,0)->url}'></a>";
	$out .= "</div>";

	$out .= "<div class='directory--right uk-width-3-4@m'>";
	$out .= "<h3 class='directory--manga-author'> by {$author}</h3>";
	$out .= "<div class='directory--manga-description'>{$desc}</div>";
	$out .= "<div class='directory--manga-genre'>{$genre}</div>";
	$out .= "</div>";
	return json_encode($out);
}