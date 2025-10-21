<h1 class="designation">
	<?=$this->product->title;?><input type="hidden" name="item[description]" value="<?= $this->product->title; ?>" form="addToCart" />
</h1>
<?= $this->specialchars_decode($this->product->hat);?>
<div id="product-description" itemprop="description"><?= $this->product->description; ?></div>	
<div id="product-features" ><?= $this->product->features; ?></div>	
<div class="best_rendering"><?= $this->product->best_rendering; ?></div>
<div id="bloc-options" data-uri="<?=$this->url('product.options', ['fqdn'=>1,  'queries' => ['id' => ':id']]);?>">
	<div>
	<?= $component->options();?>
	<?= $component->widgetMiniPlates();?>
	<?= $component->widgetWheelHubsStickers();?>
	</div>
	<div><?=$component->seatCover();?></div>	
</div>
<pre>
<?php print_r($this->product); ?>
</pre>