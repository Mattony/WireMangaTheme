<?php namespace ProcessWire; ?>
<!-- Latest Chapters -->
<h3>Latest Chapters</h3>
<section class="chapters-latest">
	<?php foreach($chapters as $chapter):
	    $editLink = "";
	    if($user->isSuperUser()) {
			$editLink  = "<a href='{$config->urls->admin}page/edit/?id={$chapter->id}' target='_blank' class='edit-link' title='Edit chapter {$chapter->title}'>";
			$editLink .= "<i class='fa fa-pencil-square-o' aria-hidden='true'></i>";
			$editLink .= "</a>";
	    }
	?>
	    <article id="p-<?= $chapter->id ?>" class="chapter uk-card uk-card-small uk-card-default">
			<div class="uk-card-body">
				<a href="<?= $chapter->url ?>">
					<img src="<?= $chapter->parent->wm_cover->first()->size(240,300)->url ?>" class="">
				</a>
			</div>
			<div class=" uk-card-footer">
				<h4 class="">
					<?= $editLink; ?>
					<a href="<?= $chapter->url ?>"><?= $chapter->parent->title ?> <?= $chapter->title ?></a>
				</h4>
			</div>
	    </article>
	<?php endforeach; ?>
</section>
<!--/ Latest Chapters -->

<!-- Latest Manga -->
<h3 class="uk-margin-large-top">Latest Manga</h3>
<section class="manga-latest">
	<?php $latest = $pages->find("template=wm_manga_single, sort=-created, limit=5, {$hideAdultManga}"); ?>
	<?php foreach($latest as $l) : 
	    $editLink = "";
	    if($user->isSuperUser()) {
			$editLink  = "<a href='{$config->urls->admin}page/edit/?id={$l->id}' target='_blank' class='edit-link' title='Edit {$l->title}'>";
			$editLink .= "<i class='fa fa-pencil-square-o' aria-hidden='true'></i>";
			$editLink .= "</a>";
	    }
	?>
		<div class="manga uk-card uk-card-small uk-card-default">
			<div class="uk-card-header">
				<h4 class="uk-card-title">
					<?= $editLink ?> <a href="<?=$l->url?>"><?=$l->title?></a>
				</h4>
			</div>
			<div class="uk-card-body">
				<a href="<?=$l->url?>"><img src="<?=$l->wm_cover->first()->size(200,0)->url?>"></a>
			</div>
		</div>
	<?php endforeach; ?>
</section>
<!--/ Latest Manga -->

<!-- Popular Manga -->
<h3 class="uk-margin-large-top">Most Popular Manga</h3>
<section class="manga-popular">
	<?php $popular = $pages->find("template=wm_manga_single, sort=-views, limit=5, {$hideAdultManga}"); ?>
	<?php foreach($popular as $p) : 
	    $editLink = "";
	    if($user->isSuperUser()) {
			$editLink  = "<a href='{$config->urls->admin}page/edit/?id={$p->id}' target='_blank' class='edit-link' title='Edit {$p->title}'>";
			$editLink .= "<i class='fa fa-pencil-square-o' aria-hidden='true'></i>";
			$editLink .= "</a>";
	    }
	?>
		<div class="manga uk-card uk-card-small uk-card-default">
			<div class="uk-card-header">
				<h4 class="uk-card-title">
					<?= $editLink ?> <a href="<?= $p->url?>"><?=$p->title?></a>
				</h4>
			</div>
			<div class="uk-card-body">
				<a href="<?=$p->url?>"><img src="<?=$p->wm_cover->first()->size(200,0)->url?>"></a>
			</div>
		</div>
	<?php endforeach; ?>
</section>
<!--/ Popular Manga -->