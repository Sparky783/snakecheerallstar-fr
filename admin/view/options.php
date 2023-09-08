<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/options.css" />
		<script type="text/javascript" src="admin/view/js/options.js"></script>
		<script type="text/javascript">
			var api_url = "<?php $router->API(""); ?>";
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		<div class="container">
			<h1>Options du site internet</h1>

			<div class="row">
				<section id="inscription" class="col-md-3">
					<div class="card">
						<div class="card-header clearfix">
							<h3>Inscriptions</h3>
						</div>
						<div class="card-body">
							<div id="openInscriptionBlock" class="custom-control custom-switch">
								<input id="cbOpenInscription" class="custom-control-input" type="checkbox" <?php echo $cbOpenInscriptionValue; ?> />
								<label class="custom-control-label" for="cbOpenInscription">Ouvrir les inscriptions</label>
							</div>
							<div class="form-group">
								<label for="tbMinDateInscription">Ouvert à partir du (inclus) :</label>
								<input id="tbMinDateInscription" class="form-control" type="date" value="<?php echo $tbMinDateIscriptionValue; ?>" />
							</div>
							<div class="form-group">
								<label for="tbMaxDateInscription">jusqu'au (exclus) :</label>
								<input id="tbMaxDateInscription" class="form-control" type="date" value="<?php echo $tbMaxDateInscriptionValue; ?>" />
							</div>
						</div>
					</div>
				</section>

				<section id="sections" class="col-md-9">
					<div class="card">
						<div class="card-header clearfix">
							<h3>Sections</h3>
							<span class="float-left">Il y a <span id="nbSections"></span> section</span>
							<button id="addSectionButton" class="btn btn-primary float-right" type="button"><i class="fas fa-plus-circle"></i> Ajouter une section</button>
						</div>
						<div id="sectionList" class="card-body">
							<table class="table table-hover">
								<thead>
									<tr>
										<th scope="col">Nom</th>
										<th scope="col">Année maximum</th>
										<th scope="col">Cotisation</th>
										<th scope="col">Prix location tenue</th>
										<th scope="col">Prix nettoyage tenue</th>
										<th scope="col">Prix achat tenue</th>
										<th scope="col">Prix caution tenue</th>
										<th scope="col">Nb Max Adhérents</th>
										<th class="text-right" scope="col">Actions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</section>

				<div id="applyBlock" class="col-md-12">
					<p class="text-center">
						<button class="btn btn-snake">Appliquer</button>
					</p>
				</div>
			</div>
		</div>
		<?php include_once("FOOTER.php"); ?>
		
		<!-- Modals -->
		<div id="addSectionModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Ajouter une section</h5>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="nameInput">Nom de la section</label>
								<input id="nameInput" class="form-control" type="text" name="name">
							</div>
							<div class="form-group">
								<label for="maxYearInput">Année de naissance maximum pour être dans la section</label>
								<input id="maxYearInput" class="form-control" type="text" name="maxYear">
							</div>
							<div class="form-group">
								<label for="cotisationPriceInput">Prix de la cotisation</label>
								<input id="cotisationPriceInput" class="form-control" type="text" name="cotisationPrice">
							</div>
							<div class="form-group">
								<label for="rentUniformPriceInput">Prix de location de la tenue</label>
								<input id="rentUniformPriceInput" class="form-control" type="text" name="rentUniformPrice">
							</div>
							<div class="form-group">
								<label for="cleanUniformPriceInput">Prix de nettoyage de la tenue</label>
								<input id="cleanUniformPriceInput" class="form-control" type="text" name="cleanUniformPrice">
							</div>
							<div class="form-group">
								<label for="buyUniformPriceInput">Prix d'achat de la tenue</label>
								<input id="buyUniformPriceInput" class="form-control" type="text" name="buyUniformPrice">
							</div>
							<div class="form-group">
								<label for="depositUniformPriceInput">Montant de caution de la tenue</label>
								<input id="depositUniformPriceInput" class="form-control" type="text" name="depositUniformPrice">
							</div>
							<div class="form-group">
								<label for="maxMembersInput">Nombre maximum d'ahdérent dans la section</label>
								<input id="maxMembersInput" class="form-control" type="text" name="maxMembers">
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuler</button>
							<button class="accepteButton btn btn-primary" type="submit">Ajouter</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div id="editSectionModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Modifier une section</h5>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="nameInput">Nom de la section</label>
								<input id="nameInput" class="form-control" type="text" name="name">
							</div>
							<div class="form-group">
								<label for="maxYearInput">Année de naissance maximum pour être dans la section</label>
								<input id="maxYearInput" class="form-control" type="text" name="maxYear">
							</div>
							<div class="form-group">
								<label for="cotisationPriceInput">Prix de la cotisation</label>
								<input id="cotisationPriceInput" class="form-control" type="text" name="cotisationPrice">
							</div>
							<div class="form-group">
								<label for="rentUniformPriceInput">Prix de location de la tenue</label>
								<input id="rentUniformPriceInput" class="form-control" type="text" name="rentUniformPrice">
							</div>
							<div class="form-group">
								<label for="cleanUniformPriceInput">Prix de nettoyage de la tenue</label>
								<input id="cleanUniformPriceInput" class="form-control" type="text" name="cleanUniformPrice">
							</div>
							<div class="form-group">
								<label for="buyUniformPriceInput">Prix d'achat de la tenue</label>
								<input id="buyUniformPriceInput" class="form-control" type="text" name="buyUniformPrice">
							</div>
							<div class="form-group">
								<label for="depositUniformPriceInput">Montant de caution de la tenue</label>
								<input id="depositUniformPriceInput" class="form-control" type="text" name="depositUniformPrice">
							</div>
							<div class="form-group">
								<label for="maxMembersInput">Nombre maximum d'ahdérent dans la section</label>
								<input id="maxMembersInput" class="form-control" type="text" name="maxMembers">
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuler</button>
							<button class="accepteButton btn btn-primary" type="submit">Modifier</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div id="removeSectionModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Supprimer une section</h5>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							Voulez-vous vraiment supprimer la section "<span id="sectionName"></span>"?
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
							<button class="btn btn-primary" type="submit">Oui</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div id="editHorairesModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">Modifier une section</h5>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="nameInput">Nom de la section</label>
								<input id="nameInput" class="form-control" type="text" name="name">
							</div>
							<div class="form-group">
								<label for="maxYearInput">Année de naissance maximum pour être dans la section</label>
								<input id="maxYearInput" class="form-control" type="text" name="maxYear">
							</div>
							<div class="form-group">
								<label for="cotisationPriceInput">Prix de la cotisation</label>
								<input id="cotisationPriceInput" class="form-control" type="text" name="cotisationPrice">
							</div>
							<div class="form-group">
								<label for="rentUniformPriceInput">Prix de location de la tenue</label>
								<input id="rentUniformPriceInput" class="form-control" type="text" name="rentUniformPrice">
							</div>
							<div class="form-group">
								<label for="cleanUniformPriceInput">Prix de nettoyage de la tenue</label>
								<input id="cleanUniformPriceInput" class="form-control" type="text" name="cleanUniformPrice">
							</div>
							<div class="form-group">
								<label for="buyUniformPriceInput">Prix d'achat de la tenue</label>
								<input id="buyUniformPriceInput" class="form-control" type="text" name="buyUniformPrice">
							</div>
							<div class="form-group">
								<label for="depositUniformPriceInput">Montant de caution de la tenue</label>
								<input id="depositUniformPriceInput" class="form-control" type="text" name="depositUniformPrice">
							</div>
							<div class="form-group">
								<label for="maxMembersInput">Nombre maximum d'ahdérent dans la section</label>
								<input id="maxMembersInput" class="form-control" type="text" name="maxMembers">
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuler</button>
							<button class="accepteButton btn btn-primary" type="submit">Modifier</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>