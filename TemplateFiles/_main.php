<?php namespace ProcessWire; 
$pageWidth = $settings->wm_fixed_width ? "uk-container" : "uk-container-expand";
if($hasSidebar) {
	$mainContentClass = $settings->wm_fixed_width ? "uk-width-3-4@l" : "uk-width-4-5@l";
} else {
	$mainContentClass = "uk-width-1-1";
}

$menuWidth = $settings->wm_fixed_width ? "uk-container" : "";

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
	<div class="page uk-container <?= $pageWidth ?>">
		<header class="header">
			<div class="uk-light uk-background-secondary">
				<nav class="header--nav uk-navbar <?= $menuWidth ?>" uk-navbar>
					<div class="header--menu-toggle off uk-navbar-left">
						<a class="uk-navbar-toggle" uk-navbar-toggle-icon href="#"></a>
					</div>
					<?= $smallScreenMenu ?>
					<?= $largeScreenMenu ?>
					<div class="header-search uk-navbar-item uk-navbar-right">
						<i class="fa fa-search header-search--toggle js-search-toggle" aria-hidden="true"></i>
						<form action="javascript:void(0)" method="get" class="header-search--form">
							<input type="text" name="s" class="header-search--input js-search-input uk-input" placeholder="search">
							<noscript><input type="submit" name="search" class="header-search--submit js-search-submit uk-button uk-button-default" value="Search"></noscript>
						</form>
					</div>
				</nav>
			</div>
			<ul class="header-search--results js-search-results"></ul>
		</header>

		<main id="p-<?= $page->id ?>" class="content uk-flex uk-flex-wrap" uk-grid>
			<section class="main-content <?= $mainContentClass ?>">
				<noscript>Enable JS</noscript>
				<?= $content ?>
			</section>
			<?= $sidebar ?>
		</main>
		<div class="ajax-messages"></div>
	</div>
	
	<footer class="footer uk-section-small uk-section-secondary uk-margin-large-top">
		<?= $footer ?>
	</footer>

	<?= $footerAssets ?>
</body>
</html>