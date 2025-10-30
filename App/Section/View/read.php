<section class="row">
	<header class="col-s-12 col-l-9 col-l-push-3" >
		<h1 class="section-title"><?=$this->section->menu_id;?> <?=$this->section->title;?></h1>
	</header>	
	<div class="col-s-12 col-l-11 col-xl-10 col-center">
		<?php if(count($this->filters) > 0): ?>
			<p class="btn-filter"><button class="btn contained  click" data-ctrl="utils.autoHeight"><span data-i18n="filters">Filtres</span></button></p>
		<?php endif;?>
	<div class="row">
		<div class="col-s-12 col-l-3 gutter-lft-off ">
			<?= $filters;?>					
		</div>
		<div class="col-s-12 col-l-9">
		
		
		<?=$this->cards;?>		
		<?=$pagination;?>
		<div class="short-description"><?=$this->specialchars_decode($this->section->short_desc);?></div>
		<div class="further-info"><?=$this->specialchars_decode($this->section->further_info);?></div>
		<div class="bottom-description"></div>
		<pre>
			<?php print_r($this->section) ?>
		</pre>
		</div>
	</div>	
	</div>
</section>