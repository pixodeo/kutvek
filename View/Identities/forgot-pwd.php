<section class="main-row">
	<div class="row">
		<div class="col-s-12 col-m-8 col-m-center">
			<h1 class="section-title"><?= $page->title; ?></h1>
			<h2 class="short-description"><?= $page->short_description; ?></h2>
			<div class="msg-info">

			</div>
			<div class="row">
				<div class="col-s-12 col-m-4">
				<form id="reset" action="<?=$this->uri('identities.forgotPassword', [], 'POST');?>" data-ctrl="identity.checkEmail">
				<?= $this->form->input('login', [
				'id' => 'email',
				'label' => 'Email',
				'attributes' => ['type' => 'type="email"', 'class="field-input"', 'required'],
				'wrapper'=> ['class' => 'column']
				]);
				?>
				<input type="hidden" name="workspace" value="<?=$this->getWorkspace();?>" />
				<input type="hidden" name="l10n" value="<?=$this->getL10nId();?>" />
				<div class="btns">
					<button id="send" class="contained primary" type="submit">Envoyer</button>
				</div>
			</form>	
				</div>
			</div>
						
		</div>
	</div>
</section>