<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/users.css" />
		<script type="text/javascript" src="admin/view/js/users.js"></script>
		<script type="text/javascript">
			var api_url = "<?php $router->API(""); ?>";
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		<div class="container">
			<div class="row">
				<section class="col-md-12">
					<h1>Gestionnaire des utilisateurs</h1>
					<div class="card">
						<div class="card-header clearfix">
							<span class="float-left">Il y a <span id="nbUsers"></span> utilisateurs</span>
							<button id="addUserButton" class="btn btn-primary float-right" type="button"><i class="fas fa-plus-circle"></i> Ajouter un utilisateur</button>
						</div>
						<table id="tableUsers" class="card-body table table-hover">
							<thead>
								<tr>
									<th scope="col">Nom</th>
									<th scope="col">E-mail</th>
									<th scope="col">Droits</th>
									<th class="text-right" scope="col">Actions</th>
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
		<div id="addUserModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Ajouter un utilisateur</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="nameInput">Nom</label>
								<input id="nameInput" class="form-control" type="text">
							</div>
							<div class="form-group">
								<label for="emailInput">E-mail</label>
								<input id="emailInput" class="form-control" type="text">
							</div>
							<div class="form-group">
								<label for="rightInput">Droits</label>
								<select id="rightInput" class="form-control" multiple>
									<option value="member" selected>Membre</option>
									<option value="coach">Coach</option>
									<option value="tresorier">Trésorier</option>
									<option value="secretaire">Secrétaire</option>
									<option value="admin">Administrateur</option>
								</select>
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
		
		<div id="editUserModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Modifier un utilisateur</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="nameInput">Nom</label>
								<input id="nameInput" class="form-control" type="text">
							</div>
							<div class="form-group">
								<label for="emailInput">E-mail</label>
								<input id="emailInput" class="form-control" type="text">
							</div>
							<div class="form-group">
								<label for="rightInput">Droits</label>
								<select id="rightInput" class="form-control" multiple>
									<option value="member">Membre</option>
									<option value="coach">Coach</option>
									<option value="tresorier">Trésorier</option>
									<option value="secretaire">Secrétaire</option>
									<option value="admin">Administrateur</option>
								</select>
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

		<div id="reinitUserPasswordModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Réinitiliser le mot de passe</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							Voulez-vous vraiment réinitialiser le mot de passe de "<span id="nameUser"></span>"?
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Non</button>
							<button class="btn btn-primary" type="submit">Oui</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div id="removeUserModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Supprimer un utilisateur</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							Voulez-vous vraiment supprimer l'utilisateur "<span id="nameUser"></span>"?
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