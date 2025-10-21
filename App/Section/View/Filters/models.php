<div class="widget-filter order-1">
	<div class="title accordion_tabs">
		<span data-i18n="models">Modèles</span>
		<input type="checkbox" id="model" class="filter" data-uri="/filters/model?" data-modal="vehicles" checked="" data-fetched="1">
		<label for="model" class="pointer"><span class="material-symbols-rounded filters"></span></label>
		<div>
			<ul>
				<?php foreach($models as $model): ?>						
				<li>
					<input name="model" type="checkbox" class="field-input onchange" value="<?=$model->id;?>" id="model-<?=$model->id;?>" data-ctrl="catalog.filter">
					<label for="model-<?=$model->id;?>"><?=$model->name;?></label>
				</li>	
				<?php endforeach; ?>												
			</ul>
		</div>
	</div>
</div>