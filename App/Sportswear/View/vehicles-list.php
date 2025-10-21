<ul id="vehicles-list">
	<?php foreach($vehicles as $b): ?>	
	<li>
		<input name="vehicle[]" type="checkbox" class="field-input onchange" value="<?= $b->vehicle_id;?>" id="vehicle-<?= $b->vehicle_id;?>" data-ctrl="saddles.filters" <?= in_array($b->vehicle_id, $selected) ? 'checked' : ''; ?> >
		<label for="vehicle-<?= $b->vehicle_id;?>"><?= $b->vehicle_name;?></label>
	</li>	
	<?php endforeach; ?>							
</ul>