<?php namespace ProcessWire; 
if($input->get("action") === "confirmemail") {
	if(!$account->changeUserEmail()) {
		echo "<div class='message error'>Something went wrong.</div>";
	} else {
        $session->set("email_changed", "Your email address was changed.");
		$session->redirect($page->httpUrl . "edit-profile/");
	}
} else {
	$session->redirect($page->httpUrl . "profile/");
}