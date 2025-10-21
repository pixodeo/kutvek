<div class="row">
	<div class="col-s-12 col-l-9 col-center">
		<h1 class="page-title"><?php echo $brands[0]->s_name; ?></h1>
		<p><?= $app->getI18n() == 'fr' ? 'Le modèle affiché est à titre d\'exemple. Nous disposons des modèles proposés dans la liste des véhicules pour chacune des marques.' : 'The model shown is an example. We have the models offered in the list of vehicles for each of the brands.';?></p>	
	
		<div class="row c-evenly">
			<?php foreach($brands as $brand): ?>
				<a class="tile" href="<?= $brand->getUrl(); ?>">
					<?= $brand->b_name; ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</div>