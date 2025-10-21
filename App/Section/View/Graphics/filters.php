<?php $cards = $this->getCards(); ?>
<?php $s = $this->trimUrl($this->getRequest()->getUri()->getPath(), '/'); ?>
<div class="row filter-js">
	<?php if (count($cards) > 0) : ?>
		<label class="btn contained dark filters" for="filter-btn" data-i18n="filters">Filtres</label>
		<input type="checkbox" hidden name="filter-btn" id="filter-btn">
		<form class="bloc-widgets-filter filter-form" id="filters-form">
			<div class="widget-filter">
				<div class="title accordion_tabs">
					<span>Designs</span>
					<input type="checkbox" id="design" class="filter onchange" data-modal="designs" data-ctrl="catalog.loadFilterData" data-fetched="0" data-uri="<?=$this->uri('section.filter',['fqdn'=>1,'queries'=>['slug'=>$s, 'filter'=>'design']])?>"/>
					<label for="design" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
					<div>
						<ul></ul>
					</div>
				</div>
			</div>
			<div class="widget-filter">
				<div class="title accordion_tabs">
					<span data-i18n="colors">Couleurs</span>
					<input type="checkbox" id="color" class="filter onchange"  data-modal="colors" data-ctrl="catalog.loadFilterData" data-fetched="0" data-uri="<?=$this->uri('section.filter',['fqdn'=>1,'queries'=>['slug'=>$s, 'filter'=>'color']])?>"/>
					<label for="color" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
					<div>
						<ul></ul>
					</div>
				</div>
			</div>
		</form>
	<?php endif; ?>
</div>