
<?php 
	$product = $this->product;
	$form = $this->form;
 ?>
<div class="col-s-12">                      
	<div class="row product" itemscope itemtype="https://schema.org/Product" data-product="<?=$product->id;?>">
		<div class="col-s-12">
			<p class="p-title"><?=$product->title;?><p>						
			<p class="p-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">   
			<span><?=$product->pricef;?></span>						
			</p>
			<hr>
		</div>
		
		<article class="col-s-12 col-l-9 " id="p-infos">
			<header>
				<h1 class="p-title"><?=$product->title;?></h1>
				<p class="p-favorites"><span class="icon material-symbols-rounded">favorite</span></p>
				<p class="p-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">   
				<span itemprop="price" content="<?=$product->price;?>"><?=$product->pricef;?></span>
				<span itemprop="priceCurrency" content="<?=$product->currency;?>"></span>
				<link itemprop="availability" href="https://schema.org/InStock" />
				<input type="hidden" name="item[description]" form="addToCart" value="<?=$product->title;?>" />			
				</p>
				<hr>
			</header>					
			<div class="row">
				<div class="col-s-12 col-l-7"><?= $this->widgetVehicles();?></div>
				<div class="col-s-6 col-l-5"><?= $this->widgetMillesims();?></div>	
				<div class="col-s-12 col-l-7"><?= $this->widgetType();?></div>
				<div class="col-s-12 col-l-5"><?= $this->widgetFinish();?></div>							
				<div class="col-s-12 col-l-6"><?= $this->widtgetSledColor();?></div>
				<div class="col-s-12 col-l-6"><?= $this->widgetTunnel();?></div>			
				<div class="col-s-12 col-l-7"><?= $this->widgetCylinder();?></div>
				<div class="col-s-12 col-l-5"><?= $this->widgetTurbo();?></div>
				<div class="col-s-12 col-l-7"><?= $this->widgetWider();?></div>
				<div class="col-s-12 col-l-5"><?= $this->widgetStarter();?></div>				
				<div class="col-s-12 col-l-5"><?= $this->widgetReverse();?></div>
				<div class="col-s-12">
					<div class="best-rendering">
					<?= '' //$this->bestRendering(); ?>
					</div>
					
				</div>

				<div class="col-s-12 bloc">
										
					<?= $this->widgetOptions();?>							
				</div>
				<div class="col-s-12 bloc">
					<hr>
					<span class="label">Pousse la personnalisation en décorant tes accessoires</span>
					<div id="accessories" class="row">
						<?= $this->widgetAdditionnals(); ?>						
					</div>
				</div>

			</div>	
			<div class="description">
			<ul class="tabs no-print">
				<li class="active"><a href="#description" data-i18n="product-desc">Description</a></li>		
				<li><a href="#install" data-i18n="laying-maintenance">Pose et entretien</a></li>						
			</ul>
			<div class="tabs_content">
				<div class="tab_content active" id="description">
					<div><?=$product->short_desc;?></div>
					<div><?=$product->desc1;?></div>
				</div>
				<div class="tab_content" id="install">
					<div><?=$product->desc2;?></div>
					<div><?=$product->desc3;?></div>
				</div>
			</div>
			</div>																		
		</article>
		<div class="col-s-12 col-l-3">
			<aside class="p-cart" id="p-cart">
				<header>
					<h4 class="p-cart-title" data-trad="<?=$this->lang;?>">TOTAL</h4>
				</header>						
				<div class="items">
					<p id="p-<?=$product->id;?>">
						<span class="designation"><?=$product->title;?></span>
						<span class="btns-group item-qty">
							<button class="btn square click" data-ctrl="item.decrease">-</button>
							<input type="text" id="qty" class="btn square" value="1" name="item[qty]" form="addToCart" />
							<button class="btn square click" data-ctrl="item.increase">+</button>
						</span>
						<input type="hidden" id="dc" value="<?=$product->dc;?>" data-uri="<?=SERVER_NAME?>/<?= $this->lang; ?>/golf-cart/json/typesKit" />
											
						<input type="hidden" name="item[price][opts]"  id="price-opts"  form="addToCart" value="0" />	
						<input type="hidden" name="item[price][accessories]"  id="price-accessories"  form="addToCart" value="0" />	
						
						<input type="hidden" name="item[product_img]" form="addToCart" value="<?=$product->visual;?>" />
						<input type="hidden" name="item[product_url]" form="addToCart" value="https://<?= $_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"] ?>" />
						<input type="hidden" name="item[design]" form="addToCart" value="<?=$product->design;?>">
						<input type="hidden" name="item[color]" form="addToCart" value="<?=$product->color;?>">
						<input type="hidden" name="item[comment]" form="addToCart" />
						<input type="hidden" name="item[licence]" form="addToCart" value="<?=$product->licence;?>" />
						<input type="hidden" name="item[poids]" form="addToCart" value="<?=$product->poids;?>" />
						<input type="hidden" name="item[category]" form="addToCart" value="<?=$product->category;?>" />

					</p>
					<div id="opts">
						<p class="type-opt" data-opt="STANDARD" data-id="2"></p>
						<p class="finish-opt" data-opt="Fini Brillant" data-id="4"></p>
						<p class="plate-sponsors" data-opt="" data-checked="0"></p>
						<p class="sponsors-only" data-opt="" data-checked="0"></p>
						<p class="switch" data-opt="" data-checked="0"></p>
					</div>							
				</div>
				<footer>
					<hr>
					<p id="item-total" data-currency="<?=$product->currency;?>" data-l10n="<?= $product->l10n;?>" data-price="<?= $product->price;?>"><?=$product->pricef;?></p>	
					<form action="" id="addToCart" data-ctrl="item.pushToCart">
						<input type="hidden" name="item[currency]" value="<?=$product->currency;?>" />
						<button class="btn contained dark addToCart" type="submit" class="btn contained dark" data-i18n="add-to-cart">Ajouter au panier</button>
					</form>
				</footer>					
			</aside>					
		</div>
	</div>				
