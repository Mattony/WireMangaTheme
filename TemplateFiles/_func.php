<?php  namespace ProcessWire;


/**
 * Get terms and display them
 *
 * @param PageArray $terms Field containing the terms ($page->genre)
 * @param string $sep Separator between the links
 * @param string $linkClass Class for the link
 *
 */
function getTerms($terms, $sep = "", $linkClass = "") {
	$out = "";
	$rootUrl = wire('config')->urls->httpRoot;
	$x = 1;
	foreach($terms->sort("name") as $term)
	{
		$termUrl = $rootUrl.$term->parent->name."/".$term->name;
		$out .= ($x != count($terms)) ? "<a href='{$termUrl}' class='{$linkClass}'>{$term->title}</a>{$sep} " : "<a href='{$termUrl}' class='{$linkClass}'>{$term->title}</a>";
		$x++;
	}
	return $out;
}

/**
 * Get the comment form
 *
 * @param CommentArray $comments
 *
 */
function getCommentsForm($page, $comments) {
	include(__DIR__ . "/classes/customCommentForm.php");
	$options = array(
		'headline' => "<h3>Add Comment</h3>",
		'successMessage' => "<p class='success'>Comment saved.</p>",
		'errorMessage' => "<p class='error'>There was an error saving your comment.</p>",
		'processInput' => true,
		'encoding' => 'UTF-8',
		'attrs' => array(
			'id' => 'CommentForm',
			'action' => './',
			'method' => 'post',
			'class' => '',
			'rows' => 5,
			'cols' => 50,
			),
		'labels' => array(
			'cite'   => 'Name',
			'email'  => 'E-Mail',
			'text'   => 'Comment',
			'submit' => 'Submit',
		),
	);
	$form = new customCommentForm($page, $comments, $options); 
	return $form->render();
}

/**
 * Get the comments for a page
 *
 * @param Page $page
 * @param int $parentID The id of the parent comment
 * @param boolean $reply Show the reply button or not
 * @param string $commentClass Class for the comment container
 *
 */
function getComments($page, $parentID = 0, $reply = true, $commentClass = "comment") {
	$comments = $page->wm_comments;
	$out = "";
	if( $parentID !== 0 ){
		$comments = $comments->sort("created");
	}
	foreach($comments as $comment)
	{
		if($comment->parent_id == $parentID)
		{
			$reply = ($reply == true) ? "<div class='comment--reply-url'><a class='CommentActionReply' data-comment-id='{$comment->id}' href='#Comment{$comment->id}'>Reply</a></div>" : "";
			if($comment->status < 1) continue;
			$cite = htmlentities($comment->cite);
			$text = htmlentities($comment->text);
			$userID = $comment->created_users_id;
			$u = wire("pages")->get($userID);
			$date = date('g:ia d/m/Y', $comment->created);
			$profileImage = $u->wm_profile_image->first()->size(200,200)->url;
			$username = $u->name;
			$out .= "
			<div class='{$commentClass}'>
			<article id='comment--{$comment->id}' class='comment--container'>
				<div class='comment--user-info'>
					<img src='{$profileImage}' class='comment--user-image'>
				</div>
				<div class='comment--content'>
					<div class='comment--meta'>
						<div class='comment--author'>{$username}</div>
						<a href='{$page->httpUrl}#comment--{$comment->id}' title='Link to comment' class='comment--url'>
							<time datetime='comment_date'>{$date}</time>
						</a>
					</div>
				<div>{$text}</div>
				{$reply}
				</div>
			</article>";
			$out .= getComments($page, $comment->id, false, "comment--child");
			$out .= "</div>";
		}
	}
	return $out;
}

/**
 * Menu builder
 *
 * @param string $menuClass Class for the menu container
 * @param string $subMenuClass Class for the sub menu container
 *
 */
function menuBuilder($classes = []) {
	if(!wire("settings")->wm_menu->count) {
		return;
	}
	if(empty($classes)) {
		$classes = [
			"menuClass" => "menu",
			"subMenuWrapperClass" => "sub-menu-wrapper",
			"subMenuClass" => "sub-menu",
			"menuItem" => "menu-item",
		];
	}
	$rootUrl = wire("config")->urls->root;
	$menu = "";
	$closeLast = null;
	$selector = (wire("user")->isLoggedin()) ? "wm_menu_show_to=1|2" : "wm_menu_show_to=1|3";
	$items = wire("settings")->wm_menu->find($selector);
	$depth = -1;
	foreach($items as $k => $item) {
		$hasChild = null;
		if($items->eq($k+1) && $items->eq($k+1)->depth > $item->depth) {
			$hasChild = "has-child";
		}
		if($item->depth > $depth) {
			if($depth == -1) {
				$menuWrapperOpen = "<ul class='{$classes["menuClass"]}'>";
			} else {
				$menuWrapperOpen = "<div class='{$classes["subMenuWrapperClass"]}'><ul class='{$classes["subMenuClass"]}'>";
			}
			$menu .= $menuWrapperOpen;
		} else if($item->depth < $depth) {
			$menuWrapperClose =  ($depth == -1) ? "</ul>" : "</ul></div>";
			$menu .= str_repeat($menuWrapperClose, $depth - $item->depth);
		}
		$href = " href='{$rootUrl}{$item->wm_menu_URL}'";
		$title = strip_tags(wire("sanitizer")->unentities($item->title), "<i>");
		$menu .= "<li class='{$classes["menuItem"]} {$item->wm_menu_class} {$hasChild}'><a{$href}>{$title}</a>";
		$depth = $item->depth;
	}
	while($depth--) $menu .= "</ul>";
	$menu .= "</ul>";
	return $menu;
}

