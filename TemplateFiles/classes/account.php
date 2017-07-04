<?php namespace ProcessWire;

/**
 * Class for handling user account related stuff
 *
 */
class Account extends Wire {


	/**
	 * User registration
	 *
	 * @return string
	 *
	 */
	public function register() {
		if( $this->wire("input")->post->username &&
			$this->wire("input")->post->email && $this->wire("input")->post->password ) {

			$username = $this->wire("input")->post->username;
			$email = $this->wire("input")->post->email;
			$pass  = $this->wire("input")->post->password;
			$_pass = $this->wire("input")->post->_password;
			// Check the user input
			if($username !== $this->wire("sanitizer")->pageName($username, false, 25) || strlen($username) < 4 ) {
				$this->wire("session")->set("registration_message", "<div class='message error'>Username is not valid. Username has to be between 4 and 25 characters, lowercase letters, digits, underscore, hyphen and period.</div>");
				return false;
			}
			if($this->wire("users")->get($username)->id) {
				$this->wire("session")->set("registration_message", "<div class='message error'>Username is taken.</div>");
				return false;
			}
			if($email !== $this->wire("sanitizer")->email($email)) {
				$this->wire("session")->set("registration_message", "<div class='message error'>Email is not valid.</div>");
				return false;
			}

			if(strlen($pass) && $pass !== $_pass) {
				$this->wire("session")->set("registration_message", "<div class='message error'>Passwords don't match.</div>");
				return false;
			}
			if(!$this->isValidPassword($pass)) {
				$error = $this->wire("session")->get("password_validation");
				$this->wire("session")->set("registration_message", "<div class='message error'>{$error}</div>");
				return false;
			}

			$this->createUser($username, $email, $pass);
			if($this->wire("settings")->wm_user_activate) {
				$this->sendActivationMail($email, $username);
			}
			return true;
		}
		$this->wire("session")->set("registration_message", "<div class='message error'>All fields are required.</div>");
		return false;
	}

	public function createUser($username, $email, $pass) {
		$u = new User();
		$u->name = $username;
		$u->email = $email;
		$u->pass = $pass;
		$u->wm_activation_code = $this->wire("settings")->wm_user_activate ? $this->generateHash(100) : "0";
		$u->wm_registration_date = time();
		$u->addRole("member");
		return $u->save();
	}

	public function generateHash($length) {
		$rand = new Password();
		return $rand->randomBase64String($length);
	}

	public function sendActivationMail($to, $username) {
		$u = $this->wire("users")->get($username);
		// Send activation code
		$activationLink = $this->wire("config")->urls->httpRoot."user/activate/?user=".$username."&hash=".$u->wm_activation_code;
		$site = $this->wire("config")->urls->httpRoot;
		$siteName = $this->wire("settings")->wm_site_name;

		// Messaged sent to user
		$emailMessage  = "Hi {$username}!<br><strong>Thank you for signing up at {$site}.</strong><br>";
		$emailMessage .= "Please verify your email address by clicking the link below!";
		$emailMessage .= "<br><br>Activation Link: <a href='{$activationLink}'>{$activationLink}</a>";

		// send email
		$mail = wireMail();
		$mail->to($to);
		$mail->from($this->wire("settings")->wm_site_email);
		$mail->subject("Email verification @ {$this->wire("config")->wmSiteName}");
		$mail->bodyHTML($emailMessage);
		return $mail->send();
	}

	/**
	 * Activate user account
	 *
	 * @param string $username 
	 * @param string $hash compare with value saved on registration
	 *
	 * @return bool
	 */
	public function activateUserAccount($username, $hash) {
		$username = $this->wire("sanitizer")->pageName($username);
		$u = $this->wire("users")->get($username);
		if($u->id && strcmp($u->wm_activation_code, $hash) === 0) {
			$u->of(false);
			$u->wm_activation_code = 0;
			return $u->save();
		} else {
			return false;
		}
	}


