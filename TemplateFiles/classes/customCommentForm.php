<?php namespace ProcessWire;

class customCommentForm extends CommentForm {
    /**
	 * Render the CommentForm output and process the input if it's been submitted
	 *
	 * @return string
	 *
	 */
	public function render() {

		if(!$this->commentsField) return "Unable to determine comments field";
		$options = $this->options; 	
		$labels = $options['labels'];
		$attrs = $options['attrs'];
		$id = $attrs['id'];
		$submitKey = $id . "_submit";
		$honeypot = $options['requireHoneypotField'];
		$inputValues = array('cite' => '', 'email' => '', 'website' => '', 'stars' => '', 'text' => '', 'notify' => '');
		if($honeypot) $inputValues[$honeypot] = '';
		
		$user = $this->wire('user'); 

		if($user->isLoggedin()) {
			$inputValues['cite'] = $user->name; 
			$inputValues['email'] = $user->email;
		}
		
		$input = $this->wire('input'); 
		$divClass = 'new';
		$class = trim("CommentForm " . $attrs['class']); 
		$note = '';

		foreach($options['presets'] as $key => $value) {
			if(!is_null($value)) $inputValues[$key] = $value; 
		}

		$out = '';
		$showForm = true; 
		
		if($options['processInput'] && $input->post->$submitKey == 1) {
			$comment = $this->processInput(); 
			if($comment) { 
				$out .= $this->renderSuccess($comment); // success, return
			} else {
				$inputValues = array_merge($inputValues, $this->inputValues);
				foreach($inputValues as $key => $value) {
					$inputValues[$key] = htmlentities($value, ENT_QUOTES, $this->options['encoding']);
				}
				$note = "\n\t$options[errorMessage]";
				$divClass = 'error';
			}

		} else if($this->options['redirectAfterPost'] && $input->get('comment_success') === "1") {
			$note = $this->renderSuccess();
		}

		$form = '';
		if($showForm) {
			if($this->commentsField->depth > 0) {
				$form = $this->renderFormThread($id, $class, $attrs, $labels, $inputValues);
			} else {
				$form = $this->renderFormNormal($id, $class, $attrs, $labels, $inputValues); 
			}
			if(!$options['presetsEditable']) {
				foreach($options['presets'] as $key => $value) {
					if(!is_null($value)) $form = str_replace(" name='$key'", " name='$key' disabled='disabled'", $form); 
				}
			}
		}

		$out .= 
			"\n<div id='{$id}' class='{$id}_$divClass'>" . 	
			"\n" . $this->options['headline'] . $note . $form . 
			"\n</div><!--/$id-->";


		return $out; 
	}

