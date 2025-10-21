<?php 
use App\Component\Sales\Cards;
$behavior = new Cards;
$behavior->setI18n($this->getI18n());
$behavior->setRouter($this->_router);
$behavior->setCurrency($this->app->getCurrency());
$behavior->setCountry($this->app->getCountry());
$cards = $behavior->cards();
if(empty($cards)){
	$pagination = '';
	$filters = '';
}else{
	$filters = $behavior->partial('View.filters', compact('cards', 'slug'));
$pagination = $this->partial('partials.paginate', ['page' => $behavior->getCurrentPage(), 'pages' => $behavior->getPages()]);
}


?>
<section class="row">
	<div class="col-s-12 col-l-11 col-xl-10 col-center">
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">		    
		  </ol>
		</nav>
		<div class="row">
			<div class="col-s-12  col-l-3 column-filter">		
			<!-- filters -->
			<?= $filters; ?>
			</div>			
			<div class="col-s-12 col-l-9">
				<header class="page-header">
					<picture >					
						<source srcset="<?= $page->cover ?>" type="image/webp" media="(orientation: landscape)" />
						<source srcset="<?= $page->cover_portrait ?>" media="(orientation: portrait)" type="image/webp" />
						<source srcset="<?= str_replace('webp', 'jpg', $page->cover_portrait)?>" media="(orientation: portrait)" type="image/jpg"/>
						<img src="<?= str_replace('webp', 'jpg', $page->cover) ?>">					
					</picture>
					<?php  if($page && $page->title):?>
					<h1 class="section-title"><?= $page->title; ?></h1>
					<?php endif; ?>
					<?php if($page && $page->short_description): ?>	
					<h2 class="short-description"><?= $page->short_description; ?></h2>
					<p><?=$page->content;?></p>
					<?php endif; ?>	
				</header>

				<div id="products" class="product-item-container">
				<?php foreach ($cards as $key => $card) : ?>
					<figure id="<?= $card->id; ?>" class="product-item" data-brand="<?= $card->brand; ?>" data-design="<?= $card->design; ?>" data-color="<?= $card->color; ?>">
						<span class="offer"><img  src="<?=$card->offerPicto;?>" alt="" /></span>
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
							
								
							</p>
						</figcaption>
						<a href="<?= $card->url; ?>"></a>
					</figure>
				<?php endforeach; ?>
			</div>

						
				<?= $pagination; ?>
				
			</div>
		</div>		
		<div class="bottom-description">
					
		</div>
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
			<?php if(isset($_GET['debug'])): ?>
			
			<pre><?php print_r($cards)?></pre>
			<?php endif; ?>