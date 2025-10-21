<nav role="navigation" aria-label="breadcrumb">
	  <ol  itemscope itemtype="https://schema.org/BreadcrumbList"  class="breadcrumb">
	    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
	    	<a href="<?=$this->url('pages.index');?>" itemprop="item"><span data-i18n="homepage">Accueil</span></a>
	    	<meta itemprop="position" content="1" />
	    </li>         
	    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"class="breadcrumb-item active" aria-current="page">
	    	<a href="<?=$this->url('dealsCorner.index', ['queries' => ['slug'=>$this->product->section]]);?>" itemprop="item"><span itemprop="name"><?=$this->product->breadcrumb_name;?></span></a>	    	
	    	<meta itemprop="position" content="2" />
	    </li>
	    <li itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="breadcrumb-item active" aria-current="page">
			<span itemprop="name"><?= $this->product->designation; ?></span>
			<meta itemprop="position" content="3">
		</li>
	  </ol>
	</nav>
<article class="row">
<div class="col-l-9">
<div class="row">
	<div class="col-l-6">		
		<div class="gallery" id="g-1">
			<?php
				$count = count($this->product->files);
				$width = $count >= 1 ? $count * 100 : 100;
				$imgWidth = $count > 0 ? 100 / $count : 100;
			?>
			<div class="slider" style="width: <?= $width ?>%; transform: translateX(0%);">
				<?php foreach ($this->product->files as $k => $file) : ?>
					<?php if ($file->type === 'image') : ?>
						<?php if ($k === 0) : ?>
							<input type="hidden" name="item[product_img]" value="<?= $file->url; ?>" form="addToCart" />
							<p id="file-<?= $file->id; ?>" style="width:<?= $imgWidth; ?>%;">
								<img itemprop="image" src="<?= $file->url; ?>"  srcset="<?= $file->w360; ?> 360w, <?= $file->url; ?> 800w"  sizes="(max-width: 600px) 360px, 800px" alt="<?= $this->product->designation; ?>">
							</p>												
						<?php else : ?>
							<p id="file-<?= $file->id; ?>" style="width:<?= $imgWidth; ?>%;">
								<img itemprop="image" loading="lazy" src="<?= $file->url; ?>"  srcset="<?= $file->w360; ?> 360w, <?= $file->url; ?> 800w" sizes="(max-width: 600px) 360px, 800px" alt="" />
							</p>								
						<?php endif; ?>
					<?php else : ?>
						<video controls loop style="width:<?= $imgWidth; ?>%;" id="file-<?= $file->id; ?>" poster="https://dev.kutvek.com/img/products/miniature.jpg">
							<source src="<?= $file->url; ?>" type="video/mp4">
						</video>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>	
		<div class="thumbnails" data-gallery="g-1">
			<?php foreach ($this->product->files as $i => $file) :
				$pos = strrpos($file->url, '/');
				$title = substr($file->url, $pos + 1);
			?>					
				<?php if ($file->type === 'image') : ?>
					<?php if ($i === 0) : ?>
						<img  loading="lazy" class="thumbnail click active" src="<?= $file->url; ?>" srcset="<?= $file->w72; ?> 72w"  sizes="72px"  title="<?= $title; ?>" data-img="file-<?= $file->id; ?>" data-translate="0" data-direction="right" data-ctrl="gallery.thumbnail" alt="">
					<?php else : ?>
						<img  loading="lazy" class="thumbnail click" src="<?= $file->url; ?>" srcset="<?= $file->w72; ?> 72w"  sizes="72px" title="<?= $title; ?>" data-img="file-<?= $file->id; ?>" alt="image" data-translate="-<?= $imgWidth * $i; ?>" data-direction="left" data-ctrl="gallery.thumbnail" alt="">
					<?php endif; ?>
				<?php else : ?>
					<img  loading="lazy" class="thumbnail click" src="https://dev.kutvek.com/img/products/miniature.jpg" data-img="file-<?= $file->id; ?>" alt="image" data-translate="-<?= $imgWidth * $i; ?>" data-direction="left" data-ctrl="gallery.thumbnail" alt="">
				<?php endif; ?>					
			<?php endforeach; ?>
			<!-- thumbs -->
		</div>
	</div>
	<div class="col-l-5 col-m-push-1">
		<h1 class="designation"><?=$this->product->designation;?></h1>
		<?=$this->product->hat;?>
		<p data-i18n="suitable-for">Convient pour :</p>
		<?=$this->product->suitableFor;?>		
	</div>
</div>		
<ul class="tabs" id="item-info">
	<li class="col-s-4 active"><a href="#desc-info">Description</a></li>
	<li class="col-s-4"><a href="#kit-info"><span data-i18n="howto-install">Pose du kit</span></a></li>
	<li class="col-s-4"><a href="#avis-info"><span data-i18n="customer-review">Avis client</span></a></li>
</ul>
<div class="tabs_content">
	<div class="tab_content active" id="desc-info">
		<?=$this->specialchars_decode($this->product->description);?>
		
		<div class="color-info"><i class="material-symbols-rounded">&#xe88e;</i><span data-i18n="color-vary">Les couleurs peuvent légèrement varier à l'impression par rapport à ce qui est affiché à l'écran</span></div>	
	</div>
	<div class="tab_content" id="kit-info">
		<iframe id="install-kit" src="https://www.youtube.com/embed/0y0sQbbVmpc?si=zTC8P-z0gDMnDK2M" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
	</div>
	<div class="tab_content" id="avis-info">
		
	</div>	
