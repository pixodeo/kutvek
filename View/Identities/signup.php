<?php $form = $this->form; $lang = $this->lang;?>
<div class="accordion_tabs">
	<input type="checkbox"  id="register" checked />
	<label for="register"></label>
	<div>
		<div class="col-m-5 col-m-center signup-card">
			
			<div class="card">
				<p class="designation"><span data-i18n="create-account">Créer un compte</span></p>
				<hr>				
			<form  action="<?= $this->uri('identities.register', [], 'POST')?>" data-ctrl="identity.register">	
				<div class="row">
					<div class="col-s-12 col-m-6 col-m-center">
						<?= $this->form->input('firstname', [					
						'label' => 'Prénom',
						'placeholder' => 'Jean',
						'attributes' => ['class="field-input"','required'],
						'wrapper'=> ['class' => 'column']
						]);
					?>
					<?= $this->form->input('lastname', [
						'label' => 'Nom',
						'placeholder' => 'DUPONT',
						'attributes' => ['class="field-input"','required'],
						'wrapper'=> ['class' => 'column']
						]);
					?>
					<?= $this->form->input('login', [
						'id' => 'email-registration',
						'label' => 'Email',
						'l10n' => 'email',
						'attributes' => ['type' => 'type="email"', 'class="field-input"', 'required'],
						'wrapper' => ['class' => 'column']
						]);
					?>
					<?= $this->form->input('password', [
						'id' => 'registration-pwd',
						'label' => 'Mot de passe',
						'l10n' => 'password',
						'attributes' => ['type' => 'type="password"', 'class="field-input"', 'required'],
						'wrapper' => ['class' => 'column']
						]);
					?>
					<div class="field-wrapper column">
						<label for="phone" class="required">Téléphone portable</label>
						<input name="phone" id="phone" type="text" class="field-input" data-i18n="phone" required/>
					</div>
					</div>	
				
					
				</div>
				<p><span data-i18n="opt-in">Cochez cette case pour recevoir occasionnellement des informations sur nos produits, nos services, et des offres promotionnelles</span></p>
				<div class="flex align-center">					
					<span class="switch-container"><input type="checkbox" name="newsletter" value="1" id="newsletter" class="sr-only" ><label class="subscribe" for="newsletter" data-i18n="subscribe"> <span class="label"></span> Je m'abonne à la newsletter</label></span>
				</div>    
				<div class="alert-message" id="message-registration"></div> 
				<div><input type="checkbox" name="product_info" value="1"></div>       	
				<div class="btns">					
					<button type="submit" class="btn contained validate"><span data-i18n="next">Suivant</span></button>
				</div>

			</form>
			</div>
			
		</div>
	</div>	
</div>
<div class="accordion_tabs">
	<input type="checkbox"  id="type-account" />
	<label for="type-account"></label>	
	<div>
		<div class="col-m-5 col-m-center signup-card">
			<p class="h4"><span data-i18n="choose-account">Merci de choisir votre type de compte</span></p>
			<hr>
			<div class="card">						
				<p class="designation" data-i18n="pro-account">Devenez Concessionnaire</p>	
				<hr>
				<p class="if-pro" data-i18n="are-you-dealer">Vous êtes professionnel de la moto ?</p>		
				<p class="pro-info" data-i18n="become-a-dealer">Devenez revendeur et profitez de prix attractifs et d'autres avantages. </p>
				<p class="dealer-conditions" data-i18n="dealer-conditions">Nous entendons par professionnel de la moto tous les professionnels exerçant dans le domaine de la moto, du quad, du ssv, du jetski, du scooter et de la motoneige. <br>Ne sont pas pris en compte les professionnels tels que carrossier, garagiste auto…
				</p>	
				<div class="btns">					
					<label for="pro" class="btn contained primary" data-i18n="choose">choisir</label>
					<input class="onchange" type="radio" name="type[]" id="pro" value="pro" data-ctrl="identity.typeAccount" hidden />
				</div>
						
			</div>
			<div class="card">
				<p class="designation" data-i18n="std-account">Compte Standard</p>
				<hr>
				<div class="btns">									
					<label for="std" class="btn contained primary" data-i18n="choose">choisir</label>
					<input class="onchange" type="radio" name="type[]" id="std" value="std" data-ctrl="identity.typeAccount" hidden />
				</div>
			</div>			
			<!-- <div class="btns">	
				<a href="#register" class="btn dark click" data-current="type-account" data-i18n="prev" data-ctrl="identity.back">Retour</a>			
			</div>	 -->			
		</div>
	</div>		
