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
					<h1>Liste des adhérents</h1>
					<div class="card">
						<div class="card-header clearfix">
							<span class="float-left">Il y a <span id="nbAdherents"></span> adhérents</span>
							<div class="form-group">
								<select id="selectedSection" class="form-control">
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
									<th class="text-right" scope="col">Options</th>
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
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
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
							<h5 class="modal-title" id="exampleModalLabel">Supprimer un adhérent</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							Voulez-vous vraiment supprimer "<span id="nameAdherent"></span>" des adhérents ?
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Non</button>
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
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
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
							<button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
							<button class="btn btn-primary" type="submit">Exporter</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>