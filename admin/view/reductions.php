<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/reductions.css" />
		<script type="text/javascript" src="admin/view/js/reductions.js"></script>
		<script type="text/javascript">
			var api_url = "<?php $router->API(""); ?>";
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		<div class="container">
			<div class="row">
				<section class="col-md-12">
					<h1>Réductions</h1>

					<div id="presences">
						<div class="form-group">
							<label for="selectedSection">Sélectionnez la section :</label>
							<select id="selectedSection" class="form-control">
								<?php echo $sectionsHtml; ?>
							</select>
						</div>
						<div id="content" class="card">
						</div>
					</div>
					<p id="traitement" class="text-center">
						Les présences ont été validées.<br />
						Traitement en cours ...
					</p>
					<p id="retour" class="text-center">
						Les présences ont été validées.<br /><br />
						<a class="btn btn-snake" href=<?php $router->Url("home"); ?>>Retour à l'accueil</a>
					</p>
				</section>
			</div>
		</div>
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>