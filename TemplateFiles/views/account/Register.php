<?php namespace ProcessWire; ?>
<?php $account->register() ?>

<section id="page-<?= $page->id ?>" class="uk-width-large uk-margin-auto">
	<h1><?= $page->title?></h1>
	<?= $session->get("registrationMessage") ?>
	<?= $account->registrationForm("", "uk-margin-bottom", "uk-input", "uk-form-label"); ?>
</section>

<?php $session->remove("registrationMessage"); ?>
