<?php namespace ProcessWire; ?>

<div class='manga-search'>
	<form action="<?= $config->urls->httpRoot ?>search/" method="get" class="header-search-form uk-width-1 uk-flex">
		<input type="text" name="s" class="header-search-input js-search-input uk-input" placeholder="Search ...">
	</form>
	<h1>Search Results</h1>
	<section class='search-results'>
		<?php foreach($results as $manga) {
			$editLink = null;
			if($user->isSuperUser()) {
				$editLink  = "<a href='{$config->urls->admin}page/edit/?id={$manga->id}' target='_blank' class='edit-link' title='Edit {$manga->title}'>";
				$editLink .= "<i class='fa fa-pencil-square-o' aria-hidden='true'></i>";
				$editLink .= "</a>";
			}
		?>
		<div class="manga uk-card uk-card-small uk-card-default">
			<div class="uk-card-header">
				<h4 class="uk-card-title">
					<?= $editLink ?> <a href="<?=$manga->url?>"><?=$manga->title?></a>
				</h4>
			</div>
			<div class="uk-card-body">
				<a href="<?=$manga->url?>"><img src="<?=$manga->wm_cover->first()->size(200,0)->url?>"></a>
			</div>
		</div>
		<?php } ?>
	</section>
</div>


