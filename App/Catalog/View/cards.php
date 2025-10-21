<div id="products" class="cards">				
	<?php foreach ($this->section->items as $k => $card) : $lazy = $k >  2 ? 'loading="lazy"' : '';?>
	<div class="card">
        <img <?=$lazy;?> src="<?= $card->cover ?? '/img/blank.png'; ?>" alt="" >			                
        <h2 class="designation"><?= $card->designation; ?></h2>
        <p class="prices"><?=$card->min;?> <?=$card->max;?></p>	                
        <a href="<?=$this->url('dispatcher',['queries'=>['slug'=>$card->slug, 'id' => $card->id]])?>"></a>                     
	</div>
	<?php endforeach; ?>			
</div>