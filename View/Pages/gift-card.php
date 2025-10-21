<section class="row">
	<div class="col-s-12 col-l-10 col-center">	
		<div class="row">
			<div class="col-s-12">
				<h1 class="section-title"><?= $page->title; ?></h1>
			</div>
			<div class="col-s-12  col-l-5">
				<img src="<?=$page->cover;?>" alt="" />
				<?= $page->content; ?>
			</div>
			<div class="col-s-12 col-l-7">
				
				<h2 class="short-desc"><?= $page->short_description; ?></h2>
				<div>
					<form action="<?=$this->uri('giftCard.addToCart', [], 'POST')?>" data-ctrl="giftCard.pushToCart">
						<?=  $amounts; ?>
						<?= $this->form->input(
							'item[card][recipient_name]', 
							[
								'label' => 'Pour',
								'id' => 'recipient-name',
								'l10n' => 'gift-to',
								'placeholder' => 'Prénom ou surnom du destinataire',
								'wrapper' => array('class' => 'column col-s-12 col-m-5'),                    
					            'attributes' => array(
					                'class="field-input"',
					                'required'                  
					            )
							]
						);?>
						<?= $this->form->email(
							'item[card][recipient_email]', 
							[
								'label' => 'Adresse e-mail où envoyer la e-carte cardeau',
								'id' => 'recipient-email',
								'l10n' => 'gift-email',
								'wrapper' => array('class' => 'column col-s-12 col-m-5'),                    
					            'attributes' => array(
					                'class="field-input"',
					                'required'                  
					            )
							]
						);?>
						<?= $this->form->textarea(
							'item[card][msg]', 
							[
								'label' => 'Message <small>(facultatif)</small>',
								'id' => 'recipient-msg',
								'l10n' => 'gift-msg',
								'placeholder' => 'Message à transmettre au bénéficiaire',
								'wrapper' => array('class' => 'column col-s-12'),                    
					            'attributes' => array(
					                'class="field-input textarea"'
					            )
							]
						);?>
						<?= $this->form->input(
							'item[card][sender_name]', 
							[
								'label' => 'De la part de',
								'id' => 'sender-name',
								'l10n' => 'gift-from',
								'placeholder' => 'Prénom ou surnom de la personne qui offre',
								'wrapper' => array('class' => 'column col-s-12 col-m-5'),                    
					            'attributes' => array(
					                'class="field-input"',
					                'required'                  
					            )
							]
						);?>
						<input type="hidden" name="item[product_url]"  value="https://<?= $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"] ?>" />
						<input type="hidden" name="item[img]" value="https://www.kutvek-kitgraphik.com/images/gift-card/gift-card-fr-fr.jpg" />
						<input type="hidden" name="item[currency][id]" value="<?=$product->currency->id;?>" />
						<input type="hidden" name="item[currency][designation]" value="<?=$product->currency->designation;?>" />
						<input type="hidden" name="item[description]" value="<?=$product->description;?>" />
						<input type="hidden" name="item[product]" value="<?=$product->id;?>" />
						<button class="btn contained dark addToCart" type="submit">
							<span class="text" data-i18n="add-to-cart">Ajouter au panier</span>
							<span class="icon material-symbols-rounded load hidden">progress_activity</span>
						</button>
					</form>
				</div>
				<div class="bottom-desccription">
					
				</div>
			</div>
		</div>			
	</div>	
</section>