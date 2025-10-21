<div class="row">
    <div  class="col-s-12 col-m-6 col-m-offset-1 col-m-push-1">
		<div class="tranquility">
			<div class="accordion_tabs">
				<h5 class="titles"><span class="step"><span class="number">1</span><span  class="icon material-symbols-rounded">check</span></span><span data-i18n="email-address">Adresse e-mail</span></h5>
				<input type="checkbox" id="email-input"/>
				<label for="email-input" class="pointer"><span class="material-symbols-rounded"></span></label>
				<div>
					<?= $this->form->email('email', ['placeholder' => 'j', 'attributes' => ['class="email hide"', 'hidden']]);?>
				</div>
			</div>		
			<div class="accordion_tabs">
				<h5 class="titles"><span class="step"><span class="number">2</span><span  class="icon material-symbols-rounded">check</span></span><span data-i18n="shipping-methods">Mode de livraison</span></h5>
				<input type="checkbox" id="shipping-method-input" disabled />
				<label for="shipping-method-input" class="pointer"><span class="material-symbols-rounded"></span></label>
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
								<div id="shipping-address" class="col-s-12 col-m-7"></div>
								<div class="col-s-12 col-m-5">
									<div class="delivery-method chrono-classic hide">
										<p>Méthode de livraison <small>CHRONO Classic</small></p>
										<p><span>Coût</span> <small class="cost"></small></p>
										<p><span>Délai de livraison</span> 3 jours ouvrables</p>
										<p>	            						
		            						<button  type="submit" class="btn contained xs validate" name="cost" value="" data-i18n="choose"  id="std-classic" form="user-address" >Choisir</button>
		        						</p>
									</div>
									<div class="delivery-method chrono-express hide">
										<p>Méthode de livraison <small>CHRONO Express</small></p>
										<p><span>Coût</span> <small class="cost"></small></p>
										<p><span>Délai de livraison</span> 1 jours ouvrable</p>	
										<p>											
		            						<button  type="submit" class="btn contained xs validate" name="cost" value="" data-i18n="choose"  id="std-express" form="user-address" >Choisir</button>
		        						</p>	
									</div>
								</div>
							</div>														
						</div>
						<div class="tab_content" id="click-and-collect">
							<div class="row">
								<div class="col-s-12 col-m-3 col-m-push-4">
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
							<p class="address_line_1"></p>
							<p class="address_line_2"></p>
							<p><span class="postal_code"></span><span class="admin_area_2"></span></p>
							<form action="<?= $this->uri('orders.pickupAddress', [], 'POST') ?>" id="form-pickup" data-ctrl="cart.pickupAddress">
								<input type="hidden" name="address[delivery_address]" />
								<input type="hidden" name="address[com_shipping]" value="0">
								<input type="hidden" name="address[delivery_type]" value="2" />
								<p><button type="submit" class="btn contained xs validate" data-i18n="choose" id="pickup">Choisir</button></p>
							</form>
						</div>		
					</div>
				<form id="form-relay" action="<?=$this->uri('orders.chronoRelay', [], 'POST')?>" data-ctrl="cart.address"></form>
				</div>
			</div>
			<div class="accordion_tabs">
				<h5 class="titles"><span class="step"><span class="number">3</span><span  class="icon material-symbols-rounded">check</span></span><span data-i18n="pay">Paiement</span></h5>
				<input type="checkbox" id="pay-input" disabled />
				<label for="pay-input" class="pointer"><span class="material-symbols-rounded"></span></label>
				<div>				
					<div class="row">
						<div class="col-s-12 col-l-6 checkout-methods">
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
						    		<form id="card-form" class="card-form">
						    			<div class="col-s-12">
						    				<label for="card-number" data-i18n="card-number">Numéro de carte</label>
						    				<div id="card-number" class="col-s-12 card_field"></div> 
						    			</div>	
						    			<div class="col-s-12">
									    	<label for="card-holder-name" data-i18n="name-on-card" >Titulaire de la carte</label>
									    	<input type="text" id="card-holder-name" name="card-holder-name"  data-i18n="name-on-card" placeholder="Jean DUPONT" class="card_field col-s-12"/>										
									    </div>				    			
						    			<div class="col-s-12 col-l-6">
									      <label for="expiration-date" data-i18n="expiration-date">Expire à fin</label>
									      <div id="expiration-date" class="card_field"></div>
									  	</div>
						    			<div class="col-s-12 col-l-6">
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
    <div id="checkout" class="col-s-12 col-m-3">
    	<h5 data-i18n="my-cart" class="titles">Mon panier</h5>
		<div id="items"></div>
    	<h5 class="titles sub-total"><span data-i18n="sub-total">Sous-total</span><span id="item-total"></span></h5>
    	<p class="titles sub-total hidden"><small data-i18n="discount">Réduction</small><small id="discount"></small></p>
    	<p class="titles sub-total"><small data-i18n="delivery">Livraison</small><small id="shipping-amount"></small></p>  	
    	<hr>
    	<h5 class="titles sub-total"><span data-i18n="total-amount">Total à régler</span><span id="total-to-pay"></span></h5>
       	           
    </div>
</div> 
    <div class="our-terms">Pour en savoir plus sur nos conditions d’achat, <a href="#" class="link">cliquez ici</a>.</div>
    <input type="hidden" id="thanks" value="<?=$this->uri('orders.thanks')?>"/>