<?php namespace ProcessWire; ?>

<section id="page-<?= $page->id ?>" class="uk-width-large uk-margin-auto">
	<?= $session_message ?>
	<h1><?= $user->name ?></h1>
	<div class="edit-profile-main">

		<form class='form edit-profile-form' method='post' enctype='multipart/form-data'>

			<div class='form-group uk-margin-bottom'>
				<label for='profile-image' id='profile-image-label' class='edit-profile--label'>
					<div><strong>Select New Avatar</strong></div>
					<?= $profileImage ?>
				</label>

				<input type='file' name='profile_image' id='profile-image' class='uk-input'>
				<input type='hidden' name='hidden_profile_image' id='hidden-profile-image' class='uk-input'>

				<div id='cropper-container' class='profile-image'>
					<img id='image' src='#'>
				</div>
			</div>

			<?php $checked = $user->wm_hide_adult ? "checked='checked'" : ""; ?>
			<div class='form-group uk-margin-bottom'>
				<label for='hide-adult' class='uk-form-label'>Hide Adult Manga</label> 
				<input type='checkbox' name='wm_hide_adult' id='hide-adult' class='uk-checkbox' <?= $checked ?>>
				<br><em>If checked manga marked as adult will be hidden.</em>
			</div>

			<?php $checked = $user->wm_adult_warning_off ? "checked='checked'" : ""; ?>
			<div class='form-group uk-margin-bottom'>
				<label for='adult-warning' class='uk-form-label'>Disable Adult Warning</label> 
				<input type='checkbox' name='wm_adult_warning_off' id='adult-warning' class='uk-checkbox' <?= $checked ?>>
				<br><em>If checked the adult warning won't show anymore.</em>
			</div>

			<div class='form-group uk-margin-bottom'>
				<label for='email' class='uk-form-label'>Email</label>
				<input type='text' name='email' placeholder='Email' value='<?= $user->email ?>' id='email' class='uk-input'>
			</div>

			<div class='form-group uk-margin-bottom'>
				<label for='password' class='uk-form-label'>Password</label>
				<input type='password' name='password' placeholder='password' id='password' class='uk-input'>
				<br><em>Requirements: at least <?= $passLength ?> characters long, <?= $passReq ?>.</em>
			</div>
			<div class='form-group uk-margin-bottom'>
				<label for='_password' class='uk-form-label'>Confirm Password</label>
				<input type='password' name='_password' placeholder='confirm password' id='_password' class='uk-input'>
			</div>

			<div class='form-group uk-margin-bottom'><input type='submit' name='submit' class='uk-button uk-button-primary uk-width-1-1' value='Save'></div>
		</form>
	</div>
</section>
