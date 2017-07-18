<?php namespace ProcessWire; ?>
<?php
$editLink = null;
if($user->isSuperUser()) {
	$editLink  = "<a href='{$config->urls->admin}page/edit/?id={$page->id}' class='edit-link' title='Edit {$page->title}'>";
	$editLink .= "<i class='fa fa-pencil-square-o' aria-hidden='true'></i>";
	$editLink .= "</a>";
}
?>
<article class="manga uk-article">

    <div class="manga-header uk-text-center">
		<h1 class="manga-title"><?= $editLink ?> <?= $page->title ?></h1>
		<?php if($alt_titles): ?>Alternative Titles: <?= $alt_titles ?><?php endif ?>
	</div>
	<hr class="uk-divider-icon">
    <div class="manga-block uk-grid uk-flex-center uk-margin-medium-bottom">
		<?php if($page->wm_cover->first()): ?>
	    <div class="manga-cover">
			<img src="<?= $page->wm_cover->first()->size(250,0)->url ?>">
		</div>
		<?php endif ?>

        <div class="manga-data wm-padding-left wm-padding-right">
			<?php if($authors): ?>
				<div class="data-block">
					<div class="data-name">Author</div>
					<div class="data-value"><?= $authors ?></div>
				</div>
			<?php endif ?>
			<?php if($artists): ?>
				<div class="data-block">
					<div class="data-name">Artist</div>
					<div class="data-value"><?= $artists ?></div>
				</div>
			<?php endif ?>
			<?php if($genres): ?>
				<div class="data-block">
					<div class="data-name">Genre</div>
					<div class="data-value"><?= $genres ?></div>
				</div>
			<?php endif ?>
			<?php if($type): ?>
				<div class="data-block">
					<div class="data-name">Type</div>
					<div class="data-value"><?= $type ?></div>
				</div>
			<?php endif ?>
			<?php if($mangaStatus): ?>
				<div class="data-block">
					<div class="data-name">Status</div>
					<div class="data-value"><?= $mangaStatus ?></div>
				</div>
			<?php endif ?>
			<?php if($scanlation): ?>
				<div class="data-block">
					<div class="data-name">Scanlation</div>
					<div class="data-value"><?= $scanlation ?></div>
				</div>
			<?php endif ?>
			<?php if($page->wm_sites->count) { ?>
				<div class="data-block">
					<div class="data-name">More</div>
					<div class="data-value">
						<?php
							$x = 1;
							foreach($page->wm_sites as $site) {
								$sep = ($x != $page->wm_sites->count) ? $sep = ", " : "";
								echo "<a href='{$site->wm_site_url}' target='_blank'>{$site->title}</a>{$sep}";
								$x++;
							}
						?>
					</div>
				</div>
			<?php } ?>
        </div>
    </div>

	<?php if($description): ?>
	<div class="manga-description uk-section-small uk-section-muted" uk-accordion>
		<div class="data-block ">
			<div class="data-name uk-accordion-title">Description</div>
			<div class="data-value uk-accordion-content"><?= $description ?></div>
		</div>
	</div>
	<?php endif ?>
	<?php if($fredi) $fredi->hideTabs("children|delete|settings")->chapters ?>
</article>

<div class="manga-toggles">
	<div class="manga-get-chapters <?= $chaptersIsActive ?>">Chapters</div>
	<div class="manga-get-comments <?= $commentsIsActive ?>">Comments</div>
</div>

<section class="uk-section-small uk-section-muted">
	<div class='manga-chapters <?= $chaptersIsActive ?>'>
		<?php if($user->isLoggedin()): ?>
			<?php if(!$page->wm_manga_subs->get($user)): ?>
				<div class="subscription uk-margin-bottom">
					<form method="post" class="subscribe-form">
						<input type="submit" name="manga_subscribe" class="subscribe uk-button uk-button-primary" value="Subscribe">
					</form>
				</div>
			<?php else: ?>
				<div class="uk-margin-bottom">
					<form method="post" class="unsubscribe-form">
						<input type="submit" name="manga_unsubscribe" class="unsubscribe uk-button uk-button-danger" value="Unsubscribe">
					</form>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?= $chapters ?>
	</div>
	<div class='manga-comments <?= $commentsIsActive ?>'><?= $comments ?></div>
	<?php

	?>
</section>
