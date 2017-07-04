<?php namespace ProcessWire; ?>

<div class="reader uk-text-center">
	<div class="uk-display-inline-block" style="max-width: <?= $width ?>px">
		<div id="reader-header" class="reader-header">
			<div class="reader-nav reader-nav-left">
				<a href="#reader-settings" uk-toggle><i class="fa fa-wrench uk-margin-right" aria-hidden="true"></i></a>
				<?= $prev_page ?>
				<a href="<?= $page->parent->url ?>"  title="Manga Page"><i class="fa fa-home" aria-hidden="true"></i></a>
				<?= $next_page ?>
			</div>

			<div class="reader-nav reader-nav-right uk-flex">
				<div class="reader-chapters uk-margin-left">
					<?= $reader->chaptersList() ?>
				</div>
				<div class="reader-pages uk-margin-left">
					<?= $pagesList ?>
				</div>
			</div>
		</div>
		<div class="reader-image uk-text-center">
			<?php
			if($show_all_ch_images) {
				foreach($page->wm_chapter_images->sort("name") as $img) {
					echo "<div class='img-wrapper uk-margin-bottom'><img data-src='{$img->url}' class='lazyload'></div>";
				}
			}
			else {
				echo "<a href='{$reader->nextPage()}#reader-header' class='reader-image-link'><img src='{$reader->imageSrc()}'></a>";
			}
			?>
		</div>
	</div>
</div>

<div id="reader-settings" uk-modal>
	<div class="uk-modal-dialog">
		<form method="post">
			<div class="uk-modal-header">
				<h2 class="uk-modal-title">Reader Settings</h2>
			</div>
			<div class="uk-padding">
				<label for="show_all_ch_images" class="uk-form-label">Select Reading Mode:</label>
				<select name="show_all_ch_images" id="show_all_ch_images" class="uk-select uk-margin-bottom">
					<?php foreach($options as $k => $o) {
						$selected = ($show_all_ch_images == $k) ? "selected='selected'" : "";
						echo "<option value='{$k}' {$selected}>{$o}</option>";
					} ?>
				</select><br>
				<label for="max_img_width" class="uk-form-label">Set Max Image Width:</label>
				<input type="text" name="max_img_width" value="<?= $width ?>" id="max_img_width" class="uk-input"><br>
			</div>
			<div class="uk-modal-footer uk-text-right">
				<button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
				<input type="submit" name="submit_rs" value="Save" class="uk-button uk-button-primary">
			</div>
		</form>
    </div>
</div>
<!-- reader settings -->