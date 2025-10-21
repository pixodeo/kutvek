<!-- Widget TOP -->
<div class="widget-top">
	<?php foreach($items as $item): ?>
		<a href="<?=$this->translation($item->link);?>" class="custom-seat-cover">
			<picture>
			<source srcset="<?= $item->img ?>" type="image/webp" media="(orientation: landscape)" />
			<source srcset="<?= str_replace('top/', 'top/portrait/', $item->img) ?>" media="(orientation: portrait)" type="image/webp" />
			<source srcset="<?= str_replace(['top/','webp'], ['top/portrait/','jpg'], $item->img) ?>" media="(orientation: portrait)" type="image/jpg"/> 
			<img src="<?= str_replace('webp', 'jpg', $item->img) ?>">
			</picture>
			<span class="designation"><?= $item->designation;?></span>
			<?php if($item->link !== '#'): ?>
			<span class="action" data-i18n="shop-now">J'y vais !</span>
		<?php endif; ?>
		</a>	
	<?php endforeach; ?>
</div>