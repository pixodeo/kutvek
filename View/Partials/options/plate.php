<?php 
$options = false; 
$accessories = false;
$finishes = false;
$finish = [];

if(property_exists($data, 'finish') && !empty($data->finish)) { 
	$finishes = true;
	$finish[] = $data->finish;
}
if(property_exists($data, 'premium') && !empty($data->premium)) {
	$finishes = true;
	$premium = json_decode($data->premium, true);
	$finish[] = $premium['name'];
}
if(property_exists($data, 'race_info') && !empty($data->race_info)){
	$race_info = array_filter(json_decode($data->race_info, true), 'strlen');
	$options = true;
} else {$race_info = [];}
if(property_exists($data, 'sponsors') && !empty($data->sponsors)){
	$sponsors = json_decode($data->sponsors);
	$options = true;
} else {
	$sponsors = [];
}
if(property_exists($data, 'switch') && !empty($data->switch)) {
	$switches = json_decode($data->switch, true);
	$options = true;
} else $switches = [];
$optSeatCover = array_filter(json_decode($data->seat_cover, true));
if(property_exists($data, 'seat_cover') && !empty($optSeatCover)){
	
	$accessories = true;
} 


?>
<div class="row">
	<?php if($finishes): $finish_designation = implode(' + ', $finish);?>
	<div class="col-s-12">
		<p class="h4 upper" data-i18n="finish">Finition</p>
		<p><?= $finish_designation;?></p>
	</div>
	<?php endif; ?>
	<?php if($options): ?>
		<div class="col-s-12"><p class="h4 upper">OPTIONS PERSO</p></div>
		<?php if(!empty($race_info)): ?>
		<div class="col-s-12 col-m-6">		
				<p><b data-i18n="name-plus-number">Nom + numéro</b></p>	
				<?php foreach($race_info AS $k => $info): ?>
					<?php switch($k):
						case 'name': 
							echo "<p class=\"item-option\"><span class=\"option-designation\" >Nom :</span><span>{$info}</span></p>";
							break;
						case 'name_typo': 
							echo "<p class=\"item-option\"><span class=\"option-designation\" >Typo du nom:</span><img src=\"/img/typo/{$info}\" width=\"160px\" alt=\"\" /></p>";
							break;
						case 'color': 
							echo "<p class=\"item-option\"><span class=\"option-designation\" >Couleur de fond :</span><span>{$info}</span></p>";
							break;
						case 'number': 
							echo "<p class=\"item-option\"><span class=\"option-designation\" >Numéro :</span><span>{$info}</span></p>";
							break;
						case 'number_typo':
							echo "<p class=\"item-option\"><span class=\"option-designation\" >Typo du numéro:</span><img src=\"/img/typo/{$info}\" width=\"160px\" alt=\"\" /></p>";			
							break;
						case 'number_color': 
							echo "<p class=\"item-option\"><span class=\"option-designation\" >Couleur du numéro :</span><span>{$info}</span></p>";
							break;
						case 'logo':
							echo "<p class=\"item-option\"><span class=\"option-designation\" >Logo:</span><img src=\"/img/plate-logo/{$info}\" width=\"160px\" alt=\"\" /></p>";			
							break;
					endswitch; ?>		
				<?php endforeach ?>		
		</div>
		<?php endif; ?>
		<?php if(!empty($sponsors)): ?>
			<div class="col-s-12 col-m-6">		
					<p><b>Sponsors</b></p>			
					<?php foreach($sponsors as $sponsor): $sponsor = is_string($sponsor) ? json_decode($sponsor) : $sponsor;?>
					<p class="item-option">
						<?php if($sponsor->file !== null && $sponsor->text !== null): ?>
							<span class="option-designation"><?= $sponsor->place?> : </span> <a class="link" target="_blank" href="<?= $sponsor->file;?>"><?= $sponsor->text;?></a>
						<?php elseif($sponsor->file !== null && $sponsor->text === null): ?>
							<span class="option-designation"><?= $sponsor->place?> : </span> <a class="link" target="_blank" href="<?= $sponsor->file;?>"><?= basename($sponsor->file);?></a>
						<?php else: ?>
							<span class="option-designation"><?= $sponsor->place?> : </span> <span><?= $sponsor->text;?></span>
						<?php endif; ?>
					</p>
					<?php endforeach; ?>
			</div>			
		<?php endif; ?>
		<?php if(!empty($witches)): ?>
		<div class="col-s-12 col-m-6">	
			<p><b data-i18n="switch-color">Switch couleurs</b></p>	
			<?php foreach($switches as $switch): ?>
				<p class="item-option">
					<span class="swicth-old"><?=$switch['old']?></span><span class="replaced-by" data-i18n="replaced-by">Remplacé par</span><span><?=$switch['new']?></span>
				</p>	
			<?php endforeach ?>
		</div>
		<?php endif; ?>	<?php endif; ?>
	<?php if($accessories): ?>
	<div class="col-s-12">
		<p class="h4 upper" data-i18n="accessories">Accessoires</p>
		
			<p class="item-option">
					<span><?=$optSeatCover['designation'];?></span>
			</p>	
		
	</div>
<?php endif; ?>
</div>