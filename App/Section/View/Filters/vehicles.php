<div class="widget-filter order-1">
	<div class="title accordion_tabs">
		<span data-i18n="vehicles">Véhicules</span>
		<input type="checkbox" id="filter-vehicles" class="filter"  checked data-fetched="1">
		<label for="filter-vehicles" class="pointer"><span class="material-symbols-rounded filters"></span></label>
		<div>
			<ul>
				<?php foreach($vehicles as $vehicle): ?>
				<?php $checked = (property_exists($this->queryResult, 'vehicle') && in_array($vehicle->id, $this->queryResult->vehicle)) ? 'checked' : '';?>
				<li>
					<input name="vehicle" type="checkbox" class="field-input onchange" value="<?=$vehicle->id;?>" id="v-<?=$vehicle->id;?>" data-ctrl="catalog.filter" <?=$checked;?> />
					<label for="v-<?=$vehicle->id;?>"><?=$vehicle->name;?></label>
				</li>	
				<?php endforeach; ?>												
			</ul>
		</div>
	</div>
</div>