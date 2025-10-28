<p><?=$this->section->menu_id;?></p>
<section class="row">
	<div class="col-s-12 col-l-11 col-xl-10 col-center">
		<?php if(count($this->filters) > 0): ?>
			<p class="btn-filter">
				<button class="btn contained dark click" data-ctrl="utils.autoHeight">
					<span data-i18n="filters">Filtres</span>
					<i class="material-symbols-rounded"></i>
				</button>
			</p>
		<?php endif;?>
	<div class="row">
		<div class="col-s-12 col-l-3 gutter-lft-off ">
			<?= $filters;?>					
		</div>
		<div class="col-s-12 col-l-9">
		<h1 class="section-title"><?=$this->section->title;?></h1>
		<div class="short-description"><?=$this->specialchars_decode($this->section->short_desc);?></div>
		<div class="further-info"><?=$this->specialchars_decode($this->section->further_info);?></div>		
		<?=$this->cards;?>		
		<?=$pagination;?>
		<div class="bottom-description"></div>
		
		<pre>
			<?php print_r($this->section) ?>
		</pre>
		</div>
	</div>	
	</div>
</section>