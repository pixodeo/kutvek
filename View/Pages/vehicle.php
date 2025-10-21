<script>alert('caca')</script>
<?php $last = array_pop($breadcrumbs) ?>
	<nav aria-label="Breadcrumb" class="breadcrumb">
	    <ul>
	        <li><a href="<?= $this->uri('pages.index', [])?>"><span class="material-symbols-rounded">home</span></a></li>
	        <?php foreach ($breadcrumbs as $breadcrumb): ?>
	        	<li><a href="<?=$breadcrumb->url;?>"><?= $breadcrumb->text;?></a></li>
	        <?php endforeach ?>	        
	        <li><span aria-current="page"><?=$last->text;?></span></li>
	    </ul>
	</nav>
<div class="row">
	<div class="col-s-12 col-l-9 col-l-offset-3">
		<h1 class="page-title"><?php $cards[0]->getPageTitle(); ?></h1>
		<p><?= $app->getI18n() == 'fr' ? 'Le modèle affiché est à titre d\'exemple. Nous disposons des modèles proposés dans la liste des véhicules pour chacune des marques.' : 'The model shown is an example. We have the models offered in the list of vehicles for each of the brands.';?></p>	
	</div>
	<div class="col-s-12 col-l-2">
		<form action="<?=$this->uri('pages.testBrands', ['queries' => ['store' => $store]]);?>" id="filter" class="form-filters">
		<?= $this->widgetBrandsMenu($brands, $brand, $vehicle);?>
		</form>	
	</div>
	<div class="col-s-12 col-l-9 col-l-offset-1">

		<div id="cards-container" class="product-item-container">
			<?php foreach($cards AS $card):?>
				<figure id="<?=$card->id;?>" class="product-item" data-brand="<?=$card->brand;?>" data-desing="<?=$card->design;?>" data-color="<?=$card->color;?>">
				<?php if(isset($card->visual)): ?>
					
					<img class="visual" src="<?=$card->visual;?>" />		

				<?php else: ?>
					<img class="visual" src="/img/blank.png" />	
				
				<?php endif; ?>
				<hr>	
				<figcaption>
			        <h3 class="item"><?= $card->title;?></h3>
			    		<p><span class="price block"><?= $card->price_0;?></span></p>
			    </figcaption>	
			    <a href="/<?= $card->url; ?>"></a>
				</figure>
			<?php endforeach;?>
		</div>
	</div>
</div>
<template id="card-tpl">
	<figure  class="product-item" data-brand data-design data-color>
		<img class="visual" />
		<hr>	
		<figcaption>
	        <h3 class="item"></h3>
	    	<p><span class="price block"></span></p>
	    </figcaption>	
	    <a></a>
	</figure>
</template>
<template id="vehicle-checkbox-tpl">
	<li>
		<?= $this->form->checkbox(
        "vehicles[]",
        array(
            'label' => ['text' => '', 'class' => 'filter-checkbox'],
            'wrapper' => array('class' => 'no-flex'),                    
            'attributes' => array(
                'class="onchange"',
                'data-ctrl="product.filterByVehicle"',
                'data-uri="'. $this->uri('products.filterByVehicle', ['queries' => ['store' => $store, 'vehicle' => ':vehicle']]) .'"'                   
            )
        )
    );?>
	</li>	
</template>
<pre><?php print_r($this->getRouter()->getRouting())?></pre>