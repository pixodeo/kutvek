<?php
use App\Component\Product\Behavior\CustomSeatBehavior;
$this->_behavior = new CustomSeatBehavior(); 
$this->_behavior->setI18n($this->getI18n());
$this->_behavior->loadTable();
$this->_behavior->setRouter($this->_router);
$colors = $this->_behavior->colors();
?>
<section class="row">
	<div class="col-s-12 col-l-8 col-l-push-1">
		<div class="banner">
			<img src="<?= $page->cover ?? '/img/blank.png';?>" alt="<?= $page->title ?? '';?>" />
		</div>
		<h1 class="section-title"><?= $page->title ?></h1>
		<h2 class="short-description">
			<?= $page->short_description;?>
		</h2>
		<div class="row vehicle custom-bloc">
			<p class="header"><span>1.</span><span data-i18n="choose-vehicle">Sélectionne ton véhicule</span><p>				
			<?= $this->_behavior->widgetFamilies(); ?>		
			<?= $this->_behavior->widgetBrands(); ?>	
			<?= $this->_behavior->widgetModels(); ?>	
			<div class="col-s-12 col-m-3">
				<?= $this->_behavior->widgetVersions(); ?>		
			</div>
				
		</div>
		
		<div class="custom-bloc disabled" id="seat-cover">
			<p class="header"><span>2.</span><span data-i18n="customize-cover">Choisis tes couleurs</span></p>									
			<div class="row">
				<div class="col-s-12 col-m-3"><img src="/img/blank.png" alt="" id="seat-img" /></div>
				<div class="col-s-12 col-m-9">
					<div class="row">
						<div class="field-wrapper field-color col-s-12 col-m-6 col-m-pull-6 hide"  id="top">
							<label><span class="round">1</span><span data-i18n="top-color">Dessus</span></label>
							<input name="item[cover][color][top]" id="color-top" type="hidden" form="addToCart">
							<div class="field-input widgetTypo click" data-ctrl="app.widget" data-widget="widget-top">
								<label for="color-top" class="value click" data-ctrl="app.widget" data-widget="widget-top" data-i18n="choose">Choisir</label>
								<ul class="opt-list widget-hide" id="widget-top" data-input="color-top">
									<?php foreach($colors as $color): ?>
										<li class="click option color" data-value="<?=$color->id;?>" data-ctrl="app.widgetSeatColor">
											<span class="color-element <?= $color->class_name;?>"><?= $color->designation;?></span>
										</li>
									<?php endforeach; ?>								
								</ul>
							</div>
						</div>
						<div class="field-wrapper field-color col-s-12 col-m-6 col-m-pull-6 hide" id="side">
							<label><span class="round">2</span><span data-i18n="side-color">Côtés</span></label>
							<input name="item[cover][color][side]" id="color-side" type="hidden" form="addToCart">
							<div class="field-input widgetTypo click" data-ctrl="app.widget" data-widget="widget-top">
								<label for="color-side" class="value click" data-ctrl="app.widget" data-widget="widget-side" data-i18n="choose">Choisir</label>
								<ul class="opt-list widget-hide" id="widget-side" data-input="color-side">
									<?php foreach($colors as $color): ?>
										<li class="click option color" data-value="<?=$color->id;?>" data-ctrl="app.widgetSeatColor">
											<span class="color-element <?= $color->class_name;?>"><?= $color->designation;?></span>
										</li>
									<?php endforeach; ?>								
								</ul>
							</div>
						</div>
						<div class="field-wrapper field-color col-s-12 col-m-6 col-m-pull-6 hide" id="grip">
							<label><span class="round">3</span><span data-i18n="grip-color">Grips</span></label>
							<input name="item[cover][color][grip]" id="color-grip" type="hidden" form="addToCart">
							<div class="field-input widgetTypo click" data-ctrl="app.widget" data-widget="widget-grip">
								<label for="color-grip" class="value click" data-ctrl="app.widget" data-widget="widget-grip" data-i18n="choose">Choisir</label>
								<ul class="opt-list widget-hide" id="widget-grip" data-input="color-grip">
									<?php foreach($colors as $color): ?>
										<li class="click option color" data-value="<?=$color->id;?>" data-ctrl="app.widgetSeatColor">
											<span class="color-element <?= $color->class_name;?>"><?= $color->designation;?></span>
										</li>
									<?php endforeach; ?>								
								</ul>
							</div>
						</div>	
						
						<div id="outline" class="col-s-12 col-m-6 hide">
							<div data-i18n="cover-install-notice">
							<p class="install-notice">L'option housse de selle comprend l'installation (complexe), il sera donc nécessaire de nous faire parvenir la selle. 
								<br>Une fois votre commande passée nous vous contacterons pour vous indiquer la marche à suivre.
							</p>
							</div>
							<div class="field-wrapper field-color ">
								<label><span class="round">1</span><span data-i18n="outline-color">Changer les parties en couleur</span></label>
								<input name="item[cover][color][outline]" id="color-outline" type="hidden" form="addToCart">
								<div class="field-input widgetTypo click" data-ctrl="app.widget" data-widget="widget-outline">
									<label for="color-outline" class="value click" data-ctrl="app.widget" data-widget="widget-outline" data-i18n="choose">Choisir</label>
									<ul class="opt-list widget-hide" id="widget-outline" data-input="color-outline">
										<?php foreach($colors as $color): ?>
											<li class="click option color" data-value="<?=$color->id;?>" data-ctrl="app.widgetSeatColor">
												<span class="color-element <?= $color->class_name;?>"><?= $color->designation;?></span>
											</li>
										<?php endforeach; ?>								
									</ul>
								</div>
							</div>	
						</div>
					</div>
				</div>			
			</div>
		</div>
		<div class="custom-bloc disabled" id="seat-options">
			<p class="header"><span>3.</span><span data-i18n="customize-cover-option">Choisis tes options</span></p>
			<div class="row">
				<div class="field-wrapper column col-s-12 col-m-3 col-m-pull-9">				
				<label class="required" for="opt-foam">Mousse confort</label>
					<select name="item[price][foam]" data-i18n="opt-foam" id="opt-foam" class="field-input select onchange opts" data-ctrl="seatCustom.optFoam" data-opt="ComfortFoam" required="required">
						<option value="">Choisir</option> 
						<option value="10.00" data-name="Avec mousse confort" data-id="24">Avec mousse confort + 10,00&nbsp;€ </option> 
						<option value="0.00" data-name="Sans mousse confort" data-id="25">Sans mousse confort </option>
					</select>
				</div>
				<div class="field-wrapper column col-s-12 col-m-3 col-m-pull-9 hidden" id="install">
					<label class="required" for="opt-install">Installation</label>
					<select name="item[price][install]" data-i18n="opt-install" id="opt-install" class="field-input select onchange opts" data-ctrl="seatCustom.optInstall" data-opt="CoverInstall" required="required" >					
						<option value="0.00" data-name="Installation par mes soins" data-id="27" selected>Installation par mes soins </option> 
						<option value="20.00" data-name="Pose en atelier" data-id="26">Pose en atelier + 20,00&nbsp;€ </option>
					</select>
				</div>
			</div>
			
		</div>
		
	</div>
	<div class="col-m-3 shopping-cart">
		<aside id="p-cart">		
			<div class="header">TOTAL</div>	
			<div class="designation"> <span id="designation"></span><small id="price_f"></small></div>
			<div class="designation"> <span>Options</span><small id="price_o">0 <?= $this->getCurrencySymbol();?></small></div>
			<div class="designation"> <span class="total">Total</span><small id="price_t">0 <?= $this->getCurrencySymbol();?></small></div>
			<form action="<?= $this->uri('orders.addItem', [], 'POST'); ?>" method="post" data-ctrl="seatCustom.pushToCart" id="addToCart">
				<input type="hidden" name="item[price][product]" id="price" value="0" />
				<input type="hidden" name="item[price][opts]" id="price-opts" value="0" />
				<input type="hidden" name="item[product_url]"  value="https://<?= $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"] ?>" />					
				<input type="hidden" name="item[comment]"  />					
				<input type="hidden" name="item[weight]" value="500" />
				<input type="hidden" id="qty" name="item[qty]" value="1">
				<input type="hidden" name="item[category]" value="9" />
				<input type="hidden" name="item[description]" id="description" value="Housse de selle perso" />
				<input type="hidden" name="item[currency][designation]" id="currency" value="<?= $this->getCurrency();?>" />
				<input type="hidden" name="item[currency][id]" value="<?= $this->getCurrencyId();?>" />
				<input type="hidden" name="item[workspace]" value="<?= $this->getWorkspace();?>"/>
				<input type="hidden" id="l10n" value="<?=$this->getL10n();?>">
				<button class="btn contained dark addToCart " type="submit" id="btn-cart" class="btn contained dark"  disabled>
					<span class="text" data-i18n="add-to-cart">Ajouter au panier</span>
					<span class="icon material-symbols-rounded load hidden">progress_activity</span>
				</button>	
			</form>			
		</aside>
	</div>
</section>