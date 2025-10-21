<div class="row">
    <div  class="col-s-12 col-m-6 col-m-offset-1 checkout-actions">
		<div class="tranquility">
			<div class="accordion_tabs">
				<h5 class="titles"><span class="step"><span class="number">1</span><span  class="icon material-symbols-rounded">check</span></span><span data-i18n="info-perso">Mes infos personelles</span></h5>
				<input type="checkbox" id="email-input"/>
				<label for="email-input" class="pointer"><span class="material-symbols-rounded"></span></label>
				<div>
					<form id="form-customer" class="row">
						<div class="col-s-12 col-m-7">						
							<div class="field-wrapper ">
					            <label for="lastname" class="required">Nom</label>
					            <input name="lastname" id="lastname" data-i18n="lastname" type="text" class="field-input" required />
					        </div>
					        <div class="field-wrapper ">
					            <label for="firstname" class="required" >Prénom</label>
					            <input name="firstname" id="firstname" data-i18n="firstname" type="text" class="field-input" required />
					        </div>
					        <div class="field-wrapper"><label for="company">Société</label><input name="company" id="company" type="text" data-i18n="company" class="field-input"></div>
					        <div class="field-wrapper">
					            <label for="phone">Téléphone fixe</label>
					            <input name="phone" id="phone" type="text" class="field-input" data-i18n="phone">
					        </div>
					        <div class="field-wrapper">
					            <label for="cellphone" class="required">Téléphone portable</label>
					            <input name="cellphone" id="cellphone" type="text" class="field-input" data-i18n="cellphone" required>
					        </div>
					        <input type="hidden" name="uid" id="user-input" />
					        <input type="submit" class="hidden" value="" >
					    </div>
					</form>					
					<div class="already-customer hide">
						<span data-i18n="already-registered"> Vous êtes déjà client(e) ?</span>
						<a class="link" href="<?= $this->uri('identities.login',['queries' => ['redirect' => 'orders.checkout']])?>">Connexion</a>	
						<span data-i18n="or">ou</span>
						<a class="link" data-i18n="signup" href="<?= $this->uri('identities.signup',['queries' => ['redirect' => 'identities.login']])?>">Inscription rapide</a>

					</div>
				</div>				
			</div>		
			<div class="accordion_tabs">
				<h5 class="titles"><span class="step"><span class="number">2</span><span  class="icon material-symbols-rounded">check</span></span><span data-i18n="shipping-method">Mode de livraison</span></h5>
				<input type="checkbox" id="shipping-method-input" disabled />
				<label for="shipping-method-input" class="pointer"><span class="material-symbols-rounded"></span></label>
				<div>
					
					<ul class="tabs tabs-checkout">
						<li class="active">
							<a href="#home-delivery" ><span class="icon material-symbols-rounded">house</span><span data-i18n="home-delivery">à domicile</span>
							</a>
						</li>		
						<li><a href="#click-and-collect" id="link-to-relay" class="click" data-ctrl="delivery.initMap"><span class="icon material-symbols-rounded">location_on</span><span data-i18n="click-and-collect">En point relais</span></a></li>	
						<li><a href="#pickup"><span class="icon material-symbols-rounded">store</span><span data-i18n="pickup">Retrait sur place</span></a></li>
					</ul>
					<div class="tabs_content">
						<div class="tab_content active" id="home-delivery">
							<div class="row">
								
								<div id="shipping-address" class="col-s-12 col-m-6 col-m-pull-1">
									<div class="shipping-default"></div>
									<div>
									<a href="#new-address" class="btn contained dark click" id="change-address" data-modal="new-address" data-ctrl="app.modal" data-i18n="modify-shipping">Changer l'adresse</a>
									
								</div>
								</div>
								<form action="<?=$this->uri('orders.shippingFees', ['queries' => ['order' => ':order']], 'POST')?>" id="user-address" method="post" data-ctrl="cart.stdDelivery" class="col-s-12 col-m-5">
									<input type="hidden" id="fees-address" name="address" />		
									<div class="delivery-method chrono-classic hide">
										<p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO Classic</small></p>
										<p><span data-i18n="cost">Coût</span> <small class="cost"></small><small class="free hide" data-i18n="free-shipping">Offerts</small></p>
										<p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="3-working-days">3 jours ouvrables</span></p>
										<p class="btns action">	            						
		            						<button  type="submit" data-type="classic" class="btn contained primary" name="cost" value="" data-i18n="choose-delivery-method"  id="std-classic" >Choisir ce mode de livraison</button>
		        						</p>
									</div>
									<div class="delivery-method chrono-express hide">
										<p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO Express</small></p>
										<p><span data-i18n="cost">Coût</span> <small class="cost"></small></p>
										<p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="1-working-days">1 jour ouvrable</span></p>	
										<p class="btns action">											
		            						<button  type="submit" data-type="express" class="btn contained primary" name="cost" value="" data-i18n="choose-delivery-method"  id="std-express"  >Choisir ce mode de livraison</button>
		        						</p>	
									</div>
									<div class="delivery-method chrono-express-intl hide">
										<p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO Express</small></p>
										<p><span data-i18n="cost">Coût</span> <small class="cost"></small></p>
										<p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="3-working-days">3 jour ouvrables</span></p>	
										<p class="btns action">											
		            						<button  type="submit" data-type="express" class="btn contained primary" name="cost" value="" data-i18n="choose-delivery-method"  id="std-intl"  >Choisir ce mode de livraison</button>
		        						</p>	
									</div>
									<div class="delivery-method chrono-13 hide">
										<p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO 13</small></p>
										<p><span data-i18n="cost">Coût</span> <small class="cost"></small></p>
										<p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="1-working-days">1 jour ouvrable</span></p>	
										<p class="btns action">											
		            						<button  type="submit" data-type="classic_13" class="btn contained primary" name="cost" value="" data-i18n="choose-delivery-method"  id="std-13"  >Choisir ce mode de livraison</button>
		        						</p>	
									</div>
								</form>
							</div>														
						</div>
						<div class="tab_content" id="click-and-collect">
							<div class="row">
								<div class="col-s-12 col-m-3 col-m-pull-4">
								<form id="search-place" data-ctrl="delivery.points">
							        	<div class="field-wrapper column">
		            							<label for="search-zipcode" class="required">Code postal</label>
		            							<input name="postal_code" id="search-zipcode" type="text" class="field-input" data-i18n="zipcode" required>
		        						</div>
		        						<div class="field-wrapper column">
		            							<label for="search-city" class="required">Ville</label>
		            							<input name="admin_area_2" id="search-city" type="text" class="field-input" data-i18n="city" required>
		        						</div>
		        						<input type="hidden" name="country_code" value="FR"/>
		        						<div class="field-wrapper">
		        							<button class="contained primary" type="submit"><span class="icon material-symbols-rounded">&#xe8b6;</span> Rechercher</button>
		        						</div>
							        </form>
							</div>
							<div class="col-s-12 col-m-5">
								<div class="delivery-method chrono-relay hide">
								<p>Méthode de livraison <small>CHRONO Relay</small></p>
								<p><span>Coût</span> <small class="cost"></small></p>
								<p><span>Délai de livraison</span> 3 jours ouvrables</p>
								<input type="hidden" name="cost" id="chrono-relay"  form="form-relay" value="0.00" />				
								</div>	
							</div>
							</div>						
							<div class="row map-container">
							    <div class="col-s-12 col-l-3 sidebar">						        
							       <h3 data-i18n="listing-relay">Liste des points relais</h3>
							        <div id="listings" class="listings"></div>
							    </div>
							     <div id="map" class="col-s-12 col-l-9 map"></div>
		    				</div>
							<div class="bloc-carrier">										
							</div>
						</div>
						<div class="tab_content pickup" id="pickup">
							<p data-i18n="free-pickup" class="delivery-type">Gratuit, récupérez votre colis directement en magasin</p>
							<p class="address_line_1"></p>
							<p class="address_line_2"></p>
							<p><span class="postal_code"></span><span class="admin_area_2"></span></p>
							<form action="<?= $this->uri('orders.pickupAddress', [], 'POST') ?>" id="form-pickup" data-ctrl="cart.pickupAddress">
								<input type="hidden" name="address[delivery_address]" />
								<input type="hidden" name="address[com_shipping]" value="0">
								<input type="hidden" name="address[delivery_type]" value="2" />
								<p class="btns action"><button type="submit" class="btn contained  primary" data-i18n="choose-delivery-method" id="pickup">Choisir ce mode livraison</button></p>
							</form>
						</div>		
					</div>
				<form id="form-relay" action="<?=$this->uri('orders.chronoRelayAddress', [], 'POST')?>" data-ctrl="cart.address"></form>
				</div>
			</div>
			<div class="accordion_tabs">
				<h5 class="titles"><span class="step"><span class="number">3</span><span  class="icon material-symbols-rounded">check</span></span><span data-i18n="pay">Paiement</span></h5>
				<input type="checkbox" id="pay-input" disabled />
				<label for="pay-input" class="pointer"><span class="material-symbols-rounded"></span></label>
				<div>				
					<div class="row relative">
						<div id="accept-cgv" class="accept-cgv off"></div>						
						<div class="col-s-12 col-l-6 col-l-center checkout-methods">
							<div id="gift-card-payment" class="payment-items  pay-gift-card hide">
								<p><b>Payer avec e-carte cadeau</b></p>
								<div id="gift-card-error" class="error-msg">
									<div class="h5"></div>
									<div></div>
								</div>
								<form action="<?=$this->uri('orders.pay', ['queries'=>['psp' => 'giftCard', 'order' => ':order']], 'POST')?>" class="card-form row" data-ctrl="cart.payWithGiftCard">
									<div class="col-s-12">
						    			<label for="gift-card-serial" class="required" data-i18n="serial-number">Numéro de carte à 9 chiffres</small></label>
						    			<input type="tel" id="gift-card-serial" name="serial"  class="card_field col-s-12" pattern="[0-9]{3} [0-9]{3} [0-9]{3}" required />	
						    		</div>	
						    		<div class="col-s-12">
									    <label for="pin" class="required" data-i18n="pin-code" >Code secret à 4 chiffres</label>
									    <input type="text" id="pin" name="pin"   class="card_field col-s-12" required />										
									</div>
									<div class="col-s-12 col-m-9 col-m-center">
									 	<button class="btn contained dark paypal">
											 <span class="icon material-symbols-rounded">&#xe8b1;</span>
											 <span data-i18n="pay-creditcard">Payer avec votre e-carte cadeau</span>
										</button>
									</div>
								</form>
							</div>
							<div class="flex decorate hide"> <span></span> <b data-i18n="or">OU</b> <span></span></div>
							<div id="pro-payment" class="payment-items  pay-later hide">
								<div class="pro-features">
									<img src="/img/payment/pro.jpg" width="56"  height="56" alt="" />
									<span data-i18n="pro-conditions" >Avantage PRO</span>
								</div>
								<span class="pay-when-shipping" data-i18n="pay-when-shipping">Payez à l'expédition</span>
								<button  class="btn contained dark click" data-ctrl="cart.payLater" data-url="<?=$this->uri('orders.approve', ['queries'=>['psp' => 'payLater', 'order' => ':order']], 'POST')?>">
									  <span class="icon material-symbols-rounded">&#xe876;</span>
									  <span data-i18n="pay-later">Teminer la commande</span>
								</button>
							</div>
							<div class="flex decorate hide"> <span></span> <b data-i18n="or">OU</b> <span></span></div>
							<div class="payment-items">
								<div id="paypal-button-container"></div>
							</div>
							<div class="flex decorate"> <span></span > <b data-i18n="or">OU</b> <span></span></div>
							<div  class="paycard-header">								
								<span data-i18n="pay-creditcard">Payer avec votre carte</span>
								<img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/cc-badges-ppmcvdam.png" alt="Buy now with PayPal" />
							</div>
							<div class="payment-items">							
								<div class="card_container">
						    		<form id="card-form" class="card-form row">
						    			<div class="col-s-12">
						    				<label for="card-number" data-i18n="card-number">Numéro de carte</label>
						    				<div id="card-number" class="col-s-12 card_field"></div> 
						    			</div>	
						    			<div class="col-s-12">
									    	<label for="card-holder-name" data-i18n="name-on-card" >Titulaire de la carte</label>
									    	<input type="text" id="card-holder-name" name="card-holder-name"  data-i18n="name-on-card" placeholder="Jean DUPONT" class="card_field col-s-12"/>										
									    </div>				    			
						    			<div class="col-s-12 col-m-6">
									      <label for="expiration-date" data-i18n="expiration-date">Expire à fin</label>
									      <div id="expiration-date" class="card_field"></div>
									  	</div>
						    			<div class="col-s-12 col-m-6">
									      <label for="cvv" data-i18n="cvv">Code de sécurité</label>
									      <div id="cvv" class="card_field"></div>									      
									    </div>  
									    <div class="col-s-12">
									    	<button  value="submit" id="submit" class="btn contained dark paypal">
									    		<span class="icon material-symbols-rounded">credit_card</span>
									    		<span data-i18n="pay-creditcard">Payer avec votre carte</span>
									    	</button>
									    </div>								     
							       	</form>
						   		</div>
							</div>
						</div>
						
					</div>	
				</div>	
			</div>
		</div>
    </div>
    <div id="checkout" class="col-s-12 col-m-3 col-m-offset-1 checkout-cart">
    	<h5 data-i18n="my-cart" class="titles">Mon panier</h5>
		<div id="items"></div>
    	<h5 class="titles sub-total"><span data-i18n="sub-total">Sous-total</span><span id="item-total"></span></h5>    	   	
    	<p class="titles sub-total"><small data-i18n="delivery">Livraison</small><small id="shipping-amount"></small></p> 
    	<div id="shipping-info" class="shipping-method"></div>
    	<div id="discount" class="hidden">
    	</div>  	
    	<hr>
    	<h5 class="titles sub-total"><span data-i18n="total-amount">Total à régler</span><span id="total-to-pay"></span></h5> 
    	<div class="conditions">
    		<input type="checkbox" name="cgv"  required id="cgv" data-url="<?=$this->uri('order.acceptCgv',['queries'=>['id' => ':id']],'PATCH')?>" class="onchange" data-ctrl="checkout.cgv"><label data-i18n="accept-cgv" for="cgv">Je reconnais avoir lu et accepté les <a target="_blank" href="/<?=$this->getPrefixUrl()?>cgv~c17.html" class="link">Conditions générales de vente</a>.</label>
    		<!-- <input type="checkbox" name="event"  required id="event" data-url="<?=$this->uri('order.event',['queries'=>['id' => ':id']],'PATCH')?>" class="onchange" data-ctrl="checkout.lottery"><label data-i18n="accept-cgv" for="event">Je reconnais avoir lu et accepté les <a target="_blank" href="/<?=$this->getPrefixUrl()?>cgv~c17.html" class="link">Conditions générales de vente</a>.</label> -->
    	</div>      	           
    </div>
</div> 
<input type="hidden" id="thanks" value="<?=$this->uri('orders.thanks')?>"/>
<template id="discount-tpl">
	<p class="titles"><span data-i18n="discount">Réduction:</span> <small class="discount"></small> <small class="amount"></small></p>
</template>