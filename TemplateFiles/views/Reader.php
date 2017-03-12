<?php namespace ProcessWire; ?>
<?php //echo "<pre>" . print_r( $reader->imagesCount(), true) . "</pre>"; ?>
<div class="reader uk-text-center">
	<div class="uk-display-inline-block">
		<div class="reader--header uk-margin-small-bottom">
			<div class="reader--nav reader--nav-left">
				<a href="<?= $reader->prevPage() ?>" title="Previous Page"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
				<a href="<?= $page->parent->url ?>"  title="Manga Page"><i class="fa fa-home" aria-hidden="true"></i></a>
				<a href="<?= $reader->nextPage() ?>" title="Next Page"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
			</div>
			<div class="reader--nav reader--nav-right uk-flex">
				<div class="reader--chapters uk-margin-left">
					<?= $reader->chaptersList() ?>
				</div>
				<div class="reader--pages uk-margin-left">
					<?= $reader->pagesList() ?>
				</div>
			</div>
		</div>
		<div class="reader--image uk-text-center">
			<a href="<?= $reader->nextPage() ?>" class="reader--image-link">
				<img src="<?= $reader->imageSrc() ?>">
			</a>
		</div>
	</div>
</div>
