<div id="products" class="cards">
<?php foreach ($cards as $k => $card) : $lazy = $k >  2 ? 'loading="lazy"' : '';?>
	<div class="card">
        <img <?=$lazy;?> src="<?= $card->cover ?? '/img/blank.png'; ?>" alt="" />
        <h2 class="designation"><?= $card->title; ?></h2>
        <p class="prices"><?=$card->cost;?></p>	                
        <a href="<?=$card->url;?>"></a>
	</div>
<?php endforeach; ?>
</div>
<pre><?php print_r($cards) ?></pre>