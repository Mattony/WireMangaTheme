<?php namespace ProcessWire;

/**
 * Class for handling user account related stuff
 *
 */
class Account extends Wire {


	/**
	 * User registration
	 *
	 * @return void|string
	 *
	 */
	public function register() {
		// if($this->wire('user')->isLoggedin()) {
		// 	$this->wire('session')->redirect($this->wire('config')->urls->httpRoot);
		// }

		if( $this->wire("input")->post->submit && $this->wire("input")->post->username &&
			$this->wire("input")->post->email && $this->wire("input")->post->password ) {

			$username = $this->wire("input")->post->username;
			$email = $this->wire("input")->post->email;
			$pass  = $this->wire("input")->post->password;
			$_pass = $this->wire("input")->post->_password;

			// Check the user input
			if($email != $this->wire("sanitizer")->email($email)) {
				$error = "<div class='contact-error'>Email is not valid.</div>";
				return $error;
			}

			if($username != $this->wire("sanitizer")->pageName($username)) {
				$error = "<div class='contact-error'>Username is not valid.</div>";
				return $error;
			}

			if($this->wire("pages")->get("template=user, name={$username}")->id) {
				$error = "<div class='contact-error'>Username is taken.</div>";
				return $error;
			}

			if($pass !== $_pass) {
				$error = "<div class='contact-error'>Passwords don't match.</div>";
				return $error;
			}

			// Create the user
			$this->createUser($username, $email, $pass);

			$siteName = $this->wire("config")->httpHosts[0];
			$this->sendActivationMail($email, $username, $u->accountStatus, $siteName);

			// Create user list page
			$p = new Page();
			$p->template = "user-list";
			$p->parent = $this->wire("pages")->get("/user/lists/");
			$p->title = $this->wire("sanitizer")->pageName($username);
			$p->save();
			$site = $this->wire("config")->urls->httpRoot;

			$message = "<div class='contact-succes'>Your account has been created!</div>";
			$this->wire('session')->set("registrationMessage", $message);
			$this->wire('session')->redirect($this->wire('page')->url);
		}
	}

	public function createUser($username, $email, $pass) {
		$u = new User();
		$u->name = $username;
		$u->email = $email;
		$u->pass = $pass;
		$accStatus = $this->wire("settings")->wm_user_activate ? $this->generateHash(100) : "active";
		$u->accountStatus = $accStatus;
		$u->registrationDate = time();
		$u->lastLoginDate = time();
		$u->addRole("member");
		$u->save();
	}

	public function generateHash($length) {
		$rand = new Password();
		$hash = $rand->randomBase64String($length);
		return $hash;
	}

	public function sendActivationMail($toEmail, $username, $hash, $siteName) {
		// Send activation code
		$activationLink = $this->wire("config")->urls->httpRoot."user/activate/?user=".$username."&hash=".$hash;

		// Messaged sent to user
		$emailMessage  = "Hi {$username}<br><strong>Thank you for signing up at {$site}.</strong><br>";
		$emailMessage .= "Please verify your email address by clicking the link below!";
		$emailMessage .= "<br><br>Activation Link: <a href='{$activationLink}'>{$activationLink}</a>";

		// send email
		$mail = wireMail();
		$mail->to($toEmail);
		$mail->from($this->wire("settings")->wm_site_email);
		$mail->subject("Email verification @ {$this->wire("config")->wmSiteName}");
		$mail->bodyHTML($emailMessage);
		$mail->send();
	}

	function registrationForm($formClass, $wrapperClass, $inputClass, $labelClass) {
		$out  = "";
		$out .= "<form method='post' action='' class='{$formClass}'>";
			$out .= "<div class='{$wrapperClass}'><label for='username' class='{$labelClass}'>Username</label>";
			$out .= "<input type='text' name='username' id='username' class='{$inputClass}' placeholder='Username' value=''>";
			$out .= "<em>Allowed characters: lowercase letters (a-z), digits (0-9), underscore (_), hyphen (-) and period (.).</em></div>";

			$out .= "<div class='{$wrapperClass}'><label for='email' class='{$labelClass}'>Email</label>";
			$out .= "<input type='text' name='email' id='email' class='{$inputClass}' placeholder='Email' value=''></div>";

			$out .= "<div class='{$wrapperClass}'><label for='password' class='{$labelClass}'>Password</label>";
			$out .= "<input type='password' name='password' id='password' class='{$inputClass}' placeholder='Password' value=''></div>";
			$out .= "<div class='{$wrapperClass}'><label for='_password' class='{$labelClass}'>Confirm Password</label>";
			$out .= "<input type='password' name='_password' id='_password' class='{$inputClass}' placeholder='Confirm password' value=''></div>";

			$out .= "<div class='{$wrapperClass}'><input type='submit' name='submit' id='' class='{$inputClass}' value='Register'></div>";
		$out .= "</form>";
		return $out;
	}

	/**
	 * Display login form
	 *
	 * @param string $formClass Class for the form
	 * @param string $wrapperClass Class for the wrapper of the label and input
	 * @param string $inputClass Class for the input
	 * @param string $labelClass Class for the label
	 *
	 * @return string
	 *
	 */
	function loginForm($formClass, $wrapperClass, $inputClass, $labelClass) {
		$out = "";
		$out .= "<form method='post' action='./' class='{$formClass}'>";
			$out .= "<div class='{$wrapperClass}'><label for='user' class='{$labelClass}'>Username</label>";
			$out .= "<input type='text' name='user' id='user' class='{$inputClass}' placeholder='Username'></div>";
			$out .= "<div class='{$wrapperClass}'><label for='pass' class='{$labelClass}'>Password</label>";
			$out .= "<input type='password' name='pass' id='pass' class='{$inputClass}' placeholder='Password'></div>";
			$out .= "<div class='{$wrapperClass}'><input type='submit' name='submit' id='' class='{$inputClass}' value='Login'></div>";
		$out .= "</form>";
		return $out;
	}