</div>
<div class="accordion_tabs">	
	<input type="checkbox"  id="pro-account" />
	<label for="pro-account"></label>
	<div>
		<div class="col-m-5 col-m-center signup-card">
			<div class="card">
				<p class="designation" data-i18n="pro-account">Devenez Concessionnaire</p>	
				<hr>				
				<form   action="<?=$this->uri('identities.businessDocuments',[], 'POST')?>" data-ctrl="identity.businessDocuments">
					<div class="row">
						<div class="col-s-12 col-m-8">
						<?= $this->form->input('company', [					
						'label' => 'Nom de votre société',
						'attributes' => ['class="field-input"', 'required'],
						'wrapper'=> ['class' => 'column col-s-12']
						]);
						?>	
						<?= $this->form->input('registration_number', [
							"id" => 'siret',	
							'label' => 'N° SIRET', 
							'wrapper' => ['class' => 'column col-m-8'], 
							'attributes' => ['class="field-input"']
							]);
						?>
						<?= $this->form->input('vat_number',[
							"id" => 'vat-number',
							'label' => 'N° TVA Intra Communautaire',
							'wrapper' => ['class' => 'column col-m-8'],
							'attributes' => ['class="field-input"', 'required']
							]);
						?>	
						</div>
						<?= $this->form->dragAndDrop('kbis',[								
							'label' => 'Extrait KBIS <small> (format PDF)</small>',
							'wrapper' => ['class' => 'column col-s-12'],
							'attributes' => ['class="onchange field-input"', 'data-ctrl="identity.kbis"']
							]);
						?>
					</div>																
					<div class="btns">	
						<a href="#type-account" class="btn dark click" data-current="pro-account" data-i18n="prev" data-ctrl="identity.back">Retour</a>				
						<button type="submit" class="btn contained validate"><span data-i18n="next">Suivant</span></button>
					</div>
				</form>	
			</div>					
		</div>
	</div>
</div>
<div class="accordion_tabs">	
	<input type="checkbox"  id="step-address" />
	<label for="step-address"></label>
	<div>
		<div class="col-m-5 col-m-center signup-card">
			<div class="card">
				<p class="designation" data-i18n="billing-address">Adresse de facturation</p>	
				<hr>	
				<p class="pro-info" data-i18n="why-address">Pour établir une facture et expédier vos achats</p>
				<p class="pro-info dealer-conditions" data-i18n="address-infos">Une fois le compte validé, vous pourrez enregister une autre adresse, pour la livraison par exemple si celle-ci diffère de l'adresse de facturation.</p>				
				<form class="row" action="<?=$this->uri('identities.address',[], 'POST')?>" data-ctrl="identity.address">							
				<div class="col-s-12 col-m-8">
					<div class="field-wrapper column col-s-12">
		            <label for="address_line_1" class="required">Rue, numéro...</label>
		            <input name="address[address_line_1]" id="address_line_1" type="text" class="field-input" data-i18n="line1" required>
			        </div>
			        <div class="field-wrapper column col-s-12">
			            <label for="address_line_2">Complément d'adresse</label>
			            <input name="address[address_line_2]" id="address_line_2" type="text" class="field-input" data-i18n="line2" />
			        </div>   
			        <div class="row">
						<div class="field-wrapper column col-s-12 col-m-4">
		                    <label for="postal_code" class="required">Code Postal</label>
		                    <input name="address[postal_code]" id="postal_code" type="text" class="field-input" data-i18n="zipcode" required>
		                </div>
			        	<div class="field-wrapper column col-s-12 col-m-8">
			                <label for="admin_area_2" class="required">Ville</label>
			                <input name="address[admin_area_2]" id="admin_area_2" type="text" class="field-input" data-i18n="city" required>
		                </div>
		            </div>	  
			         <div class="field-wrapper column col-m-8">
			          <label for="country_code" class="required">Pays</label>
			           <?= $select_countries;?>
			        </div>
			        <div class="field-wrapper column col-m-8">
			            <label for="admin_area_1">Etat / Région</label>
			            <input name="address[admin_area_1]" id="admin_area_1" type="text" class="field-input" data-i18n="line4">
			        </div>		        	            
				</div>							
				<div class="btns">		
					<a href="#type-account" class="btn dark click" data-current="step-address" data-i18n="prev" data-ctrl="identity.back">Retour</a>				
					<button type="submit" class="btn contained validate"><span data-i18n="finish-end">Terminer</span></button>
				</div>
			</form>	
			</div>					
		</div>
	</div>
</div>
<div class="accordion_tabs">	
	<input type="checkbox"  id="std-welcome" />
	<label for="std-welcome"></label>
	<div>
		<div class="col-m-5 col-m-center signup-card">
			<div class="card">
				<p class="designation" data-i18n="signup-finish">Bienvenue à bord !</p>	
				<hr>								
				<p class="welcome" data-i18n="signin-now">Connectez-vous dès à présent avec les identifiants que vous avez fourni.</p>
				<p><a href="<?=$this->uri('identities.login');?>" class="link"><span data-i18n="signin-page">Page de connexion</span></a></p>					
			</div>					
		</div>
	</div>
</div>
<div class="accordion_tabs">	
	<input type="checkbox"  id="pro-welcome" />
	<label for="pro-welcome"></label>
	<div>
		<div class="col-m-5 col-m-center signup-card">
			<div class="card">
				<p class="designation" data-i18n="signup-finish">Bienvenue à bord !</p>	
				<hr>								
				<div class="welcome" data-i18n="welcome-pro">
					<p>Nous allons vérifier les justificatifs que vous avez fournis à l'étape précédente</p>
					<p>Vous recevrez un e-mail de confirmation et les conditions et les avantages qui s'appliquent au compte pro après validation par nos équipes</p>
				</div>
				<p><a href="<?=$this->uri('identities.login');?>" class="link"><span data-i18n="signin-page">Page de connexion</span></a></p>							
			</div>					
		</div>
	</div>
</div>