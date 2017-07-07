<?php namespace ProcessWire; ?>
<div class="directory">
	<?php
		// Loop over the $aToZ array
		// and build the letter navigation
		$letternav = null;
		foreach($aToZ as $letter) {
			if($letter === "#") {
				$class = ($input->urlSegment1 === "number") ? " uk-background-secondary" : null;
				$letternav .= "<a href='{$page->url}number/' class='uk-label uk-margin-small-right{$class}'>{$letter}</a>";
				continue;
			}
			$class = ($input->urlSegment1 === $letter) ? " uk-background-secondary" : null;
			$letternav .= "<a href='{$page->url}{$letter}/' class='uk-label uk-margin-small-right{$class}'>{$letter}</a>";
		}
	?>
	<div class="directory-letters uk-margin-small-bottom"><?= $letternav ?></div>
	<div class="directory-list">
		<?php
			// Loop over the manga
			// and build the manga list
			$out = $noResults;
			foreach($results as $m) {
				$out .= "<article id='{$m->id}' class='directory-manga js-hidden'>";
					$out .= "<div class='dir-manga-info js-toggle' data-id='{$m->id}'><i class='fa fa-info-circle' aria-hidden='true'></i></div>";
					$out .= "<h2 class='dir-manga-title uk-margin-remove'><a href='{$m->url}'>{$m->title}</a></h2>";
					$out .= "<div class='dir-manga-content uk-grid'></div>";
				$out .= "</article>";
			}
			echo $out;
		?>
	</div>
<?= $pagination ?>
