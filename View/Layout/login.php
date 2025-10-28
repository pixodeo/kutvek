<!DOCTYPE html>
<html lang="<?=$this->getLang();?>" data-layout="login">
<head>	
	<meta charset="utf-8">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?= $this->meta_title();?>
	<?= $this->meta_description();?>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css') ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/cart-overview.css') ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/event/tabs.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/gallery.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/modal.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/grid-layout.css'); ?>" type="text/css" media="screen">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<style>
		#form {			
   			margin: auto;
   			max-width: 36rem;
   		}
   		#form h1 {margin-bottom: 1.6rem;font-family: 'Montserrat';font-weight: 600;color:#424242;}
   		div[data-i18n="create-account"], .forgot-pwd{margin-top:3.2rem;}
   		#form input:-webkit-autofill,
		#form input:-webkit-autofill:hover,
		#form input:-webkit-autofill:focus,
		#form textarea:-webkit-autofill,
		#form textarea:-webkit-autofill:hover,
		#form textarea:-webkit-autofill:focus,
		#form select:-webkit-autofill,
		#form select:-webkit-autofill:hover,
		#form select:-webkit-autofill:focus {-webkit-box-shadow: 0 0 0px 1000px #ffffff inset !important;}		
		#form label {font-family: 'Montserrat'; font-weight: 500;}
		#form label::before{
			content: '*';
			padding: 0 .4rem;
		}
		#form button[type="submit"]{
			transition: opacity .4s;
		}
		#form button[type="submit"]:disabled {
			opacity: .4;
    		cursor: not-allowed;
		}

		.input-div {
			display: flex;
			max-width: 36rem;
    		justify-content: space-between;
    		align-items: center;
    		padding: 0 .8rem;
			border-width: .1rem;
    		border-style: solid;
    		border-radius: .2rem;
    		margin-bottom: 1.6rem;
    	}
		.input-div input {flex:1 ;height:4.8rem;border:none;outline:none;}
		.eyed-password button::after {
			content: "\e8f4";
		    font-family: 'Material Symbols Rounded';
		    font-weight: normal;
		    font-style: normal;
		    font-size: 2.4rem;
		    line-height: 1;
		    letter-spacing: normal;
		    text-transform: none;
		    display: inline-block;
		    white-space: nowrap;
		    word-wrap: normal;
		    direction: ltr;
    		-webkit-font-feature-settings: 'liga';
    		font-feature-settings: 'liga';
    		-webkit-font-smoothing: antialiased;
    		font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;

    	}
    	.eyed-password button.hide-pwd::after {content: "\e8f5";}

	</style>
</head>
<body>
<body>	
	<header class="primary-header">
	    <div class="message-bar">    	
	        <a href="https://www.youtube.com/user/kutvekkitgraphik" target="_blank"><img src="/img/pictos/youtube.png"></a>
	        <a href="https://www.facebook.com/Kutvek" target="_blank"><img src="/img/pictos/facebook.png"></a>
	        <a href="https://www.instagram.com/kutvek" target="_blank"><img src="/img/pictos/instagram.png"></a>
	    </div>
	    <div class="infos">
	        <div>
	            <div class="trust"></div>
	            <div class="action contact-us click" data-ctrl="app.contact">
	                <img src="/img/pictos/phone.png" class="picto phone" alt="">
	                <a href="tel:<?= $this->intlphone;?>"><span class="phone"><?=$this->phone; ?></span></a>
	            </div>
	        </div>
	        <div>
	            <span data-i18n="lang" class="choose-lang">Langue</span>
	            <div class="action trads-container">
	                <?= $this->l10ns(); ?>
	            </div>
	            <?= $this->widgetCountries(); ?>
	        </div>        
	    </div>
	    <div class="baseline">
	        <a href="<?= $this->uri('page.homepage', []) ?>" class="logo"><img class="logo-kutvek" src="<?= HALLOWEEN === 1 ? '/img/charter/logo_kutvek_orange.png' : '/img/charter/logo_kutvek.png';?>" alt="Logo KUTVEK"></a>
	        <div class="red">
	            <img class="logo-footer" src="/img/charter/logo-footer.png" />
	            <?=$this->topNav();?>
	            
	            <!-- $this->topMenu(); -->   
	            <div class="user-actions">
	                <div class="action search" id="search-doofinder">                 
	                <span class="icon material-symbols-rounded"></span>
	                </div>
	                <div class="action contact-us">
	                    <label class="icon material-symbols-rounded" for="see-phone"></label>
	                    <input type="checkbox" id="see-phone" hidden />
	                    <a class="phone" href="tel:<?= $this->intlphone;?>"><span ><?=$this->phone; ?></span></a>
	                </div>   <!--  $this->widgetSlugs($this->getContent()->slugs); -->
	                </div>          
	                <?php $obf = base64_encode($this->uri('cart.overview', ['queries'=>['id'=>':id']])); ?>	                
	                <a id="shopping-cart" class="click" data-obf="<?=$obf;?>" data-count="0" href="#" data-ctrl="cart.overview"><span class="icon material-symbols-rounded">&#xe8cc;</span>  </a>
	                <a href="#" class="menu click" data-ctrl="app.menu" data-target="main-nav"><span class="icon material-symbols-rounded">&#xe5d2;</span></a>
	            </div>
	        </div>
	    </div>  
	    <?= $this->megamenu(); ?>
	</header>
	<main><?= $this->_content; ?></main>
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>	
	<script>
		/*
		Copyright 2025 Google LLC
		Licensed under the Apache License, Version 2.0 (the "License");
		you may not use this file except in compliance with the License.
		You may obtain a copy of the License at
		  https://www.apache.org/licenses/LICENSE-2.0
		Unless required by applicable law or agreed to in writing, software
		distributed under the License is distributed on an "AS IS" BASIS,
		WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
		See the License for the specific language governing permissions and
		limitations under the License.
		*/
		document.addEventListener("DOMContentLoaded", function(e) { 	
		const form = document.querySelector("form");
		const passwordInput = document.querySelector("input#current-password");
		const signinButton = document.querySelector("button#signin");
		const togglePasswordButton = document.querySelector("button#toggle-password");

		togglePasswordButton.addEventListener("click", togglePassword);

		function togglePassword() {
		  if (passwordInput.type === "password") {
		    passwordInput.type = "text";
		    togglePasswordButton.classList.toggle('hide-pwd');
		    togglePasswordButton.setAttribute("aria-label", "Hide password.");
		  } else {
		    passwordInput.type = "password";
		    togglePasswordButton.classList.toggle('hide-pwd');
		    togglePasswordButton.setAttribute(
		      "aria-label",
		      "Show password as plain text. " +
		        "Warning: this will display your password on the screen."
		    );
		  }
		}

		// passwordInput.addEventListener('input', validatePassword);

		// A production site would use more stringent password testing on the client
		// and would sanitize and validate passwords on the back end.
		function validatePassword() {
		  let message = "";
		  if (!/.{8,}/.test(passwordInput.value)) {
		    message = "At least eight characters. ";
		  } else if (!/.*[A-Z].*/.test(passwordInput.value)) {
		    message += "\nAt least one uppercase letter. ";
		  } else if (!/.*[a-z].*/.test(passwordInput.value)) {
		    message += "\nAt least one lowercase letter.";
		  } else {
		    // message += '\nPassword is incorrect. Please try again.';
		  }

		  passwordInput.setCustomValidity(message);
		}

		
	});
  	</script>	
</body>
</html>