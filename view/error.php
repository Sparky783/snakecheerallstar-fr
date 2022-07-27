<?php global $router; ?>

<!DOCTYPE html>
<html>
	<head>
		<?php include_once("view/HEAD.php"); ?>
		<title>Page d'erreur | <?php echo TITLE; ?></title>
		<meta name="description" content="Page d'erreur du site.">
		<meta name="robots" content="noindex, follow" />
		<link rel="stylesheet" type="text/css" href="view/css/home.css" />
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-6 offset-md-3">
					<div class="card" style="margin-top: 50px;">
						<div class="card-body text-center">
							<h5 class="card-title">Erreur 404 !</h5>
							<h6 class="card-subtitle mb-2 text-muted">Cette page n'existe pas.</h6>
							<p class="card-text">Veuillez retourner sur le site en cliquant ici :</p>
							<a class="card-link btn btn-snake" href=<?php $router->URL("accueil"); ?> title="Site officiel">Retour sur le site</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>