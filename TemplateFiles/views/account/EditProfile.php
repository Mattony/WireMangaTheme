<?php namespace ProcessWire;

// redirect to home page if user is logged in
if(!$user->isLoggedin()) {
	$session->redirect($config->urls->httpRoot);
}
// Updates the profile settings
$account->editProfile(); ?>

<section id="page-<?= $page->id ?>" class="uk-width-large uk-margin-auto">
	<h1><?= $page->title ?></h1>
	<div class="edit-profile--main">
		<div class=""></div>
		<!-- Displays the form -->
		<?= $account->editProfileForm(); ?>
	</div>
</section>
