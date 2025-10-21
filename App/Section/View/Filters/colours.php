<div class="widget-filter order-2">
	<div class="title accordion_tabs">
		<span data-i18n="colors">Coloris</span>
		<input type="checkbox" id="filter-color" class="filter" data-uri="/filters/color?"  checked data-fetched="1">
		<label for="filter-color" class="pointer"><span class="material-symbols-rounded filters"></span></label>
		<div>
			<ul>
				<?php foreach($colors as $color): ?>						
				<li>
					<input name="color" type="checkbox" class="field-input onchange" value="<?=$color->id;?>" id="color-<?=$color->id;?>" data-ctrl="catalog.filter">
					<label for="color-<?=$color->id;?>"><?=$color->name;?></label>
				</li>	
				<?php endforeach; ?>												
			</ul>
		</div>
	</div>
</div>