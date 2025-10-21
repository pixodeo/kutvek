<div class="row">
<div class="col-s-12 col-l-6  col-m-center">		
	<ul class="tabs">
		<li class="active">
			<a href="#tab-signin">
				<span class="icon material-symbols-rounded">vpn_key</span>
				<span data-i18n="signin">Connexion</span>
			</a>
		</li>		
		<li class="hidden">
			<a href="#tab-signup">
				<span class="icon material-symbols-rounded">assignment_ind</span>
				<span data-i18n="quick-registration">Inscription rapide </span>
			</a>
		</li>
	</ul>										
	<div class="tabs_content">
		<div class="tab_content row active" id="tab-signin">
			<div class="col-s-12 col-l-7 col-l-center">
				<div id="welcome" data-i18n="signup-success" hidden>
				<h5>Bienvenue à bord !</h5>
				<p>Connectez-vous dès à présent avec les identifiants que vous avez fourni.</p>
				</div>
				<form data-ctrl="identity.signin" id="form-signin" method="POST">
					<div class="alert-message" id="message"></div>
					<div>										
						<?= $this->form->input('login', [
						'id' => 'email',
						'label' => 'Email',
						'attributes' => ['type' => 'type="email"', 'class="field-input"', 'required'],
						'wrapper'=> ['class' => 'column']
						]);
						?>
						<?= $this->form->input('password', [	
							'id'=> 'signin-pwd',				
							'label' => 'Mot de passe',
							'l10n' => 'password',
							'attributes' => ['type' => 'type="password"', 'class="field-input"', 'required'],
							'wrapper'=> ['class' => 'column']
						]);
						?>						
						<input type="hidden" name="website" value="5" />
						<input type="hidden" name="next"  value="<?= $next; ?>" />
						<div class="btns">
							<a href="<?=$this->uri('products.forgotPassword', ['queries' => ['slug'=>'oubli-mot-de-passe']]);?>" data-i18n="forgot-password" class="btn primary">Mot de passe oublié ?</a>				
							<button type="submit" class="btn contained dark"><span data-i18n="signin">Connexion</span></button>	
						</div>	
					</div>					
				</form>	
			</div>			
		</div>	
		<div class="tab_content row" id="tab-signup">
			<div class="col-s-12 col-l-7 col-l-center">	
				<form action="<?= $this->uri('identities.register', [], 'POST'); ?>" data-ctrl="identity.registration">
					<div class="alert-message" id="message-registration"></div>
					<div>					
						<?= $this->form->input('firstname', [					
						'label' => 'Prénom',
						'placeholder' => 'Jean',
						'attributes' => ['class="field-input"', 'required'],
						'wrapper'=> ['class' => 'column']
						]);
						?>
						<?= $this->form->input('lastname', [
							'label' => 'Nom',
							'placeholder' => 'DUPONT',
							'attributes' => ['class="field-input"', 'required'],
							'wrapper'=> ['class' => 'column']
						]);
						?>
						<?= $this->form->input('login', [
						'id' => 'email-registration',
						'label' => 'Email',
						'l10n' => 'email',
						'attributes' => ['type' => 'type="email"', 'class="field-input"', 'required'],
						'wrapper'=> ['class' => 'column']
						]);
						?>
						<?= $this->form->input('password', [
							'id' => 'registration-pwd',
							'label' => 'Mot de passe',
							'l10n' => 'password',
							'attributes' => ['type' => 'type="password"', 'class="field-input"', 'required'],
							'wrapper'=> ['class' => 'column']
						]);
						?>	
						<div class="field-wrapper column">
						<label for="phone" class="required">Téléphone portable</label>
						<input name="phone" id="phone" type="text" class="field-input" data-i18n="phone" required/>
						</div>
						<div class="btns">					
						<button type="submit" class="btn contained validate"><span data-i18n="signup">Inscription</span></button>
						</div>	
					</div>				
				</form>
			</div>	
		</div>	
	</div>
</div>
</div>	