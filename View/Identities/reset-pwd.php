<section class="main-row">
	<div class="row">
		<div class="col-s-12 col-m-8 col-m-center">
			<h1 class="section-title"><?= $page->title; ?></h1>
			<h2 class="short-description"><?= $page->short_description; ?></h2>
			<div><?=$page->content;?></div>
			<div class="msg-info"></div>
			<p><a href="<?=$this->uri('identities.login');?>" id="signin-link" class="link hidden">Connexion</a></p>
			<div class="row">
				<div class="col-s-12 col-m-9">
					<form id="reset" action="<?=$this->uri('identities.resetPassword', [], 'POST');?>" data-ctrl="identity.resetPassword">
						<input type="text" autocomplete="username" name="username" value="" hidden />		
						<?= $this->form->password('password', [
						'id' => 'password',
						'label' => 'Saisissez votre nouveau mot de passe',
						'attributes' => ['class="field-input"', 'required',  'aria-autocomplete="list"'],
						'wrapper'=> ['class' => 'column']
						]);
						?>		
						<input type="hidden" name="token" value="<?= $this->getRequest()->getQueryParams()['token'];?>" />			
						<div class="btns">
							<button  class="contained primary" type="submit">Envoyer</button>
						</div>
					</form>	
				</div>
			</div>
						
		</div>
	</div>
</section>