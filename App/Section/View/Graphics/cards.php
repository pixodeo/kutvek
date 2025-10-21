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