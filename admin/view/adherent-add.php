<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/adherent-add.css" />
		<script type="text/javascript" src="admin/view/js/adherent-add.js"></script>
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
						<h1>Ajouter un adhérent</h1>
						<a class="btn btn-primary" href=<?= $router->url('adherents') ?>>
							Retour
						</a>
					</div>
					<div id="addAdherent" class="card">
						<div class="card-header clearfix">
							<div id="payment">
								<div class="form-group">
									<label for="paymentInput">Mode de paiement</label>
									<select id="paymentInput" class="form-control" name="paymentInput">
										<option value="cheque">Cheque</option>
										<option value="espece">Espece</option>
										<option value="virement">Virement</option>
									</select>
								</div>
								<div class="form-group">
									<label for="amountInput">Deadlines (pour les chèques uniquement)</label>
									<div class="form-group">
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="deadlinesInput" value="1">
											<label class="form-check-label" for="deadlinesInput">1 fois</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="deadlinesInput" value="2">
											<label class="form-check-label" for="deadlinesInput">2 fois</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="deadlinesInput" value="3">
											<label class="form-check-label" for="deadlinesInput">3 fois</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="deadlinesInput" value="4">
											<label class="form-check-label" for="deadlinesInput">4 fois</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="remiseInput">Remise hors tarif fratrie (optionnel)</label>
									<div class="input-group mb-3">
										<input id="remiseInput" class="form-control" name="remiseInput" type="text" aria-label="Remise">
										<div class="input-group-append">
											<select id="remiseTypeInput" class="custom-select input-group-text" name="remiseTypeInput">
												<option value="percent">%</option>
												<option value="fixed">€</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="modal-header">
										<h5 class="modal-title">Adhérents d'une famille</h5>
										<button id="btnAddAdherent" class="btn btn-primary" type="button">
											<i class="fas fa-plus"></i>
										</button>
									</div>
									<div class="modal-body">
										<div id="adherents">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="modal-header">
										<h5 class="modal-title">Tuteurs associés</h5>
										<button id="btnAddTuteur" class="btn btn-primary" type="button">
											<i class="fas fa-plus"></i>
										</button>
									</div>
										<div class="modal-body">
										<div id="tuteurs">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="modal-footer">
										<button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
										<button id="validButton" class="btn btn-primary" type="submit">Ajouter</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>

				<div id="templateAdherent" class="adherent card">
					<div class="card-body">
						<button class='remove-button btn btn-danger'><i class='fas fa-trash'></i></button>
						<div class="form-group">
							<label for="nomInput">Nom</label>
							<input id="nomInput" class="form-control" name="nomInput" type="text">
						</div>
						<div class="form-group">
							<label for="prenomInput">Prénom</label>
							<input id="prenomInput" class="form-control" name="prenomInput" type="text">
						</div>
						<div class="form-group">
							<label for="birthdayInput">Date de naissance</label>
							<input class="form-control" name="birthdayInput" type="date">
						</div>
						<div class="form-group">
							<div class="form-check form-check-inline">
								<label class="form-check-label">Traitement médical :</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input inputRadioMedicine1" name="medicineInput" type="radio" value="yes">
								<label class="form-check-label inputRadioMedicine1">Oui</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input inputRadioMedicine2" name="medicineInput" type="radio" value="no">
								<label class="form-check-label inputRadioMedicine2">Non</label>
							</div>
							<div class="form-inline traitementInfo">
								<label for="traitementInfoInput">Préciser :</label>
								<input class="form-control" name="traitementInfoInput" type="text">
							</div>
						</div>
					</div>
				</div>

				<div id="templateTuteur" class="tuteur card">
					<div class="card-body">
						<button class='remove-button btn btn-danger'><i class='fas fa-trash'></i></button>
						<div class="form-group">
							<label for="statusInput">Statut</label>
							<select id="statusInput" class="form-control" name="statusInput">
								<option value="adherent">Adhérent</option>
								<option value="father">Père</option>
								<option value="mother">Mère</option>
								<option value="tutor">Tuteur</option>
							</select>
						</div>
						<div class="form-group">
							<label for="nomInput">Nom</label>
							<input id="nomInput" class="form-control" name="nomInput" type="text">
						</div>
						<div class="form-group">
							<label for="prenomInput">Prénom</label>
							<input id="prenomInput" class="form-control" name="prenomInput" type="text">
						</div>
						<div class="form-group">
							<label for="emailInput">E-mail</label>
							<input id="emailInput" class="form-control" name="emailInput" type="email">
						</div>
						<div class="form-group">
							<label for="phoneInput">Téléphone</label>
							<input id="phoneInput" class="form-control" name="phoneInput" type="text">
						</div>
					</div>
				</div>

			</div>
		</div>
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>