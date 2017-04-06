<?php namespace ProcessWire; ?>
<?php //echo "<pre>" . print_r( $reader->imagesCount(), true) . "</pre>"; ?>
<div class="reader uk-text-center">
	<div class="uk-display-inline-block">
		<div class="reader--header uk-margin-small-bottom">
			<div class="reader--nav reader--nav-left">
				<a href="<?= $page->parent->url ?>"  title="Manga Page"><i class="fa fa-home" aria-hidden="true"></i></a>
			</div>
			<div class="reader--nav reader--nav-right uk-flex">
				<div class="reader--chapters uk-margin-left">
					<?= $reader->chaptersList() ?>
				</div>
			</div>
		</div>
		<div class="reader--image uk-text-center">
            <?php foreach($page->wm_chapter_images as $img){ ?>
                <div class="uk-margin-bottom"><img src="<?= $img->url ?>"></div>
            <?php } ?>
		</div>
	</div>
</div>