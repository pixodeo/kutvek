<div class="main-row dashboard">
<p class="txt-r">
	<a href="<?=$this->uri('pages.index');?>" class="click btn contained dark"  data-ctrl="user.logout">
			<span class="icon material-symbols-rounded warning">power_rounded</span>
			<span data-i18n="signout">Déconnexion</span>
	</a>
</p>
<div class="row">
	<?php if($signin == 'gth'): ?>
		<div class="col-s-12 col-m-8 col-m-center">
			<p>Bonjour, merci d'avoir choisi KUTVEK pour vous équiper.</p>
			<p>Depuis cet espace client vous avez accès à vos commandes, vos infos personnelles. <br>
				Vous pouvez gérer vos adresses de livraison, de facturation
			</p>
		</div>
	<?php endif; ?>
	<div class="col-s-12 col-m-8 col-m-center">
		<div><p class="h4"><span data-i18n="hi">Bonjour</span>&nbsp;<span id="username"></span></p></div>
		<div class="dashboard-description" data-i18n="first-dashboard-info">
			<p>Depuis l'espace client KUTVEK, tu as la possibilité de voir toutes tes commandes en cours de traitement ou déjà finalisées.<br><strong>Si ta commande comporte de la personnalisation :</strong><br>Tu seras tenu informé par email quand la maquette aura été réalisé. Toutes les informations dont tu auras besoin pour la connexion à l'espace client seront indiquées dans cet email. Tu pourras ensuite valider ta commande si la maquette te convient ou nous faire une demande de mise à jour pour obtenir une nouvelle maquette.</p>
		</div>
		<div class="row c-between customer-actions">			
				<div class="dashboard_card col-s-12 col-m-3">
					<span class="icon material-symbols-rounded">local_mall</span>
					<p class="designation" data-i18n="orders-history">Historique de mes commandes</p>
					<p class="description ellip" data-i18n="orders-history-desc">Suivi colis, téléchargement de facture</p>
					<a href="<?=$this->uri('customers.orders');?>"></a>
				</div>			
				<div class="dashboard_card col-s-12 col-m-3">
					<span class="icon material-symbols-rounded">manage_accounts</span>
					<p class="designation" data-i18n="user-parameters">Mes infos</p>
					<p class="description ellip" data-i18n="user-parameters-desc">Paramètres de connexion, adresses</p>
					<a href="<?=$this->uri('account.info');?>"></a>
				</div>			
				<div class="dashboard_card col-s-12 col-m-3">
					<span class="icon material-symbols-rounded">sms_failed</span>
					<p class="designation" data-i18n="after-sales">Service après-vente</p>
					<p class="description" data-i18n="after-sales-desc">Une erreur sur une commande ? Un retour ?</p>
					<a href="#"></a>
				</div>			
		</div>
	</div>
	<div class="col-s-12 col-l-10 col-xl-9 col-center orders">
		<ul class="tabs no-print">
				<li class="active"><a href="#mockups-confirm"><span data-i18n="orders-to-validate">Persos à valider </span><span class="counter">(<small>0</small>)</span></a></li>		
				<li><a href="#in-progress"><span data-i18n="pending-orders">Commandes en cours </span> <span class="counter">(<small>0</small>)</span></a></li>						
		</ul>
		<div class="tabs_content">
			<div class="tab_content active" id="mockups-confirm" data-uri="<?=$this->uri('customers.mockupsToValidate');?>">
				<ul class="tasks">
				</ul>
			</div>
			<div class="tab_content" id="in-progress" data-uri="<?=$this->uri('customers.currentOrders');?>">
				<ul class="tasks">
				</ul>
			</div>
		</div>
	</div>
</div>
</div>
<template id="task">
	<li class="task">
		<div>
            <span class="reference"></span>
            <p class="action">
                <a href="#" class="link bill hidden" target="_blank"><span class="icon material-symbols-rounded">&#xef6e;</span><span>Téléchargez la facture</span></a>
                <label for="" class="action dropdown pointer"><span class="material-symbols-rounded"></span></label>
            </p>            
	    </div>
		<p><span class="designation"></span></p><input type="radio" name="dropdown" class="action dropdown"/>
		<div class="dropdown-content">		
			<ul class="tabs vertical">				
				<li class="active"><a href="#posts"  data-ctrl="mockup.display"><span data-i18n="mockups-and-posts">Maquettes & Messages</span></a></li>	
				<li ><a href="#info">Infos</a></li>					
			</ul>
			<div class="tabs_content vertical">
				
				<div class="tab_content active" id="posts">
					<div class="row">
						<div class="col-s-12 col-m-4">
							<div class="tasks-header">
								<h3 class="h5"><span class="icon material-symbols-rounded">&#xe0bf;</span><span data-i18n="our-chat">Notre discussion</span></h3>
								<hr>
							</div>							
							<div class="posts"></div>
							
						</div>
						<div class="col-s-12 col-m-8">
							<div class="row">
								<div class="col-s-12">
									<div class="tasks-header">
										<h3 class="h5"><span class="icon material-symbols-rounded">&#xe413;</span><span data-i18n="latest-mockups">Maquettes</span></h3>
										<hr>
									</div>									
									<div class="mockups choice"><div></div></div>
									<div class="mockups-labels"><div class="links"></div></div>	
								</div>
								<div class="col-s-12">
									<!-- formulaire acceptation maquette -->
									<form action="<?=$this->uri('customers.acceptMockup', ['queries' => ['item' => ':item']], 'POST') ?>" method="POST"  data-ctrl="mockup.accept">
			               				<?= $this->form->select('mockup', [
			               					'label' => 'Maquette sélectionnée',
			               					'placeholder' => 'Choisir',
			               					'id' => 'mockup-choosen',               					
			               					'attributes' => ['class="field-input select onchange"', 'data-ctrl="mockup.select"','required']
			               				]);?>			               				
				                        
				                        <div class="suggest hide">
				                        	<p data-i18n="accept-seatcover-info" class="accept-seatcover-info">
	                        				Si tu le souhaites, nous pouvons réaliser une housse de selle, elle est visible sur la maquette, le prix également. <br>
	                        				En cochant la case ci-dessous, cela validera cette housse selle et nous te contacterons pour effectuer le règlement.
	                    					</p>
					                        <?= $this->form->input('seat', 
				               					[
				               						'label' => "J'accepte la proposition d'ajouter une housse de selle à ma commande", 
				               						'id' => 'accept-seatcover',
				               						'attributes' => ['type' =>'type="checkbox"', 'class="field-input checkbox"'],
				               						'value' => 1  
				               				]);?>
				                        </div>			                          	
				                        <?= $this->form->input('cgv', 
			               					[
			               						'label' => "J'accepte les conditions générales de vente", 
			               						'attributes' => ['type' =>'type="checkbox"', 'class="field-input checkbox"', 'required'],
			               						'value' => 1 
			               				]);?>                  
					                    <p class="btns-end">
					                    	<button class="btn outlined white modify click" data-modal="modify" data-ctrl="app.modal" type="button"><span data-i18n="modify">Modifications</span></button>
					                        <button type="submit" class="btn contained validate"><span class="icon material-symbols-rounded load">&#xe9d0;</span><span data-i18n="send" >Valider</span></button>   
					                    </p>	                                             
			            			</form>
			            		</div>
							</div>
						</div>						
					</div>			
				</div>	
				<div class="tab_content" id="info"></div>				
			</div>
		</div>
		<aside class="modal click modify" id="modify" data-modal="modify" data-ctrl="app.modal">
		    <div class="popup mx-w45">
		        <header class="close">
		            <p class="title" data-i18n="ask-for-change">Nouvelle maquette</p>
		            <a href="#modify" class="click" data-modal="modify" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
		        </header>
		        <div>		           
					<form action="<?=$this->uri('customers.rejectMockup', ['queries' => ['item' => ':item']], 'POST') ?>" class="form-posts" data-ctrl="order.decline">			
						<div class="field-wrapper column">
							<label for="body" data-i18n="mockup-modification">Précise les modifications à effectuer, smileys / emoticons non supportés. Tu peux également joindre jusqu'à 5 fichiers</label>	
							<textarea class="field-input textarea" name="body" required ></textarea>
						</div>
						
						<div class="preview posts-preview"></div>
						<input class="files-input onchange" type="file"  id="files" data-ctrl="uploader.addFiles" multiple hidden  />	
						
						<p class="btns-end">
							<label class="files-label btn contained dark" for="files"><span class="icon material-symbols-rounded">&#xe9fc;</span><span data-i18n="join-files">Joindre des fichiers</span></label>		                    	
				            <button type="submit"  class="btn contained validate"><span class="icon material-symbols-rounded load">&#xe9d0;</span><span data-i18n="send">Envoyer</span></button>   
				        </p>					
					</form>					
		        </div>           
		    </div>
		</aside>
	</li>    
</template>

<template id="post-tpl">
<div class="post" id="post">
    <p><span class="user"></span><span class="created">le 2023-01-12 13:55:37</span></p>
    <div class="body">
    </div>
</div>   	
</template>

<template id="mockup-tpl">
    <img src="/img/blank.png" class="mockup" data-file="" alt="" />
</template>

<template id="file-tpl">
	<div class="post file" id="file">
    <p><span class="user"></span><span class="created"></span></p>
    <div class="body">
    	<p class="title"></p>
    	<p class="size"></p>
    	<p><span class="icon material-symbols-rounded"></span></p>
    </div>
	</div>
</template>

<template id="img-tpl">
	<div class="post img" id="file">
    <p><span class="user"></span><span class="created"></span></p>
    <div class="body"></div>
	</div>
</template>