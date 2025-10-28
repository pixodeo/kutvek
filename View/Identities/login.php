  <form  action="<?=$this->url(name:'auth.signIn', method:'POST');?>" method="POST" id="form" name="form" data-ctrl="auth.login">
	<h1 data-i18n="login">Connexion</h1>
	<section>
		<label for="email">E-mail</label>
		<div class="input-div">			
			<input type="email" name="login" placeholder=" " id="email" required autocomplete="username"/>	 
		</div>		 	
	</section>
	<section>
		<label for="current-password">Mot de passe</label>
		<div class="input-div eyed-password">
			<input type="password" name="password" id="current-password" aria-describedby="password-constraints" required />
			<button id="toggle-password" type="button" aria-label="Show password as plain text. Warning: this will display your password on the screen."></button>	 
		</div>  
	</section>
	<p class="txt-right"><button type="submit" id="signin" class="contained dark" data-i18n="login">Connexion</button></p>
	<p class="forgot-pwd"><a href="#" class="link">Mot de passe oublié ?</a></p>
	<div data-i18n="create-account">Vous n'avez pas encore créé votre espace client ? <a href="#" class="link">inscription rapide</a></div>
</form>
<pre>
<?php print_r($cookies); ?>
</pre>