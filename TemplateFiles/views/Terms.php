<?php namespace ProcessWire;

echo $pagination; ?>
<h1><?= $page->parent->title ?>: <?= $page->title ?></h1>
<div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@xl uk-text-center" uk-grid>

<?php
foreach($results as $manga):
    $editLink = "";
    if($user->isSuperUser())
    {
        $editLink  = "<a href='" . $config->urls->admin . "page/edit/?id={$manga->id}' target='_blank' class='edit-link' title='Edit {$manga->title}'>";
		$editLink .= "<i class='fa fa-pencil-square-o' aria-hidden='true'></i>";
		$editLink .= "</a>";
    } ?>

	<div>
	    <article id="page-<?= $manga->id?>" class="uk-card uk-card-default">

			<div class="uk-card-header">
				<h2><?= $editLink ?> <a href="<?= $manga->url ?>"><?= $manga->title ?></a></h2>
			</div>
	        <div class="uk-card-body">
				<a href="<?= $manga->url ?>">
					<img src="<?= $manga->wm_cover->first()->size(250,300)->url ?>">
				</a>
			</div>
    		<div class="uk-card-footer"><?php echo getTerms($manga->wm_genres, "", "uk-label") ?></div>
	    </article>
	</div>

<?php endforeach; ?>
</div>
<?= $pagination ?>
