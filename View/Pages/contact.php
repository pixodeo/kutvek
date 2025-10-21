<div class="main-row page-product">
	<h1 class="page-title"><?= $page->title; ?></h1>
	<section class="row page-default <?= $page->slug; ?>">
		<div class="col-s-12 col-m-4 col-m-push-1">
			<div id="map" class="map"></div>
		</div>
		<div class="col-s-12 col-m-6">
			<?= $page->content; ?>			
			<form action="<?=$this->uri('customers.contactUs', [], 'POST');?>" class="form-contact" data-ctrl="contact.send">
				<div class="row">
					<div class="col-s-12 col-m-6">
						<?= $this->form->input('firstname', ['label' => 'Prénom', 'attributes' => ['class="field-input"', 'required'], 'wrapper' => ['class' => 'column']]); ?>			
					</div>
					<div class="col-s-12 col-m-6">					
						<?= $this->form->input('lastname', ['label' => 'Nom', 'attributes' => ['class="field-input"', 'required'], 'wrapper' => ['class' => 'column']]); ?>				
					</div>
					<div class="col-s-12 col-m-6 col-m-pull-6">
						<?= $this->form->input('company', ['label' => 'Société', 'attributes' => ['class="field-input"'], 'wrapper' => ['class' => 'column']]); ?>	<br>				
					</div>
					<div class="col-s-12 col-m-6">						
						<?= $this->form->input('email', ['label' => 'E-mail','placeholder' => 'jeandupont@kutvek.com', 'attributes' => ['type' =>'type="email"','class="field-input"', 'required'], 'wrapper' => ['class' => 'column']]); ?>								
					</div>
					<div class="col-s-12 col-m-6">					
						<?= $this->form->input('phone', ['label' => 'Téléphone', 'attributes' => ['class="field-input"'], 'wrapper' => ['class' => 'column']]); ?>		
					</div>
				</div>			
				<?= $this->form->textarea('body', ['label' => 'Objet', 'attributes' => ['rows="12"', 'required']])?>
				<div class="btns"><button type="submit" class="btn contained dark" data-i18n="send">Envoyer</button></div>
			</form>			
		</div>
	</section>
</div>
<aside class="modal click" id="contact-success" data-modal="contact-success" data-ctrl="app.modal">
    <div class="popup mx-w45">
        <header class="close">
            <p class="title" data-i18n="item-info">Détails de l'article</p>
            <a href="#contact-success" class="click" data-modal="contact-success" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
        </header>
        <div class="content">
            
        </div>
        <div class="btns"><a href="<?= $this->uri('pages.index'); ?>" data-i18n="homepage" class="btn contained dark">Retourner à l'accueil</a></div>
    </div>
</aside>