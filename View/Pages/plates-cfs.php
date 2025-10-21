<?php 
use App\Category\Read;
$action = new Read($this->_router);
$action($page->category);

?>
<nav role="navigation" aria-label="breadcrumb">
	  <ol  itemscope itemtype="https://schema.org/BreadcrumbList"  class="breadcrumb">
	    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
	    	<a href="<?=$this->url('pages.index');?>" itemprop="item"><span data-i18n="homepage">Accueil</span></a>
	    	<meta itemprop="position" content="1" />
	    </li>         
	    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"class="breadcrumb-item active" aria-current="page">
	    	<span itemprop="name">Stickers de plaques</span>
	    	<meta itemprop="position" content="2" />
	    </li>
	  </ol>
	</nav>
<div class="col-s-12 col-l-11 col-xl-10 col-center">
	<div class="row">
		<div class="col-s-12 col-l-3 column-filter">
			
		</div>
		<div class="col-s-12 col-l-9">
			<h1 class="section-title"><?=$page->title;?></h1>
			
			<?=$action->cards;?>
			
			<div class="bottom-description"></div>
		</div>	
	</div>
</div>
</section>