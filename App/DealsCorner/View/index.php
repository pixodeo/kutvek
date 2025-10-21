<nav role="navigation" aria-label="breadcrumb">
	  <ol  itemscope itemtype="https://schema.org/BreadcrumbList"  class="breadcrumb">
	    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
	    	<a href="<?=$this->url('pages.index');?>" itemprop="item"><span data-i18n="homepage">Accueil</span></a>
	    	<meta itemprop="position" content="1" />
	    </li>         
	    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"class="breadcrumb-item active" aria-current="page">
	    	<span itemprop="name"><?=$this->editorial->breadcrumb_name;?></span>
	    	<meta itemprop="position" content="2" />
	    </li>
	  </ol>
	</nav>
<section class="row">
<div class="col-s-12 col-l-11 col-center">
	<div class="row">
		<div class="col-s-12 col-l-3 column-filter">
			<p class="btn-filter"><button class="contained dark click" data-ctrl="utils.autoHeight"><span data-i18n="filters">Filtres</span><i class="material-symbols-rounded"></i></button></p>
			<form action="<?= $this->url('dealsCorner.filter', ['queries' => ['slug'=>$this->editorial->slug]]); ?>" class="bloc-widgets-filter filter-form" data-refresh="<?= $this->url('dealsCorner.refreshFilter', ['fqdn' => 1,'queries' => ['slug'=>$this->editorial->slug]]); ?>" id="filters-form">	
				<div class="widget-filter">
					<div class="title accordion_tabs">
						<span data-i18n="categories">Catégorie</span>
						<input type="checkbox" id="category" class="filter" data-uri="<?= $this->url('products.filters', ['queries' => ['filter' => 'category', 'slug' => $current_slug, 'depth' => $depth]]); ?>" checked data-fetched="1" />
						<label for="category" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
						<div>
							<ul>
								<?php foreach($categories as $c): ?>	
								<li>
									<input name="behavior[]" type="checkbox" class="field-input onchange" value="<?= $c->behavior_id;?>" id="behaviors-<?= $c->behavior_id;?>" data-ctrl="deal.filters" >
									<label for="behaviors-<?= $c->behavior_id;?>"><?= $c->behavior_name;?></label>
								</li>	
								<?php endforeach; ?>							
							</ul>
						</div>
					</div>
				</div>	
				<div class="widget-filter">
					<div class="title accordion_tabs">
						<span data-i18n="families">Univers</span>
						<input type="checkbox" id="universe" class="filter" data-uri="<?= $this->url('products.filters', ['queries' => ['filter' => 'universe', 'slug' => $current_slug, 'depth' => $depth]]); ?>" checked data-fetched="1" />
						<label for="universe" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
						<div>
							<ul>
								<?php foreach($universes as $u): ?>	
								<li>
									<input name="universe[]" type="checkbox" class="field-input onchange" value="<?= $u->family_id;?>" id="universes-<?= $u->family_id;?>" data-ctrl="deal.filters" >
									<label for="universes-<?= $u->family_id;?>"><?= $u->family_name;?></label>
								</li>	
								<?php endforeach; ?>							
							</ul>
						</div>
					</div>
				</div>
				<div class="widget-filter">
					<div class="title accordion_tabs">
						<span data-i18n="brands">Marque</span>
						<input type="checkbox" id="brand" class="filter" data-uri="<?= $this->url('products.filters', ['queries' => ['filter' => 'brand', 'slug' => $current_slug, 'depth' => $depth]]); ?>" data-modal="brands" checked/>
						<label for="brand" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
						<div>
							<ul>
								<?php foreach($brands as $b): ?>	
								<li>
									<input name="brand[]" type="checkbox" class="field-input onchange" value="<?= $b->brand_id;?>" id="brands-<?= $b->brand_id;?>" data-ctrl="deal.filters" >
									<label for="brands-<?= $b->brand_id;?>"><?= $b->brand_name;?></label>
								</li>	
								<?php endforeach; ?>							
							</ul>
						</div>
					</div>
				</div>
				<!-- <div class="widget-filter">
					<div class="title accordion_tabs">
						<span data-i18n="vehicle">Véhicules</span>
						<input type="checkbox" id="vehicle" class="filter" data-modal="vehicles" checked />
						<label for="vehicle" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
						<div>
							<ul id="vehicles-list"></ul>
						</div>
					</div>
				</div>	 -->						
				<!-- <div class="widget-filter">
					<div class="title accordion_tabs">
						<span data-i18n="colors">Couleurs</span>
						<input type="checkbox" id="color" class="filter"   data-modal="colors" />
						<label for="color" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
						<div>
							<ul></ul>
						</div>
					</div>
				</div> -->
			</form>
		</div>		
		<div class="col-s-12 col-l-9">
			<h1 class="section-title"><?=$this->editorial->designation;?></h1>
			<div class="short-description"></div>
			<div id="products" class="cards">				
		        <?php foreach ($cards as $k => $card) : $lazy = $k >  2 ? 'loading="lazy"' : '';?>
		            <div class="card">		                		                	
			            <img <?=$lazy;?> src="<?= $card->cover ?? '/img/blank.png'; ?>" alt="" >			                
		                    <h2 class="designation"><?= $card->designation; ?></h2>
		                    <p class="prices"><?=$card->old;?><?=$card->new;?></p>
		                	<a href="<?=$this->uri('dealsCorner.read',['queries'=>['section'=>$card->section, 'slug'=>$card->slug, 'id' => $card->id]])?>"></a>                         
		            </div>
		        <?php endforeach; ?>			
			</div>
			<?= $pagination; ?>
			<div class="bottom-description"></div>
		</div>
	</div>	
</div>	
</section>
<?php if(isset($_GET['debug'])): ?>
<pre>
<?php print_r($this->editorial) ?>

<?php print_r($this->alternate($this->editorial->l10ns)) ?>
</pre>

<?php endif; ?>