<!-- Réassurances -->
<div class="row reinsurances">
	<?php foreach($items as $item): ?>
		<a href="<?= $this->uri('products.section', ['queries' => ['slug' => $item->link]]); ?>" class="col-s-12 col-m-4 reinsurance">	
			<img src="<?= $item->icon;?>" class="picto" alt="">						
			<div class="body">	
				<p class="title"><?= $item->designation ?></p>								
				<?= $item->body; ?>
			</div>
    	</a>
	<?php endforeach; ?>
	<div class="trustpilot"></div>
</div>