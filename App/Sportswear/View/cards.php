<div id="products" class="cards">				
	<?php foreach ($cards as $k => $card) : $lazy = $k >  2 ? 'loading="lazy"' : '';?>
	<div class="card">
        <img <?=$lazy;?> src="<?= $card->cover ?? '/img/blank.png'; ?>" alt="" >			                
        <h2 class="designation"><?= $card->designation; ?></h2>
        <p class="prices"><?=$card->min;?></p>		                
        <a href="<?=$this->url('saddleCover.read',['queries'=>['section'=>$card->section, 'slug'=>$card->slug, 'id' => $card->id]])?>"></a>                         
	</div>
	<?php endforeach; ?>			
</div>
<?= $pagination; ?>