	/**
	 * Process the edit profile form
	 *
	 * @return bool
	 */
	public function editProfile() {
		$user  = $this->wire("user");
		$email = $this->wire("input")->post->email;
		
		$hideAdult = $this->wire("input")->post->wm_hide_adult;
		$adultWarning = $this->wire("input")->post->wm_adult_warning_off;
		$pass  = $this->wire("input")->post->password;
		$_pass = $this->wire("input")->post->_password;
		$passRules = $this->wire("fields")->get("pass");
		$u = $this->wire("pages")->get("template=user, name={$user->name}");

		if($this->wire("input")->post->hidden_profile_image) {
			$this->uploadBase64Image();
		} else {
			$this->uploadWUImage($u, 'profile_image');
		}

		if(isset($email) && $email != $this->wire("sanitizer")->email($email)) {
			$this->wire("session")->set("edit_error", "Email is not valid.");
			return false;
		}

		if(isset($pass) && $pass != $_pass) {
			$this->wire("session")->set("edit_error", "Passwords don't match.");
			return false;
		}

		if(isset($pass) && strlen($pass) && $this->isValidPassword($pass)) {
			$error = $this->wire("session")->get("password_validation");
			$this->wire("session")->set("edit_error", $error);
			return false;
		}

		if(isset($u, $email) && $email !== $u->email) {
			$this->saveTmpEmail($user, $email);
			$this->sendEmailChangeConfirmation($user, $email);
		}
		$u->of(false);
		$u->pass = $pass;
		$u->wm_hide_adult = isset($hideAdult) ? 1 : 0;
		$u->wm_adult_warning_off = isset($adultWarning) ? 1 : 0;
		$u->save();
		$this->wire("session")->set("edit_succes", "Changes Saved");
		return true;
	}

	protected function saveTmpEmail($user, $email) {
		$user->of(false);
		$user->wm_tmp_email = $email;
		$user->wm_email_conf_code = $this->generateHash(100);
		$user->save();
	}

	protected function sendEmailChangeConfirmation($user, $email) {
		$confirmLink = "{$this->wire("config")->urls->httpRoot}user/confirm/?action=confirmemail&user={$user->name}&hash={$user->wm_email_conf_code}";
		$emailMessage  = "Hi {$user->name}!<br>Please confirm the email address change by clicking the bellow link.";
		$emailMessage .= "<a href='{$confirmLink}'>{$confirmLink}</a><br>";
		$emailMessage .= "If you didn't initiate the change please ignore this email.";
		$mail = wireMail();
		$mail->to($email);
		$mail->from($this->wire("settings")->wm_site_email);
		$mail->subject("Email change confirmation @ {$this->wire("config")->wmSiteName}");
		$mail->bodyHTML($emailMessage);
		$mail->send();
	}

	public function changeUserEmail() {
		$username = $this->wire("sanitizer")->pageName($this->wire("input")->get->user);
		$hash = $this->wire("input")->get->hash;
		$user = $this->wire("users")->get($username);
		if(!$user->id) {
			return false;
		}
		if($user->wm_email_conf_code === "0") {
			return false;
		}
		if($user->id && strcmp($user->wm_email_conf_code, $hash) === 0) {
			$user->of(false);
			$user->email = $user->wm_tmp_email;
			$user->wm_tmp_email = 0;
			$user->wm_email_conf_code = 0;
			return $user->save();
		}
		return false;
	}


