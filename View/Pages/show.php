<h1 class="page-title"><?= $page->title; ?></h1>	
<div class="col-s-12 col-m-10 col-l-9 col-m-center">
	<?= $this->specialchars_decode($page->content); ?>
</div>