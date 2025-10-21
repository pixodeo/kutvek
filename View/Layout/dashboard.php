<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-obf="<?= base64_encode($this->uri('identities.login',['queries' => ['r' => 'dashboard']])) ?>" data-layout="dashboard">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-58SGQCK');</script>
	<!-- End Google Tag Manager -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Designs By KUTVEK | Dashboard</title>
	<style>
		.main-row.account-parameters {
			margin-top: 4rem;
		}

		.material-symbols-rounded {
			font-variation-settings:
				'FILL' 0,
				'wght' 400,
				'GRAD' 0,
				'opsz' 48
		}

		label.selected {order:1}

		/* .account-circle {
			font-variation-settings:
				'FILL' 1
		}

		.hidden {
			visibility: hidden;
		}

		.trads-container > ul > li.current > a {
			pointer-events: none;
		}

		.accordion_title {
			display: flex;
			align-items: center;
		}

		.accordion_title span.icon {
			font-size: 3.2rem;
			margin-right: .8rem;
		}

		.bloc-update .field-wrapper {
			flex-grow: 1;
			flex-shrink: 1;
		}

		.field-wrapper>.field-input.read-only {
			border-color: transparent !important;
			background: transparent;
		}

		button.small,
		.btn.small {
			height: 3.2rem !important;
			font-size: 1.1rem;
			padding: 0 1.2rem !important;
		}

		.field-wrapper.auto label,
		.field-wrapper.auto .label {
			max-width: initial;
		}

		@media only screen and (min-width: 1024px) {
			.main-row.account-parameters {
				padding: 0 3.2rem 0 5.2rem;
			}
		} */
	</style>
	<script src="<?= $this->auto_version('/js/auth2.js'); ?>"></script>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/app.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/dashboard.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/cart.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" media="screen">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<?= $this->fetch('css'); ?>
</head>
<body class="dashboard_body az customer-layout">
	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58SGQCK" height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?= $this->app->mainHeader($slugs); ?>
	<?= $this->view; ?>
	<?= $this->app->mainFooter(); ?>
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>  
	<script src="<?= $this->auto_version('/js/front/tabs.js'); ?>"></script>
	<?= $this->fetch('scriptBottom'); ?>
	
</body>

</html>