	/**
	 * Return whether or not the given password is valid according to configured requirements
	 * 
	 * Exact error messages can be retrieved with $this->getErrors().
	 * 
	 * @param string $value Password to validate
	 * 
	 * @return bool
	 * 
	 */
	public function isValidPassword($value) {
		$requireLetter = 'letter';
		$requireLowerLetter = 'lower';
		$requireUpperLetter = 'upper';
		$requireDigit = 'digit';
		$requireOther = 'other';

		$numErrors = 0;
		$passRules = $this->wire("fields")->get("pass");
		$requirements = $passRules->requirements;
		$this->wire("session")->remove("password_validation");
		if(preg_match('/[\t\r\n]/', $value)) {
			$this->wire("session")->set("password_validation", "Password contained invalid whitespace");
			return false;
		}

		if(strlen($value) < $passRules->minlength) {
			$this->wire("session")->set("password_validation", "Password is less than required number of characters");
			return false;
		}

		if(in_array($requireLetter, $requirements)) {
			// if(!preg_match('/[a-zA-Z]/', $value)) {
			if(!preg_match('/\p{L}/', $value)) {
				$this->wire("session")->set("password_validation", "Password does not contain at least one letter (a-z A-Z)");
				return false;
			}
		}

		if(in_array($requireLowerLetter, $requirements)) {
			if(!preg_match('/\p{Ll}/', $value)) {
				$this->wire("session")->set("password_validation", "Password must have at least one lowercase letter (a-z)");
				return false;
			}
		}

		if(in_array($requireUpperLetter, $requirements)) {
			if(!preg_match('/\p{Lu}/', $value)) {
				$this->wire("session")->set("password_validation", "Password must have at least one uppercase letter (A-Z)");
				return false;
			}
		}

		if(in_array($requireDigit, $requirements)) {
			if(!preg_match('/\p{N}/', $value)) {
				$this->wire("session")->set("password_validation", "Password does not contain at least one digit (0-9)");
				return false;
			}
		}

		if(in_array($requireOther, $requirements)) {
			if(!preg_match('/\p{P}/', $value) && !preg_match('/\p{S}/', $value)) {
				$this->wire("session")->set("password_validation", "Password must have at least one non-letter, non-digit character (like punctuation)");
				return false;
			}	
		}
		
		return true;
	}

	/**
	 * Upload users profile image
	 *
	 * Used when image is set as base64 with javascript on a hidden field
	 *
	 * @param User $user Current user
	 * @param string $base64String Image in base64 format
	 *
	 */
	protected function uploadBase64Image() {
		$user = $this->wire("user");
		$base64String = explode(',', $this->wire("input")->post->hidden_profile_image);
		$this->wire("log")->error($base64String);
		$start = strpos($base64String[0], '/') + 1;
		$end   = strpos($base64String[0], ';');
		$extension = substr($base64String[0], $start, $end-$start);
		$this->wire("log")->error($extension);
		$this->wire("log")->error($base64String[1]);
		$imagePath = $this->wire("config")->paths->assets."files/avatar.{$extension}";
		file_put_contents($imagePath, base64_decode($base64String[1]));
		$user->of(false);
		$user->wm_profile_image->removeAll();
		$user->wm_profile_image = $imagePath;
		$user->save();
		@unlink($imagePath);
	}

	/**
	 * Upload users profile image
	 *
	 * Used when javascript is disabled
	 *
	 * @param User $user Current user
	 * @param string $fieldName Name of the field holding the image
	 *
	 */
	protected function uploadWUImage($user, $fieldName) {
		$upload_path = $this->wire("config")->paths->assets . "files/tmp/";
		if(!is_dir($upload_path)) {
			if(!wireMkdir($upload_path)) throw new WireException("No upload path!");
		}
		$maxFileSize = 5;
		$ext = array("jpg", "jpeg", "png", "gif");
		$f = new WireUpload($fieldName);
		$f->setMaxFiles(1);
		$f->setMaxFileSize($maxFileSize*1000*1000);
		$f->setOverwrite(true);
		$f->setDestinationPath($upload_path);
		$f->setValidExtensions($ext);
		$file = $f->execute();
		if($f->getErrors()){
			$errors = $f->getErrors(true);
			return $errors;
		}
		if($_FILES['profile_image']['size'] != 0)
		{
			$user->of(false);
			$user->wm_profile_image->removeAll();
			$user->wm_profile_image = $upload_path . $file[0];
			$user->save();
			@unlink($upload_path . $file[0]);
		}
	}
}
