<div id="products" class="product-item-container">
	<?php foreach ($cards as $key => $card) : ?>
		<figure id="<?= $card->id; ?>" class="product-item" data-brand="<?= $card->brand; ?>" data-design="<?= $card->design; ?>" data-color="<?= $card->color; ?>" data-old="<?= $card->old_visual; ?>" data-p="<?= $card->parent; ?>">
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
					<span class="price block"><?= $card->price_0; ?></span>
					<?= $rebate;?>
				</p>
			</figcaption>
			<a href="<?= $card->url; ?>"></a>
		</figure>
	<?php endforeach; ?>
</div>
<?php if(isset($_GET['debug'])): ?>
<pre><?php print_r($cards);?></pre>
<?php endif; ?>