<section class="row">
	<div class="col-s-12 col-l-9 col-l-center">
		<div class="widget-custom"></div>
		<div class="row">
			<div class="col-s-12 col-m-3 column-filter">
				<div class="row filter-js">
					<span class="btn contained dark filters" data-i18n="filters">Filtres</span>
					<form action="" class="bloc-widgets-filter filter-form" id="filters-form">
						<div class="widget-filter">	
							<div class="title accordion_tabs">
								<span data-i18n="vehicle-type">Univers/Famille</span>
								<input type="checkbox" id="family" class="filter" data-uri="" data-modal="categories" data-fetched="1" />
								<label for="family" class="pointer"><span class="material-symbols-rounded filters"></span></label>
								<ul>
									<?php foreach($families as $f_k => $f_name) : ?>
									<li class="field-wrapper checkbox">							
										<input name="family[]" type="checkbox" class="field-input onchange" value="<?=$f_k;?>"  id="family-<?= $f_k;?>">
										<label class="checkbox no-colon" for="family-<?= $f_k;?>"><?= $f_name;?></label>							
									</li>
									<?php endforeach; ?>
								</ul>						
							</div>
						</div>
						<div class="widget-filter">
							<div class="title accordion_tabs">
								<span data-i18n="category">Catégorie</span>
								<input type="checkbox" id="category" class="filter" data-uri="" data-modal="vehicles" data-fetched="1" checked />
								<label for="category" class="pointer"><span class="material-symbols-rounded filters"></span></label>
								<ul>						
									<?php foreach($categories as $k => $name) : ?>
									<li class="field-wrapper checkbox">							
										<input name="category[]" type="checkbox" class="field-input onchange" value="<?=$k;?>"  id="category-<?= $k;?>" >
										<label class="checkbox no-colon" for="category-<?= $k;?>"><?= $name;?></label>							
									</li>
									<?php endforeach; ?>
								</ul>							
							</div>
						</div>						
					</form>
				</div>				
			</div>
			<div class="col-s-12 col-l-9">
				<div id="products" class="product-item-container">
					<?php foreach ($cards as $key => $card) : ?>
						<figure id="<?= $card->id; ?>" class="product-item" data-brand="<?= $card->brand; ?>" data-design="<?= $card->design; ?>" data-color="<?= $card->color; ?>">
							<?php if (isset($card->visual)) : ?>
								<img loading="lazy" class="visual" src="<?= $card->visual; ?>" />
							<?php else : ?>
								<img loading="lazy" class="visual" src="/img/blank.png" />
							<?php endif; ?>
							<hr>
							<figcaption>						
								<p class="item"><?= $card->designation; ?></p>
								<p><span class="price block"><?= $card->price_0; ?></span></p>
							</figcaption>
							<a href="<?= $card->url; ?>"></a>
						</figure>
					<?php endforeach; ?>
				</div>
				<?= $pagination; ?>
			</div>
		</div>		
	</div>	
</section>
<?php if(isset($_GET['debug'])): ?>
<pre><?php print_r($cards) ?></pre>
<?php endif; ?>