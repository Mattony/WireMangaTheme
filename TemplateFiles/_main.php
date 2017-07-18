<?php namespace ProcessWire; 
$pageWidth = $settings->wm_limit_width ? "" : "uk-container-expand";

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?= $seoTitle ?></title>
	<meta name="description" content="<?= $seoDesc ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noodp"/>
	<?= $headerAssets ?>
</head>

<body class="<?= $bodyClass ?>">
	<div class="page">
		<header class="header">
			<div class="uk-navbar uk-navbar-container uk-navbar-transparent">
				<div class="uk-navbar-left logo">
					<?= $logo ?>
				</div>
				<nav class="menu-wrapper">
					<div class="menu-toggle">
						<i class="fa fa-bars" aria-hidden="true"></i>
					</div>
					<?= $menu ?>
				</nav>

				<div class="header-search uk-navbar-item uk-navbar-right">
					<form action="<?= $config->urls->httpRoot ?>search/" method="get" class="header-search-form uk-width-1 uk-flex">
						<input type="text" name="s" class="header-search-input uk-input" placeholder="Search ...">
					</form>
					<i class="fa fa-search header-search-toggle" aria-hidden="true"></i>
				</div>
				<ul class="header-search-results"></ul>
			</div>
		</header>

		<main id="p-<?= $page->id ?>" class="content uk-container <?= $pageWidth ?>">
			<section class="main-content uk-width-1-1">
				<?= $content ?>
			</section>
		</main>
		<div class="ajax-messages"></div>
	
		<footer class="footer uk-section-small uk-section-secondary">
			<?= $footer ?>
		</footer>
	</div>

	<?= $footerAssets ?>
</body>
</html>