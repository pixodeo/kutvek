<article class="item">
<div>
	<div class="gallery" id="g-1" data-uri="<?=$this->url('product.gallery', ['fqdn'=>1,  'queries' => ['id' => ':id']]);?>">
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
<div class="item-composition">
	<h1 class="designation">
		<?=$this->product->title;?>
		<input type="hidden" name="item[description]" value="<?= $this->product->title; ?>" form="addToCart" />
	</h1>
	<?= $this->specialchars_decode($this->product->hat);?>
	<div class="more-info">
		<a href="#more-info" data-i18n="more-info" class="link click" data-ctrl="utils.scrollTo" data-module="utils">+ d'infos</a>
	</div>
	<?=$this->widgetVehicles();?>
	<?=$this->widgetMillesims();?>
	<?=$this->widgetTypes();?>
	
	<div id="bloc-options" data-uri="<?=$this->url('product.options', ['fqdn'=>1,  'queries' => ['id' => ':id']]);?>">
		<div>
		<?= $component->options();?>
		<?= $component->widgetMiniPlates();?>
		<?= $component->widgetWheelHubsStickers();?>
		</div>
		<div>
			<?=$component->seatCover();?>
		</div>
	</div>
	<?=$this->widgetFinish();?>		
</div>
<div class="item-content">
	<div id="more-info"></div>
	<ul class="tabs grid" id="item-info">
		<li class="col-4"><a href="#desc-info">Description</a></li>
		<li class="col-4 active"><a href="#kit-info"><span data-i18n="howto-install">Pose du kit</span></a></li>
		<li class="col-4"><a href="#avis-info"><span data-i18n="customer-review">Avis client</span></a></li>
	</ul>
	<div class="tabs_content ">
		<div class="tab_content" id="desc-info">
			<div id="product-description" itemprop="description"><?= $this->product->description; ?></div>	
			<div id="product-features" ><?= $this->product->features; ?></div>	
			<div class="best_rendering"><?= $this->product->best_rendering; ?></div>
			<div class="color-info"><i class="material-symbols-rounded">&#xe88e;</i><span data-i18n="color-vary">Les couleurs peuvent légèrement varier à l'impression par rapport à ce qui est affiché à l'écran</span>
			</div>	
		</div>
		<div class="tab_content active" id="kit-info">
			<iframe id="install-kit" src="https://www.youtube.com/embed/0y0sQbbVmpc?si=zTC8P-z0gDMnDK2M" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
				<div id="install-notice">
					<?= $this->product->howto_install; ?>
				</div>						
		</div>
		<div class="tab_content" id="avis-info">
			<pre>
			<?php 
			echo $component::class;
			print_r($this->product); 
			?>
			</pre>
		</div>
	</div>
	
</div>
<aside class="item-cart" id="p-cart">	
	<div id="item-cost" class="item-cost"><?=$this->product->cost;?></div>
	<div class="items">
		<p id="p-<?= $this->product->id; ?>">
			<!-- <span class="btns-group item-qty">
				<button class="btn square click" data-ctrl="item.decrease">-</button>
				<input type="text" id="qty" class="btn square" value="1" name="item[qty]" form="addToCart" />
				<button class="btn square click" data-ctrl="item.increase">+</button>
			</span> -->
			<input type="hidden" name="item[price][opts]" id="price-opts" form="addToCart" value="0" />
			<input type="hidden" name="item[price][accessories]" id="price-accessories" form="addToCart" value="0" />	

		</p>		
		<div id="selected-accessories"></div>
	</div>
	<footer>				
		<div itemscope itemprop="offers" itemtype="https://schema.org/Offer" >
			<meta itemprop="availability" content="https://schema.org/InStock" />	
			<meta itemprop="priceCurrency" id="item-currency" content="<?= $this->product->currency_code; ?>" />		
			<p class="prices">						
			</p>
		</div>
		<form action="<?= $this->url('product.addToCart', ['fqdn' => 1, 'queries'=>[]], 'POST'); ?>" method="post" id="addToCart" data-ctrl="product.addToCart" >	
			<input type="hidden" name="item[webshop_currency]"  value="<?= $this->product->currency_code; ?>" />
			<input type="hidden" name="item[currency]" value="<?= $this->product->currency_id; ?>" />
			<input type="hidden" name="item[workspace]" value="2"/>			
			<input type="hidden" name="item[tax_included]" value="<?= $this->product->country_vat;?>" />
			<input type="hidden" name="item[qty]" value="1" />
			<input type="hidden" name="behavior" value="<?= $this->product->behavior_id;?>" />	
			<input type="hidden" name="behavior_type"  value="<?= $this->product->behavior_type; ?>" />	
			<input type="hidden" name="item[product]" value="<?= $this->product->id; ?>" />
			<input type="hidden" name="item[product_url]"  value="https://<?= $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"] ?>" />
			<input type="hidden" name="item[product_licence]"  value="<?=$this->product->license;?>" />
			<input type="hidden" name="item[weight]"  value="<?= $this->product->weight; ?>" />
			<input type="hidden" name="item[item_category]" value="" />
			<?=$this->itemTypeInfo();?>
			<?=$this->sku();?>	
			<p class="cart-action">
				<button class="btn contained dark addToCart" type="submit" class="btn contained dark" id="button-cart">	
					<span class="icon material-symbols-rounded load hidden">progress_activity</span>
					<span class="text" data-i18n="add-to-cart">Ajouter au panier</span>
				</button>
			</p>							
		</form>
		<div class="cart-reinsurance">
			<?php foreach($reinsurance_items as $item): ?>
				<div>
					<?php if($item->icon !== null): ?>
						<?php if($item->icon_type === 'img'): ?>
							<span><img src="<?= $item->icon; ?>" alt="" class="picto"/></span>
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
	</footer>				
</aside>

</article>
<aside class="modal click" id="effects-finish" data-modal="effects-finish" data-ctrl="modal.popin">
	<div class="popup">
		<header class="close">
			<p class="title" data-i18n="effect-finish-desc"></p>
			<a href="#effects-finish" class="click" data-modal="effects-finish" data-ctrl="modal.popin"><span class="icon material-symbols-rounded">close</span></a>
		</header>
		<div>
			<img src="/img/effects-and-finish.jpg" alt="Effects and finish info on graphics" />
		</div>
	</div>
</aside>
<?=$component->popinOptions();?>