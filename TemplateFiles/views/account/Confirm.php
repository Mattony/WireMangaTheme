<?php namespace ProcessWire; 
if($input->get("action") === "confirmemail") {
	if(!$account->changeUserEmail()) {
		echo "<div class='message error'>Something went wrong.</div>";
	} else {
        $session->set("email_changed", "Your email address was changed.");
		$session->redirect($config->urls->httpRoot . "user/edit-profile/");
	}
}