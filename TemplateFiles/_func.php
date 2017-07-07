<?php  namespace ProcessWire;


/**
 * Get terms and display them
 *
 * @param PageArray $terms Field containing the terms ($page->genre)
 * @param string $sep Separator between the links
 * @param string $linkClass Class for the link
 *
 * @return string
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
 * @return string
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
 * @param string $commentClass Class for the comment container
 * @param int $depth Comment nesting depth
 *
 * @return string
 *
 */
function getComments($page, $parentID = 0, $commentClass = "comment", $depth = 0) {
	$maxDepth = wire("fields")->get("wm_comments")->depth;
	$commentDepth = $depth;
	$showReplyLink = true;
	if( $maxDepth == 0 || $maxDepth <= $commentDepth) {
		$showReplyLink = false;
	}
	$comments = $page->wm_comments;
	$out = "";
	if( $parentID !== 0 ){
		$comments = $comments->sort("created");
	}
	foreach($comments as $comment)
	{
		if($comment->parent_id == $parentID)
		{
			$replyLink = ($showReplyLink == true) ? "<div class='comment-reply-url'><a class='CommentActionReply' data-comment-id='{$comment->id}' href='#Comment{$comment->id}'>Reply</a></div>" : "";
			if($comment->status < 1) continue;
			$cite = htmlentities($comment->cite);
			$text = htmlentities($comment->text);
			$userID = $comment->created_users_id;
			$u = wire("pages")->get($userID);
			$date = date('g:ia d/m/Y', $comment->created);
			$profileImage = $u->wm_profile_image ? "<img src='{$u->wm_profile_image->first()->size(200, 200)->url}' class='comment-user-image'>" : "";
			$username = $u->name;
			$out .= "
			<div class='{$commentClass}'>
			<article id='comment-{$comment->id}' class='comment-container'>
				<div class='comment-user-info'>
					{$profileImage}
				</div>
				<div class='comment-content'>
					<div class='comment-meta'>
						<div class='comment-author'>{$username}</div>
						<a href='{$page->httpUrl}?show=comments#comment-{$comment->id}' title='Link to comment' class='comment-url'>
							<time datetime='comment_date'>{$date}</time>
						</a>
					</div>
				<div class='comment-body'>{$text}</div>
				{$replyLink}
				</div>
			</article>";
			$depthClass = "depth-" . ($commentDepth + 1);
			if($commentDepth >= $maxDepth) {
				$depthClass = "depth-" . $maxDepth;
				$out .= "</div>";
			}
			
			
			$out .= getComments($page, $comment->id, $depthClass, $commentDepth + 1);
			if($commentDepth < $maxDepth) {
				$out .= "</div>";
			}
		}
	}
	return $out;
}

/**
 * Menu builder
 *
 * @return string
 *
 */
function menuBuilder() {
	if(!wire("settings")->wm_menu->count) {
		return;
	}
	$rootUrl = wire("config")->urls->root;
	$menu = "";
	$closeLast = null;
	$selector  = (wire("user")->isLoggedin()) ? "wm_menu_show_to=1|2" : "wm_menu_show_to=1|3";
	$selector .= (wire("user")->isSuperuser()) ? ", wm_menu_admin=0|1" : ", wm_menu_admin=0";

	$items = wire("settings")->wm_menu->find($selector);
	if(!count($items)) {
		return;
	}
	$depth = -1;
	foreach($items as $k => $item) {
		$hasChild     = null;
		$hasChildIcon = null;
		if($items->eq($k+1) && $items->eq($k+1)->depth > $item->depth) {
			$hasChild = "has-child";
			$hasChildIcon = "<div class='submenu-toggle'><i class='fa fa-chevron-down' aria-hidden='true'></i></div>";
		}
		if($item->depth > $depth) {
			if($depth == -1) {
				$menuWrapperOpen = "<ul class='menu hidden'>";
			} else {
				$menuWrapperOpen = "<div class='submenu-wrap'><ul class='submenu'>";
			}
			$menu .= $menuWrapperOpen;
		} else if($item->depth < $depth) {
			$menuWrapperClose =  ($depth == -1) ? "</ul>" : "</ul></div>";
			$menu .= str_repeat($menuWrapperClose, $depth - $item->depth);
		}
		$href = ($item->wm_menu_URL != "-") ? " href='{$item->wm_menu_URL}'" : "";
		$title = strip_tags(wire("sanitizer")->unentities($item->title), "<i>");
		$itemClasses = trim(preg_replace('!\s+!', ' ', "menu-item {$item->wm_menu_class} {$hasChild}"));
		$menu .= "<li class='{$itemClasses}'><a{$href} class='menu-link'>{$title}</a>{$hasChildIcon}";
		$depth = $item->depth;
	}
	while($depth--) $menu .= "</ul>";
	$menu .= "</ul>";
	return $menu;
}


