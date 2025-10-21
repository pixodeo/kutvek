<div id="products" class="product-item-container cards">
	<?php foreach ($cards as $key => $card) : ?>
		<figure id="<?= $card->id; ?>" class="product-item" data-brand="<?= $card->brand; ?>" data-design="<?= $card->design; ?>" data-color="<?= $card->color; ?>">
			<span class="offer"><img  src="<?=$card->offerPicto;?>" alt="" /></span>
			<?php 
				$img = $this->img($card->visual);
				if (isset($card->visual) && $img && $card->visual !=='https://www.kutvek-kitgraphik.com/images/produits/original'): 
			?>
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