	/**
	 * Login user when form is submitted
	 *
	 * @return void
	 */
	public function loginUser() {
		// if($this->wire("user")->isLoggedin())
		// {
		// 	$session->redirect($this->wire("config")->urls->httpRoot);
		// }
		if($this->wire("input")->post->submit && $this->wire("input")->post->user && $this->wire("input")->post->pass)
		{
			$username = $this->wire("sanitizer")->pageName($this->wire("input")->post->user);
			$pass = $this->wire("input")->post->pass;
			$u = $this->wire("pages")->get("template=user, name={$username}");
			if($this->wire("session")->login($username, $pass))
			{
				$redirectTo = $this->wire("page")->parent->url;
				$this->wire("session")->redirect("{$redirectTo}profile");
			}
		}
	}

	/**
	 * Process the edit profile form
	 *
	 */
	public function editProfile() {
		$message = "";
		if($this->wire("input")->post->submit) {
			$user  = $this->wire("user");
			$email = $this->wire("input")->post->email;
			$pass  = $this->wire("input")->post->password;
			$_pass = $this->wire("input")->post->_password;
			$u = $this->wire("pages")->get("template=user, name={$user->name}");
			$upload_path = $this->wire("config")->paths->assets . "files/tmp/";
			$maxFileSize = 5;
			$ext = array("jpg", "jpeg", "png", "gif");

			if(!is_dir($upload_path)) {
				if(!wireMkdir($upload_path)) throw new WireException("No upload path!");
			}

			if($this->wire("input")->post->hidden_profile_image) {
				$this->uploadBase64Image($u, $base64String);
			} else {
				$this->uploadWUImage($u, 'profile_image');
			}

			if(isset($email) && $email != $this->wire("sanitizer")->email($email)) {
				$error = "Email is not valid.";
				return $error;
			}

			if(isset($pass) && $pass != $_pass) {
				$error = "Passwords don't match.";
				return $error;
			}

			$u->of(false);
			if(isset($email)) {
				$u->email = $this->wire("sanitizer")->email($email);
			}
			if(isset($pass)){
				$u->pass  = $pass;
			}
			$u->save();
			$message = "Changes Saved";
		}
		return $message;
	}

	/**
	 * Upload image for user profile
	 *
	 * Used when image is set as base64 with javascript on a hidden field
	 * the field is updated as the image is dragged/zoomed
	 *
	 * @param User $user Current user
	 * @param string $base64String Image in base64 format
	 *
	 */
	protected function uploadBase64Image($user, $base64String) {
		$base64String = explode(',', $this->wire("input")->post->hidden_profile_image);
		$start = strpos($base64String[0], '/') + 1;
		$end   = strpos($base64String[0], ';');
		$extension = substr($base64String[0], $start, $end-$start);
		$imagePath = $this->wire("config")->paths->assets."files/avatar.{$extension}";
		file_put_contents($imagePath, base64_decode($base64String[1]));
		$user->of(false);
		$user->wm_profile_image->removeAll();
		$user->wm_profile_image = $imagePath;
		$user->save();
		@unlink($imagePath);
	}

	/**
	 * Upload image for user profile
	 *
	 * Used when javascript is disabled
	 *
	 * @param User $user Current user
	 * @param string $fieldName Name of the field holding the image
	 *
	 */
	protected function uploadWUImage($user, $fieldName) {
		$f = new WireUpload($fieldName);
		$f->setMaxFiles(1);
		$f->setMaxFileSize($maxFileSize*1024*1024);
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

	public function editProfileForm($formClass, $wrapperClass, $inputClass, $labelClass) {

		$u = $this->wire("pages")->get("template=user, name={$this->wire("user")->name}");
		$profileImage = "<img src='{$u->wm_profile_image->first()->size(190, 190)->url}' id='current-profile-image'>";

		$out = "";
		$out .= "<form class='{$formClass}' method='post' action='' enctype='multipart/form-data'>";

			$out .= "<div class='{$wrapperClass}'>";
				$out .= "<label for='profile-image' id='profile-image-label' class='edit-profile--label'>";
					$out .= "<div><strong>Select New Avatar</strong></div>";
					$out .= $profileImage;
				$out .= "</label>";

				$out .= "<input type='file' name='profile_image' id='profile-image' class='{$inputClass}'>";
				$out .= "<input type='hidden' name='hidden_profile_image' id='hidden-profile-image' class='{$inputClass}'>";

				$out .= "<div id='cropper-container' class='profile-image'>"; // class must be same as input id
					$out .= "<img id='image' src='#'>";
					$out .= "";
				$out .= "</div>";
			$out .= "</div>";

			$out .= "<div class='{$wrapperClass}'>";
				$out .= "<label for='email' class='{$labelClass}'>Email</label>";
				$out .= "<input type='text' name='email' placeholder='Email' value='{$u->email}' id='' class='{$inputClass}'>";
			$out .= "</div>";

			$out .= "<div class='{$wrapperClass}'>";
				$out .= "<label for='password' class='{$labelClass}'>Password</label>";
				$out .= "<input type='password' name='password' placeholder='password' id='' class='{$inputClass}'></div>";
				$out .= "<div class='{$wrapperClass}'><label for='_password' class='{$labelClass}'>Confirm Password</label>";
				$out .= "<input type='password' name='_password' placeholder='confirm password' id='' class='{$inputClass}'>";
			$out .= "</div>";

			$out .= "<div class='{$wrapperClass}'><input type='submit' name='submit' id='' class='{$inputClass}' value='Edit'></div>";
		$out .= "</form>";
		return $out;
	}

}