    protected function renderFormNormal($id, $class, $attrs, $labels, $inputValues) {
		$form = 
			"\n<form id='{$id}_form' class='$class CommentFormNormal' action='$attrs[action]' method='$attrs[method]'>";

        if($this->wire("user")->isLoggedin()){
            $form .= "\n\t<p class='CommentFormCite {$id}_cite'>" .
                "\n\t\t<input type='hidden' name='cite' class='required uk-input' required='required' id='{$id}_cite' value='$inputValues[cite]' maxlength='128' />" .
                "\n\t</p>" .
                "\n\t<p class='CommentFormEmail {$id}_email'>" .
                "\n\t\t<input type='hidden' name='email' class='required email uk-input' required='required' id='{$id}_email' value='$inputValues[email]' maxlength='255' />" .
                "\n\t</p>";
        } else {
            $form .= "\n\t<p class='CommentFormCite {$id}_cite'>" .
                "\n\t\t<label for='{$id}_cite' class='uk-form-label'>$labels[cite]</label>" .
                "\n\t\t<input type='text' name='cite' class='required uk-input' required='required' id='{$id}_cite' value='$inputValues[cite]' maxlength='128' />" .
                "\n\t</p>" .
                "\n\t<p class='CommentFormEmail {$id}_email'>" .
                "\n\t\t<label for='{$id}_email' class='uk-form-label'>$labels[email]</label>" .
                "\n\t\t<input type='text' name='email' class='required email uk-input' required='required' id='{$id}_email' value='$inputValues[email]' maxlength='255' />" .
                "\n\t</p>";
        }
		if($this->commentsField && $this->commentsField->useWebsite && $this->commentsField->schemaVersion > 0) {
			$form .=
				"\n\t<p class='CommentFormWebsite {$id}_website'>" .
				"\n\t\t<label for='{$id}_website' class='uk-form-label'>$labels[website]</label>" .
				"\n\t\t<input type='text' name='website' class='website uk-input' id='{$id}_website' value='$inputValues[website]' maxlength='255' />" .
				"\n\t</p>";
		}

		if($this->commentsField->useStars && $this->commentsField->schemaVersion > 5) {
			$commentStars = new CommentStars();
			$starsClass = 'CommentFormStars';
			if($this->commentsField->useStars > 1) {
				$starsNote = $labels['starsRequired'];
				$starsClass .= ' CommentFormStarsRequired';
			} else {
				$starsNote = '';
			}
			$form .=
				"\n\t<p class='$starsClass {$id}_stars' data-note='$starsNote'>" .
				($labels['stars'] ? "\n\t\t<label for='{$id}_stars' class='uk-form-label'>$labels[stars]</label>" : "") .
				"\n\t\t<input type='number' name='stars' id='{$id}_stars' value='$inputValues[stars]' min='0' max='5' />" .
				"\n\t\t" . $commentStars->render(0, true) .
				"\n\t</p>";
		}

		// do we need to show the honeypot field?
		$honeypot = $this->options['requireHoneypotField'];
		if($honeypot) {
			$honeypotLabel = isset($labels[$honeypot]) ? $labels[$honeypot] : '';
			$honeypotValue = isset($inputValues[$honeypot]) ? $inputValues[$honeypot] : '';
			$form .=
				"\n\t<p class='CommentFormHP {$id}_hp'>" .
				"\n\t\t<label for='{$id}_$honeypot' class='uk-form-label'>$honeypotLabel</label>" .
				"\n\t\t<input type='text' id='{$id}_$honeypot' name='$honeypot' class='uk-input' value='$honeypotValue' size='3' />" .
				"\n\t</p>";
		}

		$form .=
			"\n\t<p class='CommentFormText {$id}_text'>" .
			"\n\t\t<label for='{$id}_text' class='uk-form-label'>$labels[text]</label>" .
			"\n\t\t<textarea name='text' class='required uk-textarea' required='required' id='{$id}_text' rows='$attrs[rows]' cols='$attrs[cols]'>$inputValues[text]</textarea>" .
			"\n\t</p>" . 
			$this->renderNotifyOptions() . 
			"\n\t<p class='CommentFormSubmit {$id}_submit'>" .
			"\n\t\t<button type='submit' name='{$id}_submit' id='{$id}_submit' class='uk-button uk-button-primary' value='1'>$labels[submit]</button>" .
			"\n\t\t<input type='hidden' name='page_id' value='{$this->page->id}' />" .
			"\n\t</p>" .
			"\n</form>";
		
		return $form; 
	}

