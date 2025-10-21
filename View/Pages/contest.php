<article class="row">
	<div class="col-l-9 col-xl-8 col-l-center">
		<header>
			<picture>
					<source srcset="<?= $page->cover ?>" type="image/jpg" media="(orientation: landscape)" />
					<source srcset="<?= $page->cover_portrait ?>" media="(orientation: portrait)" type="image/webp"/>
					<source srcset="<?= str_replace('jpg', 'webp', $page->cover_portrait) ?>" media="(orientation: portrait)" type="image/jpg"/>
					<img src="<?= str_replace('jpg', 'webp', $page->cover) ?>" / alt=""/>
						
			</picture>
			<h1><?= $page->title; ?></h1>		
		</header>
		<div><?= $this->specialchars_decode($page->content);?></div>	
	</div>	
</article>
<pre><?php if(isset($_GET['debug'])) print_r($page)?></pre>