<!DOCTYPE html>
<html lang="<?=$this->_router->lang ?? 'fr';?>">
	<head>  
        <meta charset="utf-8">     
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">       
        <title>Nous sommes fermé | We are closed</title>        
        <link rel="icon" type="image/x-icon" href="/favicon.ico"> 

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&amp;display=swap">
       	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css')?>" type="text/css" media="screen">
        <style>
        	@font-face {
        font-family: 'Montserrat';
        src: url('/css/fonts/Montserrat-Regular.woff2') format('woff2'),
        url('/css/fonts/Montserrat-Regular.woff') format('woff');
        font-weight: normal;
        font-style: normal;
        font-display: swap;
    	}

	    @font-face {
	    font-family: 'Oswald';
	    src: url('/css/fonts/Oswald-Regular.woff2') format('woff2'),
	        url('/css/fonts/Oswald-Regular.woff') format('woff');
	    font-weight: 400;
	    font-style: normal;
	    font-display: swap;
		}
		
		@font-face {
	    font-family: 'Oswald';
	    src: url('/css/fonts/Oswald-Light.woff2') format('woff2'),
	        url('/css/fonts/Oswald-Light.woff') format('woff');
	    font-weight: 300;
	    font-style: normal;
	    font-display: swap;
		}
		@font-face {
	    font-family: 'Oswald';
	    src: url('/css/fonts/Oswald-Bold.woff2') format('woff2'),
	        url('/css/fonts/Oswald-Bold.woff') format('woff');
	    font-weight: 700;
	    font-style: normal;
	    font-display: swap;
		}
		@font-face {
	    font-family: 'Montserrat';
	    src: url('/css/fonts/Montserrat-Medium.woff2') format('woff2'),
		        url('/css/fonts/Montserrat-Medium.woff') format('woff');
	    font-weight: 500;
	    font-style: normal;
	    font-display: swap;
		}
        * {
		    -moz-box-sizing: border-box;
		    -webkit-box-sizing: border-box;
		    box-sizing: border-box;
		}
        body {font-size: 16px; margin: 0; padding:0;font-family: 'Montserrat';color: #000000;}	
        header {display: flex;align-items: center; justify-content: space-between;padding:12px 12px 0 12px;}
        header .logo {height: 64px; width: auto;}
        span.icon {font-size: 36px;cursor: pointer;}
		.material-symbols-rounded {
		  font-variation-settings:
		  'FILL' 0,
		  'wght' 400,
		  'GRAD' 0,
		  'opsz' 48
		}
		.account-circle { font-variation-settings: 'FILL' 1	}
		.center {
			text-align: center;
		    position: relative;
		    top: -32px;
		}
		img {max-width: 100%;}
		main {
			min-height: calc(100vh - 160px);
		    font-size: 24px;		    
		}
		h1 {font-family: Oswald;
		    text-align: center;
		    margin-bottom: 0;
		    margin-top: 16px;
		    position: relative;
		    z-index: 1;
		    background-color: #ffffff;
		    padding-bottom: 16px;
		    font-size: 56px;
		    text-transform: uppercase;
		    font-weight: 400;
			
		}
		p{padding:0 16px;}
		p.connexion{text-align: center;margin:16px 0 32px;padding:0;}
		img.closed {max-width: 224px;    transform: rotate(-8deg);}
		a {
			padding-top: 1.6rem;
    		text-decoration: underline;
    		font-weight: bold;
    		color: inherit;
		}
		footer {
			padding-top: 16px;
		    text-align: center;
		    background-color: #000000;
		}
		footer .logo {
			height: 64px;
		}
		@media only screen and (min-width: 1024px) {
			main {text-align: justify;}
		}
		</style>
    </head>
<body>	
	<header>
		<img src="/img/charter/logo-kutvek.png" class="logo" alt="" />			
		<span  class="action user-account">				
		</span>			 
	</header>
	<main>
		<?= $this->view; ?>
	</main>	
	
	
	<footer>
		<img src="/img/charter/logo-footer.png" class="logo" alt="" />
	</footer>	
	<script src="<?= $this->auto_version('/js/app.js')?>"></script>
		
</body>
</html>