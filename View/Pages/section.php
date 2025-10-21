<section class="row">
	<div class="col-s-12 col-l-11 col-xl-10 col-center">	
		<?= $this->breadcrumb; ?>	
		<?= $widgetCustom;  ?>				
		<div class="widgets">
			<?php foreach($query->widgets as $widget): $link =  implode('/', array_filter([FQDN, $widget->link])); ?>
				<a class="widget" href="<?= $link;?>"><img src="<?=$widget->img?>" alt=""></a>
			<?php endforeach; ?>
		</div>
		<div class="row">
			<div class="col-s-12  col-l-3 column-filter">		
			<!-- filters -->
			<?= $filters; ?>
			</div>			
			<div class="col-s-12 col-l-9">
				<?php  if($section_content && $section_content->designation):?>
				<h1 class="section-title"><?= $section_content->designation; ?></h1>
				<?php endif; ?>
				<?php if($section_content && $section_content->short): ?>	
				<div class="short-description"><?= $this->specialchars_decode($section_content->short); ?></div>
				<?php endif; ?>	
				<div class="vehicle-info"><?= $this->specialchars_decode($section_content->vehicles_info ?? '');?></div>
				<!-- liens sous-categories maillage -->
				<?php if(count($query->children) > 0):?>
					<div class="links-group">
						<?php foreach($query->children as $link): ?>
							<?php if($link->obfuscation > 0):?>
								<span class="obflink obf link" data-obf="<?=base64_encode($link->_url)?>" ><?=$link->designation;?></span>
							<?php else: ?>
								<a class="link" href="<?=$link->_url;?>"><?=$link->designation;?></a>
							<?php endif; ?>							
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<!-- cards  and pagination -->
				<?= $content; ?>
				<?= $pagination; ?>
				<div class="mobile-custom">
					<?= $widgetCustom;  ?>
				</div>
				<?php if(count($query->siblings) > 0):?>
					<div class="links-group">
						<?php foreach($query->siblings as $link): ?>
							<?php if($link->obfuscation > 0):?>
								<span class="obflink obf link" data-obf="<?=base64_encode($link->_url)?>" ><?=$link->designation;?></span>
							<?php else: ?>
								<a class="link" href="<?=$link->_url;?>"><?=$link->designation;?></a>
							<?php endif; ?>							
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>		
		<div class="bottom-description">
			<?= $this->specialchars_decode($section_content->description);?>		
		</div>
		<hr>
		<?= $this->specialchars_decode($section_content->faq);?>
		<!-- TrustBox widget - Slider -->
		<div class="row trustpilot-slider">
			<div class="col-s-12 col-l-10 col-m-center">
				<?= $this->app->widgetTrustpilot('slider'); ?>
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
			<p class="amount">
				<span class="price block"></span>
				<?= $rebate ;?>
			</p>
		</figcaption>
		<a></a>
	</figure>
</template>