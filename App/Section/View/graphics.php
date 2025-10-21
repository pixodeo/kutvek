<?php $cards = $this->getCards(); ?>
<section class="row">
	<div class="col-s-12 col-l-11 col-xl-10 col-center">
		<?= $this->getBreadcrumb(); ?>		
		<?= $widgetCustom;  ?>
		<div class="row">
			<div class="col-s-12  col-l-3 column-filter">		
			<!-- filters -->
			<?= $this->filters(); ?>
			</div>			
			<div class="col-s-12 col-l-9">				
				<h1 class="section-title"><?= $this->getContent()->designation; ?></h1>	
				<div class="short-description"><?= $this->specialchars_decode($this->getContent()->short_desc);?></div>			
				<!-- links to sub-sections -->
				<div class="sub-section">

				</div>
				<div class="vehicle-info"><?= $this->specialchars_decode($this->getContent()->further_info);?>	</div>
				<div id="products" class="product-item-container grid-cards">
					<?php foreach ($cards as $key => $card) : ?>
						<figure id="<?= $card->id; ?>" class="product-item"  data-design="<?= $card->design_id; ?>" data-color="<?= $card->color_id; ?>">
							<span class="offer"><img  src="<?=$card->offerPicto?? '';?>" alt="" /></span>
							<?php if (isset($card->visual)) : ?>
								<img loading="lazy" class="visual" src="<?= $card->visual; ?>" />
							<?php else : ?>
								<img loading="lazy" class="visual" src="/img/blank.png" />
							<?php endif; ?>
							
							<figcaption>
								<hr>
								<p class="item"><?= $card->designation; ?></p>
								<p class="amount">
									<span class="price block"><?= $card->priceMin ? $card->priceMin . ' - ' : '' ?><?= $card->priceMax; ?></span>	
									<?= $this->getRebate();?>
								</p>
							</figcaption>
							<a href="<?= $card->url; ?>"></a>
						</figure>
					<?php endforeach; ?>
				</div>				
				<div class="mobile-custom">
					<?= $widgetCustom; ?>
				</div>				
				<?=$this->getPagination();?>				
			</div>
		</div>		
		<div class="bottom-description">
			<?= $this->specialchars_decode($this->getContent()->description); ?>		
		</div>
		<hr>
		<div class="faq">
			<?= $this->specialchars_decode($this->getContent()->faq); ?>		
		</div>
		<!-- TrustBox widget - Slider -->
		<div class="row trustpilot-slider">
			<div class="col-s-12 col-l-10 col-m-center">
				
			</div>
		</div>
	</div>	
</section>
<p><?=$this->getContent()->category;?></p>
<?php if(isset($_GET['dev'])): ?>
<?= $this->getRequest()->getUri()->getPath(); ?>

<pre><?php print_r($this->categoryChilds());?></pre>
<?php endif; ?>
<template id="card-tpl">
	<figure class="product-item">
		<span class="offer"><img  src="/img/blank.png" alt="" /></span>
		<img loading="lazy" class="visual" />		
		<figcaption>
			<hr>
			<h3 class="item"></h3>
			<p class="amount">
				<span class="price block"></span>
				
			</p>
		</figcaption>
		<a></a>
	</figure>
</template>
