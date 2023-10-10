<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/adherents.css" />
		<script type="text/javascript" src="admin/view/js/adherents.js"></script>
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
						<h1>Liste des adhérents</h1>
						<a class="btn btn-primary" href=<?= $router->url('home') ?>>
							Retour
						</a>
					</div>
					<div class="card">
						<div class="card-header">
							<div class="section-selection">
								<span>Il y a <span id="nbAdherents"></span> adhérents</span>
								<select id="selectedSection" class="form-select mt-sm-2">
									<?php echo $sectionsHtml; ?>
								</select>
							</div>
							<div class="list-buttons">
								<div class="btn-group">
									<button id="buttonExport" class="btn btn-primary" data-toggle="modal" data-target="#exportModal">Exporter</button>
								</div>
								<?php echo $addAdhButtonHtml; ?>
							</div>
						</div>
						<table id="tableAdherents" class="card-body table table-hover">
							<thead>
								<tr>
									<th scope="col">Nom</th>
									<th scope="col">Prénom</th>
									<th class="text-end" scope="col">Options</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</section>
			</div>
		</div>
		<?php include_once("FOOTER.php"); ?>
		
		<!-- Modals -->
		<div id="validateModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 id="editModalTitle" class="modal-title"></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuler</button>
							<button id="accepteEditButton" class="btn btn-primary" type="submit">Modifier</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div id="removeModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Supprimer un adhérent</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							Voulez-vous vraiment supprimer "<span id="nameAdherent"></span>" des adhérents ?
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
							<button class="btn btn-primary" type="submit">Oui</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div id="changeSectionModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Supprimer un adhérent</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<span class="message"></span><br />
							<div class='form-check form-switch'>
								<label class='form-check-label' for='customSwitchChangeSectionEmail'>Envoyer un E-mail d'information</label>
								<input id='customSwitchChangeSectionEmail' class='form-check-input' type='checkbox' name='changeSectionEmail' />
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
							<button class="btn btn-primary" type="submit">Oui</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div id="exportModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form id="exportForm">
						<div class="modal-header">
							<h5 class="modal-title">Exporter la liste</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label>Section</label>
								<select id="exportSection" class="form-control">
									<option value="all" selected>Toutes les sections</option>
									<?php echo $sectionsHtml; ?>
								</select>
							</div>
							<div class="form-group">
								<label>Format CSV - Délimiter</label>
								<select id="exportDelimiter" class="form-control">
									<option value="pointvirgule" selected>(;) Français</option>
									<option value="virgule">(,) Anglais</option>
								</select>
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuler</button>
							<button class="btn btn-primary" type="submit">Exporter</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>