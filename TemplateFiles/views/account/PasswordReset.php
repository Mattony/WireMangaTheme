<?php namespace ProcessWire;
if($user->isLoggedin())
	$session->redirect($config->urls->httpRoot);
?>
<section id="page-<?= $page->id ?>" class="uk-width-xxlarge uk-margin-auto">
<div class="reset-password">
<?php
	$controller = new ProcessController();
	$controller->setProcessName('ProcessForgotPassword');
	echo $controller->execute();
?>
</div>
<script>
	$(".reset-password input").addClass("uk-input");
	$(".reset-password label").addClass("uk-form-label");
	$(".reset-password .description").addClass("uk-text-meta");
	$(".reset-password button").addClass("uk-input uk-margin-top");
</script>
</section>
