  <form   method="POST" id="form" name="form">
	<h1>Sign in</h1>
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
	  <div id="password-constraints">Eight or more characters, with at least one&nbsp;lowercase and one uppercase letter.</div>
	</section>
	<button id="signin">Sign in</button>
</form>