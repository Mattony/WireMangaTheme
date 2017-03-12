<?php namespace ProcessWire; ?>
<?php $account->loginUser(); ?>


<section id="page-<?= $page->id ?>" class="uk-width-large uk-margin-auto">
	<h1><?= $page->title ?></h1>
	<?= $account->loginForm("", "uk-margin-bottom", "uk-input", "uk-form-label") ?>
	<div><a href="<?= $page->parent->httpUrl ?>password-reset">Forgot password?</a></div>
</section>
