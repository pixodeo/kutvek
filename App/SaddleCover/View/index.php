<nav role="navigation" aria-label="breadcrumb">
	  <ol itemscope itemtype="https://schema.org/BreadcrumbList" class="breadcrumb">
	    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
	    	<a href="<?=$this->url('pages.index');?>" itemprop="item"><span itemprop="name" data-i18n="homepage">Accueil</span></a>
	    	<meta itemprop="position" content="1" />
	    </li>         
	    <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
	    	<span itemprop="name"><?=$this->editorial->breadcrumb??$this->editorial->designation;?><span>
	    	<meta itemprop="position" content="2" />	
	    </li>
	  </ol>
	</nav>
<section class="row">
<div class="col-s-12 col-l-11 col-xl-10 col-center">	
	<img class="category-img" src="<?=$this->editorial->cover;?>" alt="" />	
	<div class="row">
		<div class="col-s-12 col-l-3 column-filter gutter-lft-off">
			<p class="btn-filter">
				<button class="btn contained dark click" data-ctrl="utils.autoHeight">
					<span data-i18n="filters">Filtres</span>
					<i class="material-symbols-rounded"></i>
				</button>
			</p>
			<form action="<?= $this->url('saddleCover.filter', ['queries' => ['slug'=>$this->editorial->slug]]); ?>" class="bloc-widgets-filter filter-form" data-refresh="<?= $this->url('saddleCover.refreshFilter', ['fqdn' => 1,'queries' => ['slug'=>$this->editorial->slug]]); ?>" id="filters-form">
				<div class="widget-filter">
					<div class="title accordion_tabs">
						<span data-i18n="families">Univers</span>
						<input type="checkbox" id="universe" class="filter" data-uri="<?= $this->url('products.filters', ['queries' => ['filter' => 'universe', 'slug' => $current_slug, 'depth' => $depth]]); ?>" data-modal="vehicles"  checked data-fetched="1" />
						<label for="universe" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
						<div>
							<ul>
								<?php foreach($universes as $u): 
									$checked = isset($this->getFilters()['universes']) && in_array($u->family_id, $this->getFilters()['universes']) ? 'checked' : '';
								?>	
								<li>
									<input name="universe[]" type="checkbox" class="field-input onchange" <?=$checked;?> value="<?= $u->family_id;?>" id="universe-<?= $u->family_id;?>" data-ctrl="saddles.filters" >
									<label for="universe-<?= $u->family_id;?>"><?= $u->family_name;?></label>
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
								<?php foreach($brands as $b): 
									$checked = isset($this->getFilters()['brands']) && in_array($b->brand_id, $this->getFilters()['brands']) ? 'checked' : '';
								?>	
								<li>
									<input name="brand[]" type="checkbox" <?=$checked;?> class="field-input onchange" value="<?= $b->brand_id;?>" id="brand-<?= $b->brand_id;?>" data-ctrl="saddles.filters" >
									<label for="brand-<?= $b->brand_id;?>"><?= $b->brand_name;?></label>
								</li>	
								<?php endforeach; ?>							
							</ul>
						</div>
					</div>
				</div>
				<div class="widget-filter">
					<div class="title accordion_tabs">
						<span data-i18n="vehicle">Véhicules</span>
						<input type="checkbox" id="vehicle" class="filter" data-uri="<?= $this->url('products.filters', ['queries' => ['filter' => 'vehicle', 'slug' => $current_slug, 'depth' => $depth]]); ?>" data-modal="vehicles" checked />
						<label for="vehicle" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
						<div>
							<ul id="vehicles-list"></ul>
						</div>
					</div>
				</div>							
				<div class="widget-filter">
					<div class="title accordion_tabs">
						<span data-i18n="colors">Couleurs</span>
						<input type="checkbox" id="color" class="filter" data-uri="<?= $this->url('products.filters', ['queries' => ['filter' => 'color', 'slug' => $current_slug, 'depth' => $depth]]); ?>"  data-modal="colors" />
						<label for="color" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
						<div>
							<ul id="colors-list">
								<?php foreach($colors as $c): 
									$checked = isset($this->getFilters()['colors']) && in_array($c->color_id, $this->getFilters()['colors']) ? 'checked' : '';
								?>	
								<li>
									<input name="color[]" type="checkbox" <?=$checked;?> class="field-input onchange" value="<?= $c->color_id;?>" id="color-<?= $c->color_id;?>" data-ctrl="saddles.filters" >
									<label for="color-<?= $c->color_id;?>"><?= $c->color_name;?></label>
								</li>	
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="col-s-12 col-l-9 content">
			<h1 class="section-title"><?=$this->editorial->designation;?></h1>
			<div class="short-description"><?=$this->specialchars_decode($this->editorial->description);?></div>

			<div id="products" class="cards">				
		        <?php foreach ($cards as $k => $card) : $lazy = $k >  2 ? 'loading="lazy"' : '';?>
		            <div class="card saddle-index">
		                <img <?=$lazy;?> src="<?= $card->cover ?? '/img/blank.png'; ?>" alt="" >			                
		                <h2 class="designation"><?= $card->designation; ?></h2>
		                <p class="prices"><?=$card->min;?></p>		                
		                <a href="<?=$this->url('saddleCover.read',['queries'=>['section'=>$card->section, 'slug'=>$card->slug, 'id' => $card->id]])?>"></a>                         
		            </div>
		        <?php endforeach; ?>			
			</div>
		<?= $pagination; ?>
		<div class="bottom-description"></div>
	</div>	
</div>
</section>
<pre>	
	<?php if(isset($_GET['debug'])): ?>
		<?php print_r($cards); ?>
		<?php print_r($this->editorial); ?>
	<?php endif; ?>
</pre>