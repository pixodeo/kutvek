<div class="row filter-js">
	<?php if (count($cards) > 0) : ?>
		<span class="btn contained dark filters" data-i18n="filters">Filtres</span>
		
		<form class="bloc-widgets-filter filter-form" id="filters-form">
		<div class="widget-filter">
				<div class="title accordion_tabs">
					<span data-i18n="brands">Marques</span>
					<input type="checkbox" id="brand" class="filter" data-uri="<?= $this->uri('products.filters', ['queries' => ['filter' => 'brand', 'slug' => $current_slug, 'depth' => $depth]]); ?>"  data-modal="brands" />
					<label for="brand" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
					<div>
						<ul></ul>
					</div>
				</div>
			</div>
			<div class="widget-filter">
				<div class="title accordion_tabs">
					<span>Designs</span>
					<input type="checkbox" id="design" class="filter" data-uri="<?= $this->uri('products.filters', ['queries' => ['filter' => 'design', 'slug' => $current_slug, 'depth' => $depth]]); ?>"  data-modal="designs" />
					<label for="design" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
					<div>
						<ul></ul>
					</div>
				</div>
			</div>
			<div class="widget-filter">
				<div class="title accordion_tabs">
					<span data-i18n="colors">Couleurs</span>
					<input type="checkbox" id="color" class="filter" data-uri="<?= $this->uri('products.filters', ['queries' => ['filter' => 'color', 'slug' => $current_slug, 'depth' => $depth]]); ?>"  data-modal="colors" />
					<label for="color" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
					<div>
						<ul></ul>
					</div>
				</div>
			</div>
		</form>
	<?php endif; ?>
</div>