<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/ag_elections.css" />
		<script type="text/javascript" src="admin/view/js/ag_elections.js"></script>
		<script type="text/javascript">
			var api_url = "<?php $router->API(""); ?>";
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1>Candidats pour l'Assemblé Générale</h1>
				</div>
				<section class="col-md-8">
					<div class="card">
						<div class="card-header clearfix">
							<span class="float-left">Il y a <span id="nbCandidats"></span> candidats</span>
							<button id="addCandidatButton" class="btn btn-primary float-right" type="button"><i class="fas fa-plus-circle"></i> Ajouter un candidat</button>
						</div>
						<table id="tableCandidats" class="card-body table table-hover">
							<thead>
								<tr>
									<th scope="col">Nom</th>
									<th scope="col">Prénom</th>
									<th class='text-right' scope="col">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</section>
				<section class="col-md-4">
					<div class="card">
						<div class="card-header">
							<h4>Résultats</h4>
						</div>
						<table id="resultats" class="card-body table table-striped">
							<tbody>
							</tbody>
						</table>
					</div>
				</section>
			</div>
		</div>
		<?php include_once("FOOTER.php"); ?>
		
		<!-- Modals -->
		<div id="addCandidatModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Ajouter un candidat</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="lastnameInput">Nom</label>
								<input id="lastnameInput" class="form-control" type="text">
							</div>
							<div class="form-group">
								<label for="firstnameInput">Prénom</label>
								<input id="firstnameInput" class="form-control" type="text">
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
							<button class="accepteButton btn btn-primary" type="submit">Ajouter</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div id="editCandidatModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Modifier un candidat</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="lastnameInput">Nom</label>
								<input id="lastnameInput" class="form-control" type="text">
							</div>
							<div class="form-group">
								<label for="firstnameInput">Prénom</label>
								<input id="firstnameInput" class="form-control" type="text">
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
							<button class="accepteButton btn btn-primary" type="submit">Modifier</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div id="removeCandidatModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Supprimer un candidat</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							Voulez-vous vraiment supprimer le candidat "<span id="nameCandidat"></span>"?
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Non</button>
							<button class="btn btn-primary" type="submit">Oui</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>