</div>
<p data-i18n="cross-selling" class="cross-selling-title">Produits complémentaires</p>
<div class="cross-selling">
	<div>
		<?php foreach($x_sell->getCleaningProducts() as $cleaningProduct) :?>
		<figure id="<?=$cleaningProduct->id?>" class="cross-selling-item">			
			<img loading="lazy" class="visual" src="<?=$cleaningProduct->visual;?>" />			
			<figcaption>								
				<p class="item"><?=$cleaningProduct->designation;?></p>
				<p class="amount"><span class="price block"><?=$cleaningProduct->pricef;?></span></p>
			</figcaption>
			<a href="<?=$cleaningProduct->url;?>" target="_blank"></a>
		</figure>
		<?php endforeach; ?>
	</div>					
</div>
</div>
<aside class="col-s-12 col-m-3 item-cart" id="p-cart">	
	<div class="cart-reinsurance">
				<?php foreach($reinsurance_items as $item): ?>
					<div>
						<?php if($item->icon !== null): ?>
							<?php if($item->icon_type === 'img'): ?>
								<img src="<?= $item->icon; ?>" alt="" class="picto"/>
							<?php else: ?>
								<span class="material-symbols-rounded"><?= $item->icon; ?></span>
							<?php endif; ?>							
						<?php endif; ?>
						<div>
							<p class="reinsurance-designation"><?= $item->designation ?></p>
							<?= $item->body; ?>
						</div>
						<?php if($item->link !== null): ?>
						<a href="<?= $item->link ?>" class="click" data-ctrl="modal.fetch" data-module="modal" data-modal="reinsurances"></a>
						<?php endif; ?>
					</div>					
				<?php endforeach; ?>
			</div>
	<div class="header">TOTAL</div>	

		<div class="items">
			<p id="p-<?= $this->product->id; ?>">
				<!-- <span class="btns-group item-qty">
					<button class="btn square click" data-ctrl="item.decrease">-</button>
					<input type="text" id="qty" class="btn square" value="1" name="item[qty]" form="addToCart" />
					<button class="btn square click" data-ctrl="item.increase">+</button>
				</span> -->
				<input type="hidden" name="item[price][opts]" id="price-opts" form="addToCart" value="0" />
				<input type="hidden" name="item[price][accessories]" id="price-accessories" form="addToCart" value="0" />
				<input type="hidden" name="item[product]" value="<?= $this->product->id; ?>" form="addToCart"/>
				<input type="hidden" name="item[product_url]" form="addToCart" value="https://<?= $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"] ?>" />
				<input type="hidden" name="item[licence]" form="addToCart" value="" />
				<input type="hidden" name="item[weight]" form="addToCart" value="<?= $this->product->weight; ?>" />
				<input type="hidden" name="item[item_category]" form="addToCart" value="64" />				
				
			</p>
			<div id="opts" class="options">
				<p class="type-opt" data-opt="STANDARD" data-id="2"></p>
				<p class="finish-opt" data-opt="Finition Brillant" data-id="4"></p>
				<p class="premium-opt" data-opt="Aucune" data-id="10"></p>
				<p class="plate-sponsors" data-opt="" data-checked="0"></p>
				<p class="sponsors-only" data-opt="" data-checked="0"></p>
				<p class="switch" data-opt="" data-checked="0"></p>
				<p class="seat-cover" data-opt="" data-checked="0"></p>
				<p class="rim-sticker" data-opt="" data-checked="0"></p>
				<p class="door-stickers" data-opt="" data-checked="0"></p>
				<p class="plastics" data-opt="" data-checked="0"></p>
				<p class="hubs-stickers" data-opt="" data-checked="0"></p>
				<p class="mini-plates" data-opt="" data-checked="0"></p>
			</div>
			<div id="selected-accessories"></div>
		</div>
			<footer>				
				<div itemscope itemprop="offers" itemtype="https://schema.org/Offer" >
					<meta itemprop="availability" content="https://schema.org/InStock" />	
					<meta itemprop="priceCurrency" id="item-currency" content="<?= $this->product->currency_code; ?>" />					
					<p class="prices">
						<span class="old"><?= $this->product->old;?></span>
						<data itemprop="price" class="new" value="<?= number_format($this->product->price->new,2,'.',''); ?>"><?= $this->product->new;?></data>
					</p>
				</div>
				<form action="<?= $this->uri('dealsCorner.addToCart', ['queries'=>['section'=>$this->product->section]], 'POST'); ?>" method="post" id="addToCart" data-ctrl="deal.addToCart">
					<input type="hidden" name="item[price][product]" value="<?= number_format($this->product->price->new,2,'.',''); ?>" />
					<input type="hidden" name="item[webshop_currency]"  value="<?= $this->product->currency_code; ?>" />
					<input type="hidden" name="item[currency]" value="<?= $this->product->currency_id; ?>" />
					<input type="hidden" name="item[workspace]" value="<?=$this->product->workspace;?>"/>
					<input type="hidden" name="item[description]" value="<?= $this->product->designation; ?>" />
					<input type="hidden" name="item[tax_included]" value="<?= $this->product->country_vat;?>" />
					<input type="hidden" name="item[qty]" value="1" />
					<input type="hidden" name="behavior" value="<?= $this->product->behavior_id;?>" />
					<p class="cart-action">
						<button class="btn contained dark" type="submit" class="btn contained dark" >							
							<span class="icon material-symbols-rounded load hidden">progress_activity</span>
							<span class="text" data-i18n="add-to-cart">Ajouter au panier</span>
						</button>
					</p>							
				</form>
			</footer>
				
		</aside>
</article>