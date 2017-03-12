<?php namespace ProcessWire;
if(!$user->isLoggedin()) {
	$session->redirect($config->urls->httpRoot . "user/login/");
}
if($user->wm_account_status !== "active") {
	$session->redirect($config->urls->httpRoot . "user/activate/");
}

$u = $pages->get("template=user, name={$user->name}");

?>
<section class='profile uk-background uk-padding'>
	<h1 class='profile--title'><?= $user->name ?> <a href='<?= $page->parent->httpUrl ?>edit-profile' class='' title='Edit Profile'><i class='fa fa-cog' aria-hidden='true'></i></a></h1>
	<div class='uk-flex uk-flex-wrap'>
		<div class='uk-padding-small'>
			<?php if($u->wm_profile_image->first()) : ?>
			<img src='<?= $u->wm_profile_image->first()->size(190,190)->url ?>' class=''>
			<?php endif; ?>
		</div>
		<div class='uk-padding-small'>
			<div class='uk-flex'>
				<div class='profile--info-left'><strong>Email</strong></div>
				<div class='profile--info-right'><?= $u->email ?></div>
			</div>
			<div class='uk-flex'>
				<div class='profile--info-left'><strong>Joined</strong></div>
				<div class='profile--info-right'><?= $u->wm_registration_date ?></div>
			</div>
		</div>
	</div>
</section>
