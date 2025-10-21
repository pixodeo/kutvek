<section class="row">
	<div class="col-s-12 col-l-9 col-center">	
		<?= $this->breadcrumb; ?>
		<?= $widgetCustom;  ?>	
		<div class="row">
			<div class="col-s-12  col-l-3 column-filter">		
			<!-- filters -->
			<?= $filters; ?>
			</div>			
			<div class="col-s-12 col-l-9">
				<?php  if($section_content && $section_content->designation):?>
				<h1 class="section-title"><?= $section_content->designation; ?></h1>
				<?php endif; ?>
				<?php if($section_content &&  $section_content->short): ?>	
				<h2 class="short-description"><?= $section_content->short; ?></h2>
				<?php endif; ?>	
				<div class="vehicle-info"><?= $section_content->vehicles_info ?? '' ;?></div>
				<!-- cards  and pagination -->
				
				<?= $content; ?>
				<?= $pagination; ?>
				<div class="mobile-custom">
					<?= $widgetCustom;  ?>
				</div>
			</div>
		</div>
		<div class="bottom-description"><?= $section_content->description ?? '';?></div>
		<!-- TrustBox widget - Slider -->
		<div class="row trustpilot-slider">
			<div class="col-s-12 col-m-9 col-m-center">
				<div class="trustpilot-widget bottom" data-locale="<?=$this->getLang();?>" data-template-id="54ad5defc6454f065c28af8b" data-businessunit-id="5c10d1e8416bce0001137d41" data-style-height="240px" data-style-width="100%" data-theme="light" data-stars="3,4,5">
					<a href="https://fr.trustpilot.com/review/kutvek-kitgraphik.com" target="_blank" rel="noopener">Trustpilot</a> 
				</div>
			</div>
		</div>
	</div>	
</section>
<template id="card-tpl">
	<figure class="product-item">
		<span class="offer"><img  src="/img/blank.png" alt="" /></span>
		<img loading="lazy" class="visual" />		
		<figcaption>
			<hr>
			<h3 class="item"></h3>
			<p class="amount"><span class="price block"></span><?= $rebate; ?></p>
		</figcaption>
		<a></a>
	</figure>
</template>