<?php if(count($this->filters) > 0): ?>
<form id="form-filter" action="" class="bloc-widgets-filter filter-form" data-ctrl="catalog.paginate">
	<a href="" class="click close" data-ctrl="product.closeFilters"><span class="material-symbols-rounded">&#xef7d;</span></a>
	<?php foreach($this->filters as $filter): ?>
		<?=$filter();?>
	<?php endforeach; ?>					
</form>
<?php endif; ?>