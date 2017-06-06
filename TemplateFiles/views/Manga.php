<?php namespace ProcessWire; ?>
<div class="directory">
	<?php
		// Loop over the $aToZ array
		// and build the letter navigation
		$letternav = "";
		foreach($aToZ as $letter) {
			$letternav .= "<a href='{$page->url}?q={$letter}' class='uk-label uk-margin-small-right'>{$letter}</a>";
		}
	?>
	<div class="directory-letters uk-margin-small-bottom"><?= $letternav ?></div>
	<div class="directory-list">
	<?php
		// Loop over the manga
		// and build the manga list
		$out = $noResults;
		foreach($results as $m) {
			$out .= "<article id='{$m->id}' class='directory-manga'>";
				$out .= "<div class='js-manga-info js-show' data-id='{$m->id}'><i class='fa fa-info-circle' aria-hidden='true'></i></div>";
				$out .= "<h2 class='directory-title uk-margin-remove'><a href='{$m->url}'>{$m->title}</a></h2>";
				$out .= "<div class='js-directory-ajax-content directory-content uk-grid'></div>";
			$out .= "</article>";
		}
		echo $out;
	?>
</div>
<?= $pagination ?>
