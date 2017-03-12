<?php namespace ProcessWire; ?>
<?php //var_dump($page); ?>
<article class="manga uk-article">

    <div class="manga--header uk-text-center">
		<h1 class="uk-heading-primary"><?= $page->title ?></h1>
	</div>
	<hr class="uk-divider-icon">
    <div class="manga--block uk-flex-center uk-margin-medium-bottom" uk-grid>
		<?php if($page->wm_cover->first()): ?>
	    <div class="manga--cover">
			<img src="<?= $page->wm_cover->first()->size(250,0)->url ?>">
		</div>
		<?php endif ?>

        <div class="manga--data wm-padding-left wm-padding-right">
			<?php if($alt_titles): ?>
				<div class="data--block">
					<div class="data--name"><abbr title="Alternative Titles">Alt. titles</abbr></div>
					<div class="data--value"><?= $alt_titles ?></div>
				</div>
			<?php endif ?>
			<?php if($authors): ?>
				<div class="data--block">
					<div class="data--name">Author</div>
					<div class="data--value"><?= $authors ?></div>
				</div>
			<?php endif ?>
			<?php if($artists): ?>
				<div class="data--block">
					<div class="data--name">Artist</div>
					<div class="data--value"><?= $artists ?></div>
				</div>
			<?php endif ?>
			<?php if($genres): ?>
				<div class="data--block">
					<div class="data--name">Genre</div>
					<div class="data--value"><?= $genres ?></div>
				</div>
			<?php endif ?>
			<?php if($type): ?>
				<div class="data--block">
					<div class="data--name">Type</div>
					<div class="data--value"><?= $type ?></div>
				</div>
			<?php endif ?>
			<?php if($mangaStatus): ?>
				<div class="data--block">
					<div class="data--name">Status</div>
					<div class="data--value"><?= $mangaStatus ?></div>
				</div>
			<?php endif ?>
			<?php if($scanlation): ?>
				<div class="data--block">
					<div class="data--name">Scanlation</div>
					<div class="data--value"><?= $scanlation ?></div>
				</div>
			<?php endif ?>
			<?php if($page->wm_external_sites->count) { ?>
				<div class="data--block">
					<div class="data--name">More</div>
					<div class="data--value">
						<?php
							$x = 1;
							foreach($page->wm_sites as $site) {
								$sep = ($x != $page->wm_sites->count) ? $sep = ", " : "";
								echo "<a href='{$site->wm_site_url}'>{$site->title}</a>{$sep}";
								$x++;
							}
						?>
					</div>
				</div>
			<?php } ?>
        </div>
    </div>

	<?php if($description): ?>
	<div class="manga--description uk-section-small uk-section-muted" uk-accordion>
		<div class="data--block ">
			<div class="data--name uk-accordion-title">Description</div>
			<div class="data--value uk-accordion-content"><?= $description ?></div>
		</div>
	</div>
	<?php endif ?>
	<?php if($fredi) $fredi->hideTabs("children|delete|settings")->chapters ?>
</article>

<div class="manga--toggles">
	<div class="manga--get-chapters js-active">Chapters</div>
	<div class="manga--get-comments">Comments</div>
</div>
<section class="uk-section-small uk-section-muted">
	<div class="manga--chapters-comments wm-padding-left wm-padding-right">
		<?php echo $chapters ?>
		<?php // echo $comments ?>
	</div>
</section>
