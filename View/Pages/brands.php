<?php
	use App\Component\Brand\Universes; 
	$component = new Universes;
	$component->setI18n($this->getI18n());
	$component->setRoute($this->_route);
	$component->setBrandId($page->brand);
	$links = $component->getLinks();
?>
<article class="col-s-12 col-m-10 col-l-9 col-m-center">
	<h1><?= $page->title; ?></h1>	
	<div class="links-group">
		<?php foreach($links as $link): ?>
			<a class="link" href="<?= $link->url;?>"><?= $link->designation;?></a>	
		<?php endforeach; ?>													
	</div>
	<?= htmlspecialchars_decode($page->content, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5); ?>
	<hr>
	<?= htmlspecialchars_decode($page->faq, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5); ?>
</article>
<?php if(isset($_GET['debug'])): ?>
	<?php  ?>
	<pre>
		<?php print_r($links); ?>
	</pre>	
<?php endif; ?>