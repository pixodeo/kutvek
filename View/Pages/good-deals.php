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
								<span data-i18n="family">Univers/Famille</span>
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
								<span>Véhicules</span>
								<input type="checkbox" id="vehicle" class="filter" data-uri="" data-modal="vehicles"   />
								<label for="vehicle" class="pointer"><span class="material-symbols-rounded filters"></span></label>
								<ul>						
									<?php foreach($vehicles as $k => $name) : ?>
									<li class="field-wrapper checkbox">							
										<input name="vehicle[]" type="checkbox" class="field-input onchange" value="<?=$k;?>" data-i18n="vehicle" id="vehicle-<?= $k;?>" >
										<label class="checkbox no-colon" for="vehicle-<?= $k;?>"><?= $name;?></label>							
									</li>
									<?php endforeach; ?>
								</ul>						
							</div>
						</div>						
					</form>
				</div>				
			</div>
			<div class="col-s-12 col-l-9">
				<?php  if($section_content && $section_content->designation):?>
				<h1 class="section-title"><?= $section_content->designation; ?></h1>
				<?php endif; ?>
				<?php if($section_content && $section_content->short): ?>	
				<h2 class="short-description"><?= $section_content->short; ?></h2>
				<?php endif; ?>	
				<div class="vehicle-info"><?= $section_content->vehicles_info ?? '' ;?></div>
				<div id="products" class="product-item-container">
					<?php foreach ($cards as $key => $card) : ?>
						<figure id="<?= $card->id; ?>" class="product-item"  data-design="<?= $card->design; ?>" data-color="<?= $card->color; ?>">
							
								<img loading="lazy" class="visual" src="<?= $card->visual; ?>" />
							
							<hr>
							<figcaption>						
								<p class="item"><?= $card->designation; ?></p>

								<p class="prices"><span class="price stroke"><?= $card->price_f; ?></span>
									<span class="price sale">
										<?= $card->sale_price; ?>
									</span>
								</p>
							</figcaption>
							<a href="<?= $this->uri($card->url); ?>"></a>
						</figure>
					<?php endforeach; ?>
				</div>
				<?= $pagination; ?>
			</div>
		</div>
		<div class="bottom-description">
			<?= $section_content->description ?? '';?>			
		</div>	
		<!-- TrustBox widget - Slider -->
		<div class="row trustpilot-slider">
			<div class="col-s-12 col-m-9 col-m-center">
				<div class="trustpilot-widget bottom" data-locale="<?=$this->getLang();?>" data-template-id="54ad5defc6454f065c28af8b" data-businessunit-id="5c10d1e8416bce0001137d41" data-style-height="240px" data-style-width="100%" data-theme="light" data-stars="3,4,5">
					<a href="https://fr.trustpilot.com/review/kutvek-kitgraphik.com" target="_blank" rel="noopener">Trustpilot</a> 
				</div>
			</div>
		</div>	
	</div>	
</section>
<pre><?php //print_r($query); ?></pre>