<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="identity">

<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->title; ?></title>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css') ?>">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css') ?>">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/cart.css') ?>">
	<?= $this->fetch('css'); ?>	
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital@0;1&amp;Kalam:wght@700&amp;family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,500;1,600&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<style>
		.material-symbols-rounded {
			font-variation-settings:
				'FILL' 0,
				'wght' 400,
				'GRAD' 0,
				'opsz' 48
		}

		.account-circle {
			font-variation-settings:
				'FILL' 1
		}

		.trads-container>ul>li.current>a {
			pointer-events: none;
		}

		/** LOGIN */
		.bloc-login {
			background-color: #F5F5F5;
			background-color: #FAFAFA;
			padding: 2.4rem;
			margin: 0 auto 2.4rem;
			border-width: 0.1rem;
			border-style: ridge;
			min-height: 30rem;
		}

		.bloc-login.auto {
			min-height: initial;
		}

		.bloc-login.no-wrap {
			min-height: initial;
			border: none;
		}

		.bloc-login .title,
		.login .title {
			font-family: 'Oswald';
			font-weight: 400;
			font-size: 3.2rem;
		}

		.bloc-login .title+hr {
			margin-top: 1rem;
			margin-bottom: 1rem;
			border: 0;
			border-bottom-width: .1rem;
			border-style: solid;
			border-color: rgba(0, 0, 0, 0.1);
		}

		.bloc-login .title>.icon {
			color: red;
			font-size: 4.8rem;
			vertical-align: text-bottom;
			margin-right: .8rem;
		}

		@media only screen and (min-width: 1281px) {
			.bloc-login .title {
				height: 4.8rem;
				line-height: 4.8rem;
			}

			.bloc-login {
				width: 80%;
			}
		}
	</style>
</head>
<body>
	<header class="main">
		<a href="<?= $this->uri('pages.index', []) ?>" class="logo"><img src="<?= HALLOWEEN === 1 ? '/img/charter/logo_kutvek_orange.png' : '/img/charter/logo_kutvek.png';?>"></a>
	</header>	
	<div class="main-row">
		<?= $this->view; ?>
	</div>
	<?= $this->app->mainFooter(); ?>
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>  
	<?= $this->fetch('scriptBottom'); ?>	
</body>
</html>