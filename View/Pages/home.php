<!-- Carrousel -->
<?php
$now = new DateTime('now', new DateTimeZone('Europe/Paris'));
$frdays = new DateTime('2025-09-19 24:00:00', new DateTimeZone('Europe/Paris'));
$endfrdays = new DateTime('2025-10-01 00:00:00', new DateTimeZone('Europe/Paris'));

$suffixLang = strtoupper($this->getI18n(false));
$idx = 0;

if ($now->getTimestamp() > $frdays->getTimestamp()   && $now->getTimestamp() < $endfrdays->getTimestamp()):
	$programmed[] = (object) ['img' => '/img/slider/SLIDER-FRENCH-DAYS-AUTOMNE-2025.webp', 'portrait'=> '/img/slider/square/SQUARE-FRENCH-DAYS-AUTOMNE-2025.webp', 'id' => $idx++, 'url' => ''];
	$programmed[] = (object) ['img' => '/img/slider/SLIDER-MILLESIME-BLEU.webp', 'portrait'=> '/img/slider/square/SQUARE-MILLESIME-BLEU.webp', 'id' => $idx++, 'url' => "#"];
	$programmed[] = (object) ['img' => '/img/slider/SLIDER-FRENCH-DAYS-AUTOMNE-2025.webp', 'portrait'=> '/img/slider/square/SQUARE-FRENCH-DAYS-AUTOMNE-2025.webp', 'id' => $idx++, 'url' => ''];
	$programmed[] = (object) ['img' => '/img/slider/SLIDER-HORNET-NEST.webp', 'portrait'=> '/img/slider/square/SQUARE-HORNET-NEST.webp', 'id' => $idx++, 'url' => ''];
	$files = $programmed;
else:
	$files = array_merge($programmed??[], [		
	
	(object) ['img' => '/img/slider/SLIDER-MILLESIME-BLEU.webp', 'portrait'=> '/img/slider/square/SQUARE-MILLESIME-BLEU.webp', 'id' => $idx++,'url' => ''],
	(object) ['img' => '/img/slider/SLIDER-HORNET-NEST.webp', 'portrait'=> '/img/slider/square/SQUARE-HORNET-NEST.webp', 'id' => $idx++, 'url' => '#'],
	(object) ['img' => '/img/slider/SLIDER-BRAMBLE-2024-'.$suffixLang.'.webp', 'portrait'=> '/img/slider/square/SQUARE-BRAMBLE-2024-'.$suffixLang.'.webp', 'id' => $idx++, 'url' => '#'],
	(object) ['img' => '/img/slider/SLIDER-CRF-LENA-2024-'.$suffixLang.'.webp', 'portrait'=> '/img/slider/square/SQUARE-CRF-LENA-2024-'.$suffixLang.'.webp', 'id' => $idx++, 'url' => '#'],
	(object) ['img' => '/img/slider/SLIDER-SCORE-SERIE-2023-'.$suffixLang.'.webp', 'portrait'=> '/img/slider/square/SQUARE-SCORE-SERIE-2023-'.$suffixLang.'.webp', 'id' => $idx++, 'url' => '#'],
	(object) ['img' => '/img/slider/SLIDER-FIFTY-2023-'.$suffixLang.'.webp', 'portrait'=> '/img/slider/square/SQUARE-FIFTY-2023-'.$suffixLang.'.webp', 'id' => $idx++, 'url' => '#'],
	(object) ['img' => '/img/slider/SLIDER-BROWING-2023-'.$suffixLang.'.webp', 'portrait'=> '/img/slider/square/SQUARE-BROWING-2023-'.$suffixLang.'.webp', 'id' => $idx++, 'url' => '#']
]);
endif;

$count = count($files);
$width = $count >= 1 ? $count * 100 : 100;
$imgWidth = $count > 0 ? 100 / $count : 100;
?>
<div id="carousel" class="gallery main-gallery">
	<div id="main-slider" class="slider" style="width: <?= $width ?>%; transform: translateX(0%);">
		<?php foreach ($files as $k => $file) : ?>
			<?= $file->url != '#' ? '<a href="'.$file->url.'" style="width:' .$imgWidth. '%">' : '<span style="width:'. $imgWidth .'%">' ?>
				<picture>
					<source srcset="<?= $file->img ?>" type="image/webp" media="(orientation: landscape)" />
						<source srcset="<?= $file->portrait ?>" media="(orientation: portrait)" type="image/webp" />
						<source srcset="<?= str_replace('webp', 'jpg', $file->portrait) ?>" media="(orientation: portrait)" type="image/jpg"/>
						<source srcset="<?= str_replace('webp', 'png', $file->portrait) ?>" media="(orientation: portrait)" type="image/png"/>
					<?php if ($k === 0) : ?>						
						<img src="<?= str_replace('webp', 'jpg', $file->img) ?>" id="img-visual" class="active">
					<?php else : ?>						
						<img src="<?= str_replace('webp', 'jpg', $file->img) ?>" id="img-<?= $file->id; ?>">
					<?php endif ?>
				</picture>
			<?= $file->url != '#' ? '</a>' : '</span>'; ?>
		<?php endforeach ?>
	</div>
	<div class="p-thumbs gallery-cursor">
		<?php foreach ($files as $i => $file) :
			$pos = strrpos($file->img, '/');
			$title = substr($file->img, $pos + 1);
		?>
			<?php if ($i === 0) : ?>
				<div title="<?= $title; ?>" data-img="img-visual" data-translate="0" data-direction="right"  class="visual_thumb  active cursor"></div>
			<?php else : ?>
				<div title="<?= $title; ?>" data-img="img-<?= $file->id; ?>" alt="image" data-translate="-<?= $imgWidth * $i; ?>" data-direction="left"  class="visual_thumb  cursor"></div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
<?= $this->app->reinsurances(); ?>
<div class="row">
	<div class="col-s-12 col-l-11 col-xl-9 col-m-center">
		<?= $this->app->widgetTop();?>
		
		<?= $this->app->widgetLicences();?>
		<!-- <h2 class="h1 page-title" data-i18n="good-deals">Les bonnes affaires</h2> -->
		
		<!-- TrustBox widget - Slider -->
		<div class="trustpilot-slider">			
			<div class="trustpilot-widget bottom" data-locale="fr-FR" data-template-id="54ad5defc6454f065c28af8b" data-businessunit-id="5c10d1e8416bce0001137d41" data-style-height="240px" data-style-width="100%" data-theme="light" data-stars="3,4,5">
				<a href="https://fr.trustpilot.com/review/kutvek-kitgraphik.com" target="_blank" rel="noopener">Trustpilot</a> 				
			</div>
		</div>
	</div>
</div>
<?php if(isset($_GET['debug'])): ?>
	<pre><?php print_r($isPro) ?></pre>
<?php endif; ?>