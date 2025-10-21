<section class="row">
	<div class="col-s-12 col-l-11 col-xl-10 col-center">
		<nav aria-label="breadcrumb"><ol class="breadcrumb"></ol></nav>
		<div class="widget-custom">
		<a href="<?=$this->uri('perso-housses')?>">
		<picture>
			<source srcset="/img/section/<?=$section_content->cover;?>" type="image/webp" media="(orientation: landscape)">
			<source srcset="/img/section/square/<?=$section_content->cover;?>" media="(orientation: portrait)" type="image/webp">
			<source srcset="/img/section/square/<?=str_replace('webp', 'jpg', $section_content->cover);?>" media="(orientation: portrait)" type="image/jpg">
			<img src="/img/section/<?=str_replace('webp', 'jpg', $section_content->cover);?>" id="img-visual">
		</picture>
		</a>
		</div>
		<div class="row">
			<div class="col-s-12  col-l-3 column-filter"><!-- filters -->
				
			</div>
			<div class="col-s-12 col-l-9">
				<?php if ($section_content && $section_content->designation) : ?>
					<h1 class="section-title"><?= $section_content->designation; ?></h1>
				<?php endif; ?>
				<?php if ($section_content && $section_content->short) : ?>
					<h2 class="short-description"><?= $section_content->short; ?></h2>
				<?php endif; ?>
				<div class="vehicle-info"><?= $section_content->vehicles_info ?? ''; ?></div>
				<!-- cards  and pagination -->
				<?= $pagination;?>
				<?= $content;?>
				<?= $pagination;?>
				<div class="mobile-custom">
					<div class="widget-custom">
						<a href="<?=$this->uri('perso-housses')?>">
						<picture>
							<source srcset="/img/section/<?=$section_content->cover;?>" type="image/webp" media="(orientation: landscape)">
							<source srcset="/img/section/square/<?=$section_content->cover;?>" media="(orientation: portrait)" type="image/webp">
							<source srcset="/img/section/square/<?=str_replace('webp', 'jpg', $section_content->cover);?>" media="(orientation: portrait)" type="image/jpg">
							<img src="/img/section/<?=str_replace('webp', 'jpg', $section_content->cover);?>" id="img-visual">
						</picture>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom-description">
			<?= $section_content->description ?? ''; ?>
		</div>
		<!-- TrustBox widget - Slider -->
		<div class="row trustpilot-slider">
			<div class="col-s-12 col-m-9 col-m-center">
				<?= $this->app->widgetTrustpilot('slider'); ?>
			</div>
		</div>
	</div>
</section>

<template id="card-tpl">
	<figure class="product-item">
		<img loading="lazy" class="visual" />
		<figcaption>
			<hr>
			<h3 class="item"></h3>
			<p><span class="price block"></span></p>
		</figcaption>
		<a></a>
	</figure>
</template>