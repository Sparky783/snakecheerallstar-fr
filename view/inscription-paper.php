<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Site officiel de cheerleading en France et dans le Sud-Ouest.</title>
		<meta name="description" content="">
		
		<link href="https://fonts.googleapis.com/css?family=Poppins:700" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="view/css/inscription-paper.css" />
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>

		<main class="container snake-body">
			<?php if ($allowAccess) { ?>
				<div class="row">
					<div class="col-sm-8 offset-sm-2">
						<div id="inscription-online">
							<a href=<?php $router->url('inscription'); ?> title="">Inscription en ligne <i class="fas fa-chevron-right"></i></a>
						</div>
						<h2 class="text-center mt-3 mb-4">Inscription papier</h2>
						<div class="card">
							<div class="card-body">
								<p>
									Si l'inscription en ligne ne vous convient pas, vous pouvez nous fournir une inscription papier.
									Pour ceci, imprimez et remplissez les documents suivants pour chaque adhérent (dans le cas d'une fratrie):
								</p>
								<ul>
									<li>Dossier d'inscription <a href='<?= URL ?>/content/dossier_inscription/dossier_snake.pdf' title='' target='_blank'>disponible ici</a></li>
									<li>Formulaire de la FFFA <a href='<?= URL ?>/content/dossier_inscription/licence_FFFA.pdf' title='' target='_blank'>disponible ici</a></li>
									<li>Autorisation parentale en cas d'accident <a href='<?= URL ?>/content/dossier_inscription/autorisation_parentale.pdf' title='' target='_blank'>disponible ici</a></li>
									<li>Questionnaire de santé (Mineur) <a href='<?= URL ?>/content/dossier_inscription/questionnaire_sante_mineur.pdf' title='' target='_blank'>disponible ici</a> (obligatoire)</li>
									<li>Questionnaire de santé (Majeur) <a href='<?= URL ?>/content/dossier_inscription/questionnaire_sante_majeur.pdf' title='' target='_blank'>disponible ici</a> (obligatoire)</li>
									<li>Formulaire de Sportmut <a href='<?= URL ?>/content/dossier_inscription/sportmut.pdf' title='' target='_blank'>disponible ici</a> (même si vous n'y adhérez pas)</li>
									<li>Formulaire d'autorisation médical à remplir <a href='<?= URL ?>/content/dossier_inscription/afld.pdf' title='' target='_blank'>disponible ici</a></li>
									<li>Certificat médical (pour tous les nouveaux inscrits)</li>
									<li>Photocopie de la pièce d'identité</li>
									<li>Photo d'identité</li>
								</ul>
								<p>
									Le paiement de la cotisation peut être fait soit:
								</p>
								<ul>
									<li>par chèque en une ou plusieurs fois (jusqu'à 4x) à l'ordre de "Snake Cheer All Star"</li>
									<li>en espèce et en <b>totalité</b></li>
								</ul>
								<p>
									Veuillez remettre ces élémens au coach de votre section ou à l'un des membres du bureau,
									<br /><br />
									Cordialement,<br />
									<?= TITLE ?>
								</p>
							</div>
						</div>
					</div>
				</div>
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