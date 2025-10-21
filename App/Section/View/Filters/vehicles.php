<div class="widget-filter">
	<div class="title accordion_tabs">
		<span data-i18n="vehicles">Véhicules</span>
		<input type="checkbox" id="filter-vehicles" class="filter"  checked data-fetched="1">
		<label for="filter-vehicles" class="pointer"><span class="material-symbols-rounded filters"></span></label>
		<div>
			<ul>
				<?php foreach($vehicles as $vehicle): ?>						
				<li>
					<input name="vehicle" type="checkbox" class="field-input onchange" value="<?=$vehicle->id;?>" id="vehicle-<?=$vehicle->id;?>" data-ctrl="catalog.filter">
					<label for="vehicle-<?=$vehicle->id;?>"><?=$vehicle->name;?></label>
				</li>	
				<?php endforeach; ?>												
			</ul>
		</div>
	</div>
</div>