/**
 * Adult Notice
 *
 * @param Page $page Page to redirect once accepted
 * @param Page $parent Page to check for adult content
 *
 * @return string
 *
 */
function adultNotice($page, $parent) {
	$user    = wire("user");
	$input   = wire("input");
	$session = wire("session");
	$out     = false;
	// set an adult content session variable
	// preventing the adult content notice from appearing again during a session
	if($input->post->show_adult) {
		$session->set("adult", true);
		$session->redirect($page->httpUrl);
	}
	
	// show the adult content notice
	// if not disabled and the adult content session variable was not set
	if($parent->wm_adult && !$session->get("adult") && !$user->wm_adult_warning_off) {
		$message = wire("settings")->wm_adult_warn_mess ? wire("settings")->wm_adult_warn_mess : "This manga contains adult content.<br>Proceed?";
		$out = "<div class='uk-flex'>";
			$out .= "<div class='uk-width-xxlarge uk-margin-auto uk-text-center uk-text-lead'>";
			$out .= $message;
			$out .= "<form method='post'>";
			$out .= "<input type='submit' name='show_adult' value='OK' class='uk-input uk-margin-top uk-button uk-button-danger'>";
			$out .= "</form>";
			$out .= "</div>";
		$out .= "</div>";
	}
	return $out;
}

/**
 * View Counter
 *
 * @return string
 *
 */
function viewCounter() {
	$page    = wire("page");
	$session = wire("session");
	// increase views by one if it is a new session
	if(!$session->get('viewed:'.$page->id)) {

		// increment views and save page
		$page->of(false);
		$page->wm_views = $page->wm_views ? $page->wm_views + 1 : 1;
		$page->save("wm_views");

		// set page as viewed so it doesn't increment again this session
		$session->set('viewed:'.$page->id, true);
	}
}


/**
 * Reader Settings
 *
 * @return array
 *
 */
function readerSettings() {
	$page    = wire("page");
	$user    = wire("user");
	$input   = wire("input");
	$session = wire("session");

	$valid_values = [0, 1];
	if($input->post->submit_rs && in_array($input->post->show_all_ch_images, $valid_values)) {
		if($user->isLoggedin()) {
			// save settings to user profile
			$user->of(false);
			$user->wm_all_images = $input->post->show_all_ch_images;
			$user->wm_max_img_width = (int) $input->post->max_img_width;
			$user->save();
		}
		else {
			// save settings to user session
			$session->set("show_all_ch_images", $input->post->show_all_ch_images);
			$session->set("max_img_width", $input->post->max_img_width);
		}
	}

	$settings = [];
	if($user->isLoggedin()) {
		// get settings from user profile
		$settings["show_all_ch_images"] = $user->wm_all_images;
		$settings["width"] = 1000;
		if($user->wm_max_img_width) {
			$settings["width"] = $user->wm_max_img_width;
		}
	} else {
		// get settings from user profile
		$settings["show_all_ch_images"] = $session->get("show_all_ch_images");
		$settings["width"] = 1000;
		if($session->get("max_img_width")) {
			$settings["width"] = $session->get("max_img_width");
		}
	}
	// redirect to a page displaying single image 
	// or all chapter images on the same page
	if(!$input->urlSegment1 && $settings["show_all_ch_images"] == 0) {
		$session->redirect($page->url . "1/");
	}
	elseif($input->urlSegment1 && $settings["show_all_ch_images"] == 1) {
		$session->redirect($page->url);
	}
	return $settings;
}



/**
 * Subscribe user to manga 
 *
 * Adds user to list of users emailed 
 * when a new chapter is added to the manga they subscribed
 *
 * @param Page $page Page the user subscribes to
 * @param User $user User that subscribes to the page
 *
 * @return bool
 */

function subscribeUserToManga($page, $user) {
	$page->of(false);
	$page->wm_manga_subs = $user;
	return $page->save("wm_manga_subs");
}
function unsubscribeUserFromManga($page, $user) {
	$page->of(false);
	$page->wm_manga_subs->remove($user);
	return $page->save("wm_manga_subs");
}