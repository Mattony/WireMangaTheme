<?php namespace ProcessWire; ?>
<?php
if(!$show_all_chapter_images) {
	$prev_page = "<a href='{$reader->prevPage()}' title='Previous Page'><i class='fa fa-chevron-left' aria-hidden='true'></i></a>";
	$next_page = "<a href='{$reader->nextPage()}' title='Next Page'><i class='fa fa-chevron-right' aria-hidden='true'></i></a>";
}

$options = ["One image per page", "All chapter images on one page"];

?>
<div id="reader-settings" uk-modal>
	<div class="uk-modal-dialog">
		<form method="post">
			<div class="uk-modal-header">
				<h2 class="uk-modal-title">Reader Settings</h2>
			</div>
			<div class="uk-padding">
				<label for="display_mode" class="uk-form-label">Select reading mode:</label>
				<select name="display_mode" id="display_mode" class="uk-select uk-margin-bottom">
					<?php foreach($options as $k => $o) {
						$selected = ($show_all_chapter_images == $k) ? "selected='selected'" : "";
						echo "<option value='{$k}' {$selected}>{$o}</option>";
					} ?>
				</select><br>
			</div>
			<div class="uk-modal-footer uk-text-right">
				<button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
				<input type="submit" name="submit_rs" value="Save" class="uk-button uk-button-primary">
			</div>
		</form>

    </div>
</div>

<div class="reader uk-text-center">
	<div class="uk-display-inline-block">
		<div class="reader--header uk-margin-small-bottom">
			<div class="reader--nav reader--nav-left">
				<a href="#reader-settings" uk-toggle><i class="fa fa-wrench uk-margin-right" aria-hidden="true"></i></a>
				<?= $prev_page ?>
				<a href="<?= $page->parent->url ?>"  title="Manga Page"><i class="fa fa-home" aria-hidden="true"></i></a>
				<?= $next_page ?>
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
			<?php
			if($show_all_chapter_images) {
				foreach($page->wm_chapter_images as $img) {
					echo "<div class='uk-margin-bottom'><img src='{$img->url}'></div>";
				}
			}
			else {
				echo "<a href='{$reader->nextPage()}' class='reader--image-link'><img src='{$reader->imageSrc()}'></a>";
			}
			?>
		</div>
	</div>
</div>
