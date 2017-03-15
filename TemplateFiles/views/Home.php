<?php namespace ProcessWire; 
$childWidth = $settings->wm_fixed_width ? "uk-child-width-1-1" : "uk-child-width-1-2@s"; ?>

<div class=" <?= $childWidth ?>" uk-grid>
	<?php foreach($chapters as $chapter):
	    $editLink = "";
	    if($user->isSuperUser()) {
			$editLink  = "<a href='" . $config->urls->admin . "page/edit/?id={$chapter->id}' target='_blank' class='edit-link' title='Edit chapter {$chapter->title}'>";
			$editLink .= "<i class='fa fa-pencil-square-o' aria-hidden='true'></i>";
			$editLink .= "</a>";
	    }
		$authors = getTerms($chapter->parent->wm_authors, ", ", "");
		$genre = getTerms($chapter->parent->wm_genres, ", ");
	?>
	<div>
	    <article id="p-<?= $chapter->id ?>" class="uk-card uk-card-default">
			<div class="uk-padding-small" uk-grid>
	        <div class="">
	            <a href="<?= $chapter->url ?>">
					<img src="<?= $chapter->parent->wm_cover->first()->size(100,110)->url ?>" class="">
				</a>
	        </div>
	        <div class="">
	            <h2 class="">
					<?= $editLink; ?>
					<a href="<?= $chapter->url ?>"><?= $chapter->parent->title ?> <?= $chapter->title ?></a>
				</h2>
	            <div class="">
					by <?= $authors ?>
				</div>
	        </div>
			</div>
	    </article>
	</div>
	<?php endforeach; ?>
</div>
