<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Site officiel de cheerleading en France et dans le Sud-Ouest.</title>
		<meta name="description" content="">
		
		<link href="https://fonts.googleapis.com/css?family=Poppins:700" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="view/css/inscription.css" />
		
		<script type='text/javascript' src='view/js/inscription.js'></script>
		<script>
			let urlApi = '<?= $router->getAPI('') ?>'
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>

		<main class="container snake-body">
			<?php if ($allowAccess) { ?>
				<div class="row">
					<div class="col-sm-12">
						<div id="inscription-paper">
							<a href=<?php $router->url('inscription-paper'); ?> title="">Inscription papier <i class="fas fa-chevron-right"></i></a>
						</div>
						<div id="steps">
							<div class="steps-line">
								<span></span>
							</div>
							
							<div class="steps-blocks">
								<div id="stepAdherents" class="step active">
									<span>1<br />Adhérents</span>
								</div>
								<div id="stepTuteurs" class="step">
									<span>2<br />Tuteurs</span>
								</div>
								<div id="stepAuthorisation" class="step">
									<span>3<br />Autorisations</span>
								</div>
								<div id="stepPayment" class="step">
									<span>4<br />Paiement</span>
								</div>
								<div id="stepValidation" class="step">
									<span>5<br />Confirmation</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php include('inscription-adherents.php') ?>
				<?php include('inscription-tuteurs.php') ?>
				<?php include('inscription-authorisation.php') ?>
				<?php include('inscription-payment.php') ?>
				<?php include('inscription-validation.php') ?>
			<?php } else { ?>
				<div class="row">
					<div class="col-sm-12">
						<div id='messageInscription'>
							<div class='alert alert-warning'>
								<i class='fas fa-exclamation-triangle'></i>
								Les inscriptions sont fermé pour le moment.
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</main>

		<?php include_once("FOOTER.php"); ?>
	</body>
</html>