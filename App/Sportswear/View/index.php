<nav role="navigation" aria-label="breadcrumb">
	  <ol  itemscope itemtype="https://schema.org/BreadcrumbList"  class="breadcrumb">
	    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
	    	<a href="<?=$this->url('pages.index');?>" itemprop="item"><span data-i18n="homepage">Accueil</span></a>
	    	<meta itemprop="position" content="1" />
	    </li>         
	    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"class="breadcrumb-item active" aria-current="page">
	    	<span itemprop="name">Sportswear</span>
	    	<meta itemprop="position" content="2" />
	    </li>
	  </ol>
	</nav>
<section class="row">
<div class="col-s-12 col-l-11 col-xl-10 col-center">	
	<img src="<?=$this->editorial->cover;?>" alt="" />	
	<div class="row">
		<div class="col-s-12 col-l-3 column-filter">
			
		</div>
		<div class="col-s-12 col-l-9">
			<h1 class="section-title"><?=$this->editorial->designation;?></h1>
			<div class="short-description"><?=$this->specialchars_decode($this->editorial->description);?></div>

			<div id="products" class="cards">				
		        <?php foreach ($cards as $k => $card) : $lazy = $k >  2 ? 'loading="lazy"' : '';?>
		            <div class="card">
		                <img <?=$lazy;?> src="<?= $card->cover ?? '/img/blank.png'; ?>" alt="" >			                
		                <h2 class="designation"><?= $card->title; ?></h2>
		                <p class="prices"><?=$card->cost;?></p>		                
		                <a href="<?=$card->url;?>"></a>                         
		            </div>
		        <?php endforeach; ?>			
			</div>
		<?= $pagination; ?>
		<div class="bottom-description"></div>
	</div>	
</div>
</div>
</section>
<pre>	
	<?php if(isset($_GET['debug'])): ?>
		<?php print_r($this->_router->getParts()); ?>
		<?php //print_r($this->_router->getRouting()); ?>			
		<?php print_r($this->_router->getL10n()); ?>		
		<?php print_r($cards) ?>
	<?php endif; ?>
</pre>