<?php if(count($this->filters) > 0): ?>
<p class="btn-filter">
	<button class="btn contained dark click" data-ctrl="utils.autoHeight">
		<span data-i18n="filters">Filtres</span>
		<i class="material-symbols-rounded"></i>
	</button>
</p>
<form id="form-filter" action="" class="bloc-widgets-filter filter-form" data-ctrl="catalog.paginate">
	<?php foreach($this->filters as $filter): ?>
		<?=$filter();?>
	<?php endforeach; ?>					
</form>
<?php endif; ?>