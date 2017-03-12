<?php namespace ProcessWire; ?>

<h1>Search</h1>
<div class=''>
	<section class=''>
		<h2>Filters</h2>
		<form class="" action="" method="get">
			<input type="text" name="keywords" value="">
			<h3>Genres</h3>
			<?= $genres ?>
			<h3>Types</h3>
			<select multiple name="type[]">
				<?= $types ?>
			</select>
			<br><br>
			<button type="submit" name="submit" value="1">Search</button>
		</form>
	</section>

	<section class=''>
		<h2>Results</h2>
		<?php foreach($results as $m){ ?>

		<h3><a href="<?= $m->url ?>"><?= $m->title ?></a></h3>

		<?php } ?>
	</section>
</div>

<?= $selector ?>
