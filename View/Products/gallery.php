<div class="gallery" id="g-1" data-uri="<?=$this->url('product.gallery', ['fqdn'=>1,  'queries' => ['id' => ':id']]);?>">
	<?php
		$count = count($files);
		$width = $count >= 1 ? $count * 100 : 100;
		$imgWidth = $count > 0 ? 100 / $count : 100;
	?>
	<div class="slider" style="width: <?= $width ?>%; transform: translateX(0%);">
		<?php foreach ($files as $k => $file) : ?>
			<?php if ($file->type === 'image') : ?>
				<?php if ($k === 0) : ?>
					<input type="hidden" name="item[product_img]" value="<?= $file->url; ?>" form="addToCart" />
					<p id="file-<?= $file->id; ?>" style="width:<?= $imgWidth; ?>%;">
						<img itemprop="image" src="<?= $file->url; ?>"  srcset="<?= $file->w360; ?> 360w, <?= $file->url; ?> 800w"  sizes="(max-width: 600px) 360px, 800px" alt="" />
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
	<?php foreach ($files as $i => $file) :
		$pos = strrpos($file->url, '/');
		$title = substr($file->url, $pos + 1);
	?>					
		<?php if ($file->type === 'image') : ?>
			<?php if ($i === 0) : ?>
				<img  loading="lazy" class="thumbnail click active" src="<?= $file->url; ?>" srcset="<?= $file->w72; ?> 72w"  sizes="72px"  title="<?= $title; ?>" data-img="file-<?= $file->id; ?>" data-translate="0" data-direction="right" data-ctrl="gallery.thumbnail" alt="" />
			<?php else : ?>
				<img  loading="lazy" class="thumbnail click" src="<?= $file->url; ?>" srcset="<?= $file->w72; ?> 72w"  sizes="72px" title="<?= $title; ?>" data-img="file-<?= $file->id; ?>" alt="image" data-translate="-<?= $imgWidth * $i; ?>" data-direction="left" data-ctrl="gallery.thumbnail" alt="" />
			<?php endif; ?>
		<?php else : ?>
			<img  loading="lazy" class="thumbnail click" src="https://dev.kutvek.com/img/products/miniature.jpg" data-img="file-<?= $file->id; ?>" alt="image" data-translate="-<?= $imgWidth * $i; ?>" data-direction="left" data-ctrl="gallery.thumbnail" alt="" />
		<?php endif; ?>					
	<?php endforeach; ?>
	<!-- thumbs -->
</div>