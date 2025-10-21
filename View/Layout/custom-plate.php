<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="custom-plate">

<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-58SGQCK');</script>
	<!-- End Google Tag Manager -->
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->getTitle(); ?></title>
	<meta name="description" content="<?= $this->getDescription();?>">
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/app.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/products.css'); ?>" type="text/css" media="screen">

	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/front.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<?= $this->fetch('css'); ?>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital@0;1&amp;Kalam:wght@700&amp;family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,500;1,600&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
	<style>
		/*.material-symbols-rounded {
			font-variation-settings:
				'FILL' 0,
				'wght' 400,
				'GRAD' 0,
				'opsz' 48
		}*/
		.account-circle {
			font-variation-settings:
				'FILL' 1
		}

		.load {
			animation: 1s cubic-bezier(.36, .07, .57, .99) infinite load_rotate;
		}

		@keyframes load_rotate {
			from {
				transform: rotate(0deg);
			}

			to {
				transform: rotate(360deg);
			}
		}

		.custom > img {
			width: 100%;
		}

		.custom > a {
			position: relative;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.custom .custom-kit-button {
			font-family: 'Oswald';
			position: absolute;
			font-size: 2.4rem;
			height: 7.2rem !important;
			border-bottom-right-radius: 25%;
			border-top-left-radius: 25%;
			background-color: red;
			color: white;
		}

		.custom {
			display: flex;
			justify-content: center;
		}

		#selected-accessories {
			flex-direction: column;
		}

		.p-cart .items .options {
			font-size: 1.6rem;
		}

		.graphic-kit {
			display: flex;
			justify-content: center;
			margin: 3.2rem 0;
		}
		
		.graphic-kit > a {
			position: relative;
			display: flex;
			align-items: center;
		}
		
		.graphic-kit > a > p.title {
			position: absolute;
			color: white;
			font-size: 3.2rem;
			font-family: 'Oswald';
			padding-left: 3.2rem;
		}

		img.cover {
			display: block;
			margin: auto;
		}

		.bloc-infos {
			border-left: 0.2rem solid #ff0000;
			margin: 2.4rem 0 1.6rem 0.8rem;
			padding: 1.2rem 0 1.2rem 0.8rem;
		}

		.info-plastics {
			background-color: #c8dadf;
			color: black;
			font-family: 'din_procondensed_bold';
			letter-spacing: .5px;
			display: inline-flex;
			align-items: center;
			padding: 0.4rem 0.8rem;
			border-radius: 0.2rem;
			margin-top: 0.8rem;
			line-height: 1.6rem;
		}

		.info-plastics .icon {
			padding-right: 0.4rem;
		}

		.main-row {
			margin: 3.2rem 0 3.2rem 0;
		}

		@media only screen and (min-width: 1024px) {
			.graphic-kit > a > p.title {
				font-size: 8rem;
			}
		}

		.plates-wrapper { display: flex; flex-wrap: wrap; justify-content: center; }
		.plates-wrapper input.plates {
			position: absolute;
			left: -9999px;
		}

		.plates-wrapper input.plates + label {
			position: relative;
			background-color: #FFF;
			padding: 4rem 1.5rem 1rem;
			margin: 0.5rem;
			cursor: pointer;
			text-align: center;
			border-radius: 0.5rem;
			border: 1px solid #c1c1c1;
		}

		.plates-wrapper input.plates + label > span {
			display: block;
			text-align: center;
			font-family: Oswald;
			font-size: 1.6rem;
			letter-spacing: .5px;
			text-transform: uppercase;
			margin-top: 0.5rem;
			max-width: 18rem;
			margin: 0 auto;
		}
		input.plates:checked + label::before {
			background-color: #fc0d1b;
		}
		input.plates +label::before {
			content: '';
			display: block;
			position: absolute;
			top: 1rem;
			right: 1rem;
			width: 2.2rem;
			height: 2.2rem;
			border: 3px solid #666;
			border-radius: 100%;
			transition: background-color .2s;
		}

		.plates-wrapper img { opacity: 0.6; }
		.plates-wrapper input.plates:checked + label > img { opacity: 1; }
	</style>

</head>
<body>
	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58SGQCK" height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?= $this->app->mainHeader($slugs); ?>
	<div class="main-row row">
		<?= $this->view; ?>
	</div>

	<?= $this->app->mainFooter(); ?>
    <script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>

	<?= $this->fetch('scriptBottom'); ?>
	<?= $this->fetch('dedicatedScripts'); ?>
</body>

</html>