<?php
use Snake\SnakeTools;

global $router;
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/email.css" />
		<script type="text/javascript" src="admin/view/js/email.js"></script>
		<script type="text/javascript">
			var api_url = "<?php $router->api('email'); ?>";
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		<div class="container">
			<div class="row">
				<div id="mainTitle">
					<h1>Envoi d'E-mail</h1>
					<a class="btn btn-primary" href=<?= $router->url('home') ?>>
						Retour
					</a>
				</div>

				<?php if ($session->selectedSaison === SnakeTools::getCurrentSaison() || $session->selectedSaison === SnakeTools::getPreviousSaison()) { ?>
					<?php if ($session->selectedSaison === SnakeTools::getPreviousSaison()) { ?>
						<section class="col-md-12">
							<div class="alert alert-danger">
								<strong>Attention !</strong> Vous allez envoyer un E-mail aux parents et adhérents de la saison précédente.
							</div>
						</section>
					<?php } ?>

					<section id="email" class="col-md-12">
						<div id="emailForm">
							<div> <!-- action=<?php $router->api('email'); ?> method="POST" -->
								<div class="mb-3 row">
									<label class="col-form-label col-sm-2" for="inputSection">Section</label>
									<div class="col-sm-10">
										<select id="inputSection" class="form-select" name="id_section">
											<?php echo $sectionsHtml; ?>
										</select>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-form-label col-sm-2" for="inputSubject">Sujet</label>
									<div class="col-sm-10">
										<input id="inputSubject" class="form-control" type="text" name="subject" placeholder="Entrez le sujet de votre E-mail">
									</div>
								</div>
								<div id="attachements" class="mb-3 row">
									<label class="col-form-label col-sm-2" for="inputAttachements">Pièces jointes </label>
									<div class="col-sm-10">
										<button id="addAttachement" class="btn btn-snake"><i class="fas fa-plus-square"></i></button>
										<span id="attachementsList"></span>
									</div>
								</div>
								<div class="mb-3 row">
									<div class="col-sm-12">
										<label for="inputMessage">Message</label>
										<textarea id="inputMessage" class="form-control" type="text" name="message" rows="5"></textarea>
									</div>
								</div>

								<div class="col-sm-12">
									<div class="d-flex flex-row-reverse">
										<button id="formButton" class="btn btn-snake" type="submit">Envoyer</button>
									</div>
								</div>
							</div>
							<input id="attachementSelectFile" type="file" name="" multiple />
						</div>

						<div id="emailSend">
							<p class="text-center">
								Envoi en cours ...
							</p>
							<div class="spinner-border text-secondary" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</div>

						<div id="emailResponse"></div>
					</section>
				<?php } else { ?>
					<section class="col-md-12">
						<div class="alert alert-warning">
							<strong>Désolé,</strong> vous ne pouvez pas envoyer d'E-mail à une saison trop ancienne.
						</div>
					</section>
				<?php } ?>
			</div>
		</div>
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>