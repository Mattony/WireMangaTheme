<?php namespace ProcessWire;

class PasswordReset extends ProcessForgotPassword {

	/**
	 * Render forgot password form
	 *
	 */
	protected function step1_renderForm() {
		$form = $this->modules->get("InputfieldForm"); 
		$form->attr('action', './password-reset/?forgot=1'); 
		$form->attr('method', 'post');
		
		if($this->session->get("password_reset")) {
			$message = $this->session->get("password_reset");
			$this->session->remove("password_reset");
			$field = $this->modules->get("InputfieldMarkup");
			$field->attr('class', 'message success uk-text-center'); 
			$field->markupText = $message;
			$form->add($field);
		}
		
		$field = $this->modules->get("InputfieldText");
		$field->attr('id+name', 'username');
		$field->attr('class', 'uk-input'); 
		$field->required = true; 
		$field->label = $this->_("Enter your user name");
		$field->description = $this->_("If you have an account in our system with a valid email address on file, an email will be sent to you after you submit this form. That email will contain a link that you may click on to reset your password.");
		$form->add($field);

		$submit = $this->modules->get("InputfieldSubmit"); 
		$submit->attr('id+name', 'submit_forgot'); 
		$submit->attr('class', 'uk-button uk-margin-top'); 
		$form->add($submit);

		$this->session->userResetStep = 1; 

		return $form->render();
	}

		/**
	 * Send an email with password reset link to the given User account
	 *
	 */
	protected function step2_sendEmail(User $user) {

		$subject = $this->_("Password Reset Information"); // Email subject

		// create the unique verification token that is stored on the server and sent in the email
		$token = md5(mt_rand() . $user->name . $user->id . microtime() . mt_rand()); 

		// set some session vars we'll use for comparison
		$this->session->userResetStep = 2; 
		$this->session->userResetID = $user->id; 
		$this->session->userResetName = $user->name;

		$url = $this->page->httpUrl() . "password-reset/?forgot=1&user_id={$user->id}&token=" . urlencode($token);

		$body = $this->_("To complete your password reset, click the URL below (or paste into your browser) and follow the instructions:") . "\n\n"; // Email body part 1
		$body .= $url . "\n\n";
		$body .= $this->_("This URL will expire 60 minutes from time it was sent. This URL must be opened from the same computer and browser that the request was initiated from."); // Email body part 2
		
		$emailFrom = $this->emailFrom; 	
		if(!$emailFrom) $emailFrom = $this->wire('config')->adminEmail;
		if(!$emailFrom) $emailFrom = 'processwire@' . $this->config->httpHost;

		if($this->wire('mail')->send($user->email, $emailFrom, $subject, $body)) {

			// for informational/debugging purposes
			$ip = preg_replace('/[^\d.]/', '', $_SERVER['REMOTE_ADDR']);

			// clear space for this reset request, since there can only be one active for any given user
			$database = $this->wire('database');
			$table = $database->escapeTable($this->table); 
			
			try {
				
				$query = $database->prepare("DELETE FROM `{$table}` WHERE id=:id"); 
				$query->bindValue(":id", (int) $user->id, \PDO::PARAM_INT);
				$query->execute();
				
				$query = $database->prepare("INSERT INTO `{$table}` SET id=:id, name=:name, token=:token, ts=:ts, ip=:ip"); 
				$query->bindValue(":id", $user->id, \PDO::PARAM_INT);
				$query->bindValue(":name", $user->name); 
				$query->bindValue(":token", $token); 
				$query->bindValue(":ts", time(), \PDO::PARAM_INT);
				$query->bindValue(":ip", $ip);
				$query->execute();
					
			} catch(\Exception $e) {
				// catch any errors, just to prevent anything from ever being reported to screen
				$this->session->clearErrors();
				$this->error("Unable to complete this step"); 
				return;
			}
		}
	}
	
	/**
	 * Build the form with the reset password field
	 *
	 */
	protected function step3_buildForm($id, $token) {

		$form = $this->modules->get("InputfieldForm"); 
		$form->attr('method', 'post');
		$form->attr('action', "./?forgot=1&user_id=$id&token=$token"); 

		$field = $this->modules->get("InputfieldPassword"); 
		$field->attr('id+name', 'pass'); 
		$field->attr('class', 'uk-input uk-width-large'); 
		$field->required = true; 
		$field->label = $this->_("Reset Password"); // New password field label
		$form->add($field);

		$submit = $this->modules->get("InputfieldSubmit"); 
		$submit->attr('id+name', 'submit_reset'); 
		$submit->attr('class', 'uk-button uk-margin-top');
		$form->add($submit); 

		return $form; 
	}
	
	/**
	 * Process the submitted password reset form and reset password
	 *
	 */
	protected function step4_completeReset($id, $form) {

		$form->processInput($this->input->post);
		$user = $this->users->get((int) $id); 
		$pass = $form->get('pass')->value; 

		if(count($form->getErrors()) || !$user->id || !$pass) return $form->render();

 		$outputFormatting = $user->outputFormatting;
		$user->setOutputFormatting(false);
		$user->pass = $pass; 
		$user->save();
		$user->setOutputFormatting($outputFormatting);

		$this->session->message($this->_("Your password has been successfully reset. You may now login.")); 

		$this->session->remove('userResetStep'); 
		$this->session->remove('userResetID'); 
		$this->session->remove('userResetName'); 

		$database = $this->wire('database');
		$table = $database->escapeTable($this->table);
		$query = $database->prepare("DELETE FROM `$table` WHERE id=:id"); 
		$query->bindValue(":id", $user->id, \PDO::PARAM_INT); 
		$query->execute();
		
		$this->session->set("password_reset", "Your password has been changed.");
		$this->session->redirect("./"); 
			
	}
}