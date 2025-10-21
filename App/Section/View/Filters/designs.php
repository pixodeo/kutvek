<div class="widget-filter">
	<div class="title accordion_tabs">
		<span data-i18n="designs">Designs</span>
		<input type="checkbox" id="filter-design" class="filter" data-uri="/filters/design?" data-fetched="1">
		<label for="filter-design" class="pointer"><span class="material-symbols-rounded filters"></span></label>
		<div>
			<ul>
				<?php foreach($designs as $design): ?>						
				<li>
					<input name="design" type="checkbox" class="field-input onchange" value="<?=$design->id;?>" id="design-<?=$design->id;?>" data-ctrl="catalog.filter">
					<label title="<?=$design->name;?>" for="design-<?=$design->id;?>"><?=$design->name;?></label>
				</li>	
				<?php endforeach; ?>												
			</ul>
		</div>
	</div>
</div>