</div>	
<pre><?php print_r($product)?></pre>
		
<aside class="modal click" id="addedToCart" data-modal="addedToCart" data-ctrl="app.modal" >
	<div class="popup mx-w45">
		<header class="close">
			<p class="title" data-i18n="added-to-cart">Ajouté au panier !</p>
			<a href="#addedToCart" class="click" data-modal="addedToCart" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
		</header>                            
		<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Architecto adipisci ea nostrum iusto consectetur doloremque, perferendis aperiam voluptatibus voluptatem neque ratione natus ullam recusandae deleniti vero rem obcaecati reprehenderit. Qui.</p>  
		<div class="btns-group btns-popup">
			<a href="/<?=$this->lang?>/can-am-store.html" class="btn contained dark" data-i18n="shop">Continuer mes achats</a>	
			<a href="/<?=$this->lang?>/cart.html" class="btn contained warning" data-i18n="order">Passer commande</a>
		</div>	
	</div>
</aside>
<aside class="modal click" id="plate-sponsors" data-modal="plate-sponsors" data-ctrl="app.modal" >
	<div class="popup mx-w45">
		<header class="close">					
			<p class="title" data-i18n="customize">Personnalisation</p>
			<a href="#plate-sponsors" class="click" data-modal="plate-sponsors" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
		</header>                            
		<ul class="tabs no-print">
				<li  data-input="plate"><a href="#opt-plate-number" data-i18n="name-plus-number">Nom + numéro</a></li>		
				<li data-input="plate-sponsor"><a href="#opt-sponsors">Sponsors</a></li>											
		</ul>
		<div class="tabs_content">
			<div class="tab_content" id="opt-plate-number" data-input="plate">
				<div class="row">
					<div class="col-s-12 col-l-6"><?=$this->widgetRaceName();?></div>
					<div class="col-s-12 col-l-6"><?= $this->widgetTypoRaceName();?></div>						
					<div class="col-s-12 col-l-6"><?= $this->widgetRaceNumber();?></div>
					<div class="col-s-12 col-l-6"><?= $this->widgetTypoRaceNumber();?></div>		
					<div class="col-s-12"><hr></div>
					<div class="col-s-12 col-l-6 col-l-push-6">
						<?= $this->widgetPlateColor();?>
					</div>
					<div class="col-s-12 col-l-6 col-l-push-6">
						<?= $this->widgetRaceNumberColor(); ?>
					</div>
					<div class="col-s-12 col-l-6 col-l-push-6"><?= $this->widgetRaceLogo();?></div>
				</div>
			</div>
			<div class="tab_content" id="opt-sponsors" data-input="plate-sponsor">
				<div><img src="/images/charte/<?=$product->template_sponsors;?>" /></div>
				<div class="grid-sponsors">
					<?php for ($i=1; $i <= $product->nb_sponsor; $i++):?>
						<p class="sponsor">
							<span class="place"><?= $i;?></span>								
							<input class="field-input text" type="text"  name="opts[sponsor][<?= $i;?>]"  data-i18n="sponsor-placeholder" placeholder="Nom du sponsor"/>
							<input class="file onchange" type="file" id="sp-<?= $i;?>" data-place="<?= $i;?>"  data-ctrl="option.uploadSponsor" />
							<label for="sp-<?= $i;?>">
								<span class="icon material-symbols-rounded">download</span>
							</label>
							<span class="fileName"></span>	
							
						</p>

					<?php endfor;?>														
				</div>
				
			</div>
		</div>
		<div class="btns-group btns-popup">					
			<a href="#plate-sponsors" data-modal="plate-sponsors" data-ctrl="app.modal" class="click btn contained dark" data-i18n="close">Fermer</a>
		</div>	
	</div>
</aside>

<aside class="modal click" id="sponsors-only" data-modal="sponsors-only" data-ctrl="app.modal" >
	<div class="popup mx-w45">
		<header class="close">					
			<p class="title" data-i18n="customize">Personnalisation</p>
			<a href="#sponsors-only" class="click" data-modal="sponsors-only" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
		</header>                            
		<ul class="tabs no-print">	
				<li data-input="sponsor"><a href="#opt-sponsors">Sponsors</a></li>											
		</ul>
		<div class="tabs_content">					
			<div class="tab_content" id="opt-sponsors" data-input="sponsor">
				<div><img src="/images/charte/<?=$product->template_sponsors;?>" /></div>
				<div class="grid-sponsors">
					<?php for ($i=1; $i <= $product->nb_sponsor; $i++):?>
						<p class="sponsor">
							<span class="place"><?= $i;?></span>								
							<input class="field-input text" type="text"  name="opts[sponsor][<?= $i;?>]"  data-i18n="sponsor-placeholder" placeholder="Nom du sponsor"/>
							<input class="file onchange" type="file" id="sp-<?= $i;?>" data-place="<?= $i;?>"  data-ctrl="option.uploadSponsor" />
							<label for="sp-<?= $i;?>">
								<span class="icon material-symbols-rounded">download</span>
							</label>
						</p>
						
					<?php endfor;?>
				</div>
			</div>
		</div>
		<div class="btns-group btns-popup">					
			<a href="#sponsors-only" data-modal="sponsors-only" data-ctrl="app.modal" class="click btn contained dark" data-i18n="close">Fermer</a>
		</div>	
	</div>
</aside>

<aside class="modal click" id="switch" data-modal="switch" data-ctrl="app.modal" >
	<div class="popup mx-w45">
		<header class="close">					
			<p class="title" data-i18n="customize">Personnalisation</p>
			<a href="#switch" class="click" data-modal="switch" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
		</header>                            
		<ul class="tabs no-print">						
				<li data-input="switch-color"><a href="#opt-switch" data-i18n="switch-color">Switch Couleur</a></li>						
		</ul>
		<div class="tabs_content">
			
			<div class="tab_content" id="opt-switch" data-input="switch-color">
				<div class="row">
					<div class="col-s-6"><?=$this->widgetSwitchColor(1, true);?></div>
					<div class="col-s-6"><?= $this->widgetSwitchColor(2);?></div>						
					<div class="col-s-6"><?= $this->widgetSwitchColor(3, true);?></div>
					<div class="col-s-6"><?= $this->widgetSwitchColor(4);?></div>		
					<div class="col-s-6"><?= $this->widgetSwitchColor(5, true);?></div>
					<div class="col-s-6"><?= $this->widgetSwitchColor(6);?></div>	
				</div>
			</div>
		</div>
		<div class="btns-group btns-popup">					
			<a href="#switch" data-modal="switch" data-ctrl="app.modal" class="click btn contained dark" data-i18n="close">Fermer</a>
		</div>	
	</div>
</aside>
<template id="p-header">
	<h1 class="p-title"><?=$product->title;?></h1>
	<p class="p-favorites"><i class="icon far fa-heart"></i></p>
	<p class="p-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">   
		<span itemprop="price" content="<?=$product->price;?>"><?=$product->pricef;?></span>
		<span itemprop="priceCurrency" content="<?=$product->currency;?>"></span>
		<link itemprop="availability" href="https://schema.org/InStock" />
		<input type="hidden" name="item[description]" form="addToCart" value="<?=$product->title;?>" />		
	</p>
	<hr>
</template>	