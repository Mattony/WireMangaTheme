<?php namespace ProcessWire;
$sidebarClass = $settings->wm_fixed_width ? "uk-width-1-4@l" : "uk-width-1-5@l"; ?>

<aside class='secondary-content sidebar <?= $sidebarClass ?>'>

<!-- // Latest Manga -->
<section class='widget uk-margin-large-bottom'>
<h3 class=''>Latest Manga</h3>
<ul class=''uk-grid>
<?php
$popular = $pages->find("template=wm_manga_single, sort=-created, limit=5");
foreach($popular as $p) {
?>
	<div class='uk-width-1-2@s uk-width-1-4@m uk-width-1-1@l'><div class='uk-card uk-card-default uk-card-small uk-margin-small-bottom'>
		<div class='uk-card-header'><h4 class='uk-card-title'><a href='<?=$p->url?>'><?=$p->title?></a></h4></div>
		<div class='uk-card-body uk-text-center'><a href='<?=$p->url?>'><img src='<?=$p->wm_cover->first()->size(200,0)->url?>'></a></div>
	</div></div>
<?php } ?>
</ul>
</section>

<!-- // Popular Manga -->
<section class='widget'>
<h3 class=''>Most Popular Manga</h3>
<div class='' uk-grid>
<?php
$popular = $pages->find("template=wm_manga_single, sort=-views, limit=5");
foreach($popular as $p) {
?>
	<div class='uk-width-1-2@s uk-width-1-4@m uk-width-1-1@l'><div class='uk-card uk-card-default uk-card-small uk-margin-bottom'>
		<div class='uk-card-header'><h4 class='uk-card-title'><a href='{$p->url}'><?=$p->title?></a></h4></div>
		<div class='uk-card-body uk-text-center'><a href='<?=$p->url?>'><img src='<?=$p->wm_cover->first()->size(200,0)->url?>'></a></div>
	</div></div>
<?php } ?>
</div>
</section>

</aside>