    protected function renderFormThread($id, $class, $attrs, $labels, $inputValues) {
		
		$form = 
			"\n<form class='$class CommentFormThread' action='$attrs[action]' method='$attrs[method]'>";

        if($this->wire("user")->isLoggedin()){
            $form .= "\n\t<p class='CommentFormEmail {$id}_email'>" .
                "\n\t\t<input type='hidden' name='email' class='required email uk-input' required='required' id='{$id}_email' value='$inputValues[email]' maxlength='255' />" .
                "\n\t</p>";
        } else {
            $form .= "\n\t<p class='CommentFormCite {$id}_cite'>" .
                "\n\t\t<label for='{$id}_cite' class='uk-form-label'>$labels[cite]</label>" .
                "\n\t\t<input type='text' name='cite' class='required uk-input' required='required' id='{$id}_cite' value='$inputValues[cite]' maxlength='128' />" .
                "\n\t</p>" .
                "\n\t<p class='CommentFormEmail {$id}_email'>" .
                "\n\t\t<label for='{$id}_email' class='uk-form-label'>$labels[email]</label>" .
                "\n\t\t<input type='text' name='email' class='required email uk-input' required='required' id='{$id}_email' value='$inputValues[email]' maxlength='255' />" .
                "\n\t</p>";
        }

		if($this->commentsField && $this->commentsField->useWebsite && $this->commentsField->schemaVersion > 0) {
			$form .=
				"\n\t<p class='CommentFormWebsite {$id}_website'>" .
				"\n\t\t<label class='uk-form-label'>" . 
				"\n\t\t\t<span>$labels[website]</span> " .
				"\n\t\t\t<input type='text' name='website' class='website uk-input' value='$inputValues[website]' maxlength='255' />" .
				"\n\t\t</label>" . 
				"\n\t</p>";
		}

		if($this->commentsField->useStars && $this->commentsField->schemaVersion > 5) {
			$commentStars = new CommentStars();
			$starsClass = 'CommentFormStars';
			if($this->commentsField->useStars > 1) {
				$starsNote = $labels['starsRequired'];
				$starsClass .= ' CommentFormStarsRequired';
			} else {
				$starsNote = '';
			}
			$form .=
				"\n\t<p class='$starsClass {$id}_stars' data-note='$starsNote'>" .
				"\n\t\t<label class='uk-form-label'>" .
				"\n\t\t\t<span>$labels[stars]</span>" .
				"\n\t\t\t<input type='number' name='stars' id='{$id}_stars' value='$inputValues[stars]' min='0' max='5' />" .
				"\n\t\t\t" . $commentStars->render(0, true) .
				"\n\t\t</label>" .
				"\n\t</p>";
		}

		// do we need to show the honeypot field?
		$honeypot = $this->options['requireHoneypotField'];
		if($honeypot) {
			$honeypotLabel = isset($labels[$honeypot]) ? $labels[$honeypot] : '';
			$honeypotValue = isset($inputValues[$honeypot]) ? $inputValues[$honeypot] : '';
			$form .=
				"\n\t<p class='CommentFormHP {$id}_hp'>" .
				"\n\t\t<label class='uk-form-label'><span>$honeypotLabel</span>" .
				"\n\t\t<input type='text' name='$honeypot' class='uk-input' value='$honeypotValue' size='3' />" .
				"\n\t\t</label>" .
				"\n\t</p>";
		}

		$form .=
			"\n\t<p class='CommentFormText {$id}_text'>" .
			"\n\t\t<label class='uk-form-label'>" .
			"\n\t\t\t<span>$labels[text]</span>" .
			"\n\t\t\t<textarea name='text' class='required uk-textarea' required='required' rows='$attrs[rows]' cols='$attrs[cols]'>$inputValues[text]</textarea>" .
			"\n\t\t</label>" . 
			"\n\t</p>" .
			$this->renderNotifyOptions() . 
			"\n\t<p class='CommentFormSubmit {$id}_submit'>" .
			"\n\t\t<button type='submit' name='{$id}_submit' class='uk-button uk-button-primary' value='1'>$labels[submit]</button>" .
			"\n\t\t<input type='hidden' name='page_id' value='{$this->page->id}' />" .
			"\n\t\t<input type='hidden' class='CommentFormParent' name='parent_id' value='0' />" .
			"\n\t</p>" .
			"\n</form>";
		
		return $form;
	}

    protected function renderNotifyOptions() {
		if(!$this->commentsField->useNotify) return '';
		$out = '';
		
		$options = array();
		
		if($this->commentsField->depth > 0) {
			$options['2'] = $this->_('Replies');
		}


		if($this->commentsField->useNotify == Comment::flagNotifyAll) {
			$options['4'] = $this->_('All');
		}
	
		if(count($options)) {
			$out = 
				"\n\t<p class='CommentFormNotify'>" . 
				"\n\t\t<label class='uk-form-label'>" . $this->_('E-Mail Notifications:') . "</label> " . 
				"\n\t\t<label class='uk-form-label'><input type='radio' name='notify' class='uk-radio' checked='checked' value='0'>" . $this->_('Off') . "</label> ";
			
			foreach($options as $value => $label) {
				$label = str_replace(' ', '&nbsp;', $label); 
				$out .= "\n\t\t<label><input type='radio' name='notify' class='uk-radio' value='$value'>$label</label> ";
			}
			$out .= "\n\t</p>";
		}
	
		return $out; 
	}
}