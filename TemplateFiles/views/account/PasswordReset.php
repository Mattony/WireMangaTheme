<?php namespace ProcessWire;
if($user->isLoggedin()) {
	$session->redirect($config->urls->httpRoot);
}
?>
<section id="page-<?= $page->id ?>" class="uk-width-xxlarge uk-margin-auto">
<div class="reset-password">
<?php
	include($config->paths->templates . "classes/PasswordReset.php");

	$controller = new PasswordReset();
	echo $controller->execute();
?>
</div>
<style>ul{list-style: none;}</style>
</section>
