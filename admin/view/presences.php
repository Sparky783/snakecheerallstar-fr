<?php
global $router;
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/presences.css" />
		<script type="text/javascript" src="admin/view/js/presences.js"></script>
		<script type="text/javascript">
			var api_url = "<?php $router->API(""); ?>";
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>

		<div class="container">
			<div class="row">
				<section class="col-md-12">
					<div id="mainTitle">
						<h1>Suivi des présences</h1>
						<a class="btn btn-primary" href=<?= $router->url('home') ?>>
							Retour
						</a>
					</div>

					<?php if ($canBeDisplayed) { ?>
						<div id="presences">
							<div class="form-group">
								<label for="selectedSection">Sélectionnez la section :</label>
								<select id="selectedSection" class="form-select">
									<?php echo $sectionsHtml; ?>
								</select>
							</div>
							<div class="d-grid gap-2 mt-2">
								<button id='statButton' class='btn btn-snake' type='button'>Voir les statistiques</button>
							</div>
							<div id="presences-content" class="card">
								<div class='card-header text-center'>
								</div>

								<div id='adherentsList' class='card-body'>
								</div>

								<div class='card-footer text-center'>
									<button id='validatePresences' class='btn btn-primary' type='button'>Valider les présences</button> 
								</div>
							</div>
						</div>
					<?php } else { ?>
						<div class="alert alert-warning">
							<strong>Désolé,</strong> vous ne pouvez pas valider les présences pour les saisons précédentes.
						</div>
					<?php } ?>
				</section>
			</div>
		</div>

		<div id="messageModal" class="modal" tabindex="-1">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
					</div>
				</div>
			</div>
		</div>

		<?php include_once("FOOTER.php"); ?>
	</body>
</html>