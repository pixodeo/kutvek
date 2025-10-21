<div class="row">
	<div data-i18n="order-success" class="col-s-12 col-m-8 col-m-center">
		<h4>Merci pour votre commande</h4>
		<p>Votre commande a bien été prise en compte.<br>
		Elle sera visible dans votre espace client dès que nous l’aurons enregistrée.</p>
		<p>Si la commande comporte au moins une option de personnalisation : <br>
		Nom et numéro de plaque, sponsors, changement de couleurs...) <br>
		un graphiste réalisera une maquette et vous recevrez un e-mail quand cette maquette est disponible.</p>
		<p>Depuis votre compte client vous pourrez accepter les travail effectué et valider la maquette. </p>
		<p> Si le rendu ne vous convient pas, vous pourrez demander une modification. </p>
		<p>Dès qu’une maquette est validée, elle part en  impression pour être expédiée dans la journée</p>
		<div>
			<h5>Vous n'avez pas encore créé de compte client ?</h5>
			<p>Inscrivez-vous dès maintenant, <a class="primary link"href="<?= $this->uri('identities.signup')?>">ça se passe ici</a>
			</p>
		</div>		
	</div>
	<div  class="col-s-12 col-m-8 col-m-center">
		<p class="h5"><span data-i18n="fyi">Pour info</span> : </p>
		<p><span data-i18n="transaction-id">Votre numéro de commande, à communiquer pour toute correspondance avec notre service client</span> : <b id="transaction-id"></b></p>
		<p><span data-i18n="transaction-email">L'adresse e-mail où nous enverrons les informations</span> : <b id="transaction-email"></b></p>
	</div>
</div>