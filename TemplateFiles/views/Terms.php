<?php namespace ProcessWire;

echo $pagination; ?>
<h1><?= $page->parent->title ?>: <?= $page->title ?></h1>
<div class="manga-latest uk-text-center">
<?php
$editLink = null;
foreach($results as $manga):
    if($user->isSuperUser()) {
        $editLink  = "<a href='" . $config->urls->admin . "page/edit/?id={$manga->id}' target='_blank' class='edit-link' title='Edit {$manga->title}'>";
		$editLink .= "<i class='fa fa-pencil-square-o' aria-hidden='true'></i>";
		$editLink .= "</a>";
    }
?>
	<article id="page-<?= $manga->id?>" class="manga uk-card uk-card-small uk-card-default">

		<div class="uk-card-header">
			<h2><?= $editLink ?> <a href="<?= $manga->url ?>"><?= $manga->title ?></a></h2>
		</div>
		<div class="uk-card-body">
			<a href="<?= $manga->url ?>">
				<img src="<?= $manga->wm_cover->first()->size(250,300)->url ?>">
			</a>
		</div>
		<div class="uk-card-footer"><?php echo getTerms($manga->wm_genre, "", "uk-label") ?></div>
	</article>
<?php endforeach; ?>
</div>
<?= $pagination ?>
