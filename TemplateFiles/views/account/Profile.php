<?php namespace ProcessWire;
	if(!$user->isLoggedin()) {
		$session->redirect($config->urls->httpRoot . "user/login/");
	}
	if($user->wm_activation_code && !$user->isSuperuser()) {
		$session->redirect($config->urls->httpRoot . "user/activate/");
	}
?>

<section class='profile uk-background-default uk-padding'>
	<h1 class='profile-title'>
		<?= $user->name ?> 
		<a href='<?= $page->httpUrl ?>edit-profile' title='Edit Profile'>
			<i class='fa fa-cog' aria-hidden='true'></i>
		</a>
	</h1>
	<div class='profile uk-flex uk-flex-wrap'>
		<div class='uk-padding-small'>
			<?php if($user->wm_profile_image->first()) : ?>
			<img src='<?= $user->wm_profile_image->first()->size(190,190)->url ?>' class='user-image'>
			<?php endif; ?>
		</div>
		<div class='uk-padding-small'>
			<div class='uk-flex'>
				<div class='profile-info-left'><strong>Email</strong></div>
				<div class='profile-info-right'><?= $user->email ?></div>
			</div>
			<div class='uk-flex'>
				<div class='profile-info-left'><strong>Joined</strong></div>
				<div class='profile-info-right'><?= $user->wm_registration_date ?></div>
			</div>
		</div>
	</div>
	<h3>Manga Subscriptions</h3>
	<div class="manga-subscriptions">
		<?php $subscriptions = $pages->find("template=wm_manga_single, wm_manga_subs={$user->name}"); ?>
		<?php foreach($subscriptions as $manga) : ?>
			<div class="manga-subscription">
				<div class="subscription-link"><a href="<?= $manga->httpUrl ?>"><?= $manga->title ?></a></div>
				<form method="post" class="subscription-form unsubscribe">
					<input type="submit" name="manga_unsubscribe" class="manga-unsubscribe uk-button uk-button-danger" data-manga-id="<?= $manga->id ?>" value="Unsubscribe">
					<input type="hidden" name="manga_id" value="<?= $manga->id ?>">
				</form>
			</div>
		<?php endforeach; ?>
	</div>
</section>
