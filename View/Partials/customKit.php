
<?php 
	if($this->getL10nId() == 1 || $this->getL10nId() == 2) :
		$file = (object)[
		'img' => '/img/section/100-PERSO.webp', 
		'portrait'=> '/img/section/square/100-PERSO.webp'
		]; 
	else : 
		$file = (object)[
		'img' => '/img/section/100-PERSO-EN.webp', 
		'portrait'=> '/img/section/square/100-PERSO-EN.webp'
		];
	endif; ?>

<div class="widget-custom">
	<a href="<?= $this->uri('products.section', ['queries' => ['slug' => $customKit->slug]]); ?>">
		<picture>
			<source srcset="<?= $file->img ?>" type="image/webp" media="(orientation: landscape)" />
			<source srcset="<?= $file->portrait ?>" media="(orientation: portrait)" type="image/webp" />
			<source srcset="<?= str_replace('webp', 'jpg', $file->portrait) ?>" media="(orientation: portrait)" type="image/jpg"/>
			<img src="<?= str_replace('webp', 'jpg', $file->img) ?>" id="img-visual">
		</picture>
	</a>
</div>