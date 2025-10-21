<div id="carousel" class="gallery main-gallery">
	<div id="main-slider" class="slider" style="width: <?= $width ?>%; transform: translateX(0%);">
		<?php foreach ($items as $k => $item) : ?>
			<?= $item->url != '#' ? '<a href="'.$item->url.'" style="width:' .$imgWidth. '%;">' : '<span style="width:'. $imgWidth .'%;' ?>
				<picture style="width:<?= $imgWidth ?>%;">
					<?php if ($k === 0) : ?>
						<source srcset="<?= $item->img ?>" type="image/webp" media="(orientation: landscape)" />
						<source srcset="<?= $item->portrait ?>" media="(orientation: portrait)" type="image/webp" />
						<source srcset="<?= str_replace('webp', 'jpg', $item->portrait) ?>" media="(orientation: portrait)" type="image/jpg"/>
						<img src="<?= str_replace('webp', 'jpg', $item->img) ?>" id="img-visual" class="active">
					<?php else : ?>
						<source srcset="<?= $item->img ?>" type="image/webp" media="(orientation: landscape)" />
						<source srcset="<?= $item->portrait ?>" media="(orientation: portrait)" type="image/webp"/>
						<source srcset="<?= str_replace('webp', 'jpg', $item->portrait) ?>" media="(orientation: portrait)" type="image/jpg"/>
						<img src="<?= str_replace('webp', 'jpg', $item->img) ?>" id="img-<?= $item->id; ?>">
					<?php endif ?>
				</picture>
			<?= $item->url != '#' ? '</a>' : '</span>'; ?>
		<?php endforeach ?>
	</div>
	<div class="p-thumbs gallery-cursor">
		<?php foreach ($items as $i => $item) :
			$pos = strrpos($item->img, '/');
			$title = substr($item->img, $pos + 1);
		?>
			<?php if ($i === 0) : ?>
				<div title="<?= $title; ?>" data-img="img-visual" data-translate="0" data-direction="right"  class="visual_thumb  active cursor"></div>
			<?php else : ?>
				<div title="<?= $title; ?>" data-img="img-<?= $item->id; ?>" alt="image" data-translate="-<?= $imgWidth * $i; ?>" data-direction="left"  class="visual_thumb  cursor"></div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>