<h1 class="page-title">Catégory <?php echo $brands[0]->title; ?></h1>
<p><?= $app->getI18n() == 'fr' ? 'Le modèle affiché est à titre d\'exemple. Nous disposons des modèles proposés dans la liste des véhicules pour chacune des marques.' : 'The model shown is an example. We have the models offered in the list of vehicles for each of the brands.';?></p>		
		<div class="col-s-12 col-l-9 col-l-center ">
			<div class="row">
		    <?php foreach($brands as $brand): ?>
				<div class="col-s-12 col-l-4 tile"><a href="/<?=$lang;?>/<?= $brand->store_url;?>/<?=$brand->slug;?>" ><img src="https://www.kutvek-amerika.com/img/categories/golf-cart/<?= $brand->img;?>" /></a>
				</div>	       		
	    	<?php endforeach;?>	
		   
			</div>			
		</div>
<pre>
	<?php //print_r($brands); ?>
</pre>

