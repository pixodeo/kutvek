<section class="col-s-12 col-l-10 col-xl-9 col-m-center">
	<?= $this->breadcrumb; ?>
	<header class="store-header">
		<h1><?=$content->designation;?></h1>
		<div class="store">
			<img src="<?=$content->cover;?>" alt="" class="image">
			<strong class="h1 category "><?= $content->header; ?></strong>
			<div class="items">
				<?php foreach ($items as $item) : ?>
					<a class="direct-links" href="<?=$item->slug; ?>">
						<?= $item->name; ?>
					</a>
				<?php endforeach; ?>
			</div>		
		</div>	
	</header>	
	<div>
		<?= htmlspecialchars_decode($content->description, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5); ?>		
	</div>
	<hr style="margin-top:4.8rem;">
	<div class="faq" style="margin-top: 4.8rem;">
    
		<?= strip_tags(htmlspecialchars_decode($content->faq, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5),['h2','h3','p', 'strong', 'em', 'b', 'i', 'div']); ?>
	</div>
	<!-- TrustBox widget - Slider -->
	<div class="row trustpilot-slider">
		<div class="col-s-12 col-l-10 col-xl-9 col-m-center">
			<?= $this->app->widgetTrustpilot('slider'); ?>
		</div>
	</div>
</section>
<?php if(isset($_GET['debug'])): ?>
	<pre>
		<?php print_r($content) ?>
		<br> old : <br>
		<?php print_r($old) ?>
		<br> id
		<?php print_r($query) ?>
	</pre>
<?php endif; ?>