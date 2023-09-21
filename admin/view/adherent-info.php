<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/adherent-info.css" />
		<script type="text/javascript" src="admin/view/js/adherent-info.js"></script>
		<script type="text/javascript">
			var api_url = "<?php $router->api(''); ?>";
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		<div class="container">
			<div class="row">
				<section class="col-md-12">
					<div id="mainTitle">
						<h1>Infos adhérent</h1>
						<a class="btn btn-primary" href=<?= $router->url('adherents', ['section' => $adherent->getSection()->getId()]) ?>>
							Retour
						</a>
					</div>
					<div class="card">
						<div class="card-header">
							<h2><?php echo $htmlName; ?></h2>
							<div class="btn-group">
								<button id="btnActions" class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Actions
								</button>
								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnActions">
									<?php echo $actionsHtml; ?>
								</div>
							</div>
						</div>
						<div class="card-body">
							<?php echo $html; ?>
						</div>
					</div>
				</section>
			</div>
		</div>
		<?php include_once("FOOTER.php"); ?>
		
		<!-- Modals -->
		<div id="addTuteurModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form id="addTuteurForm">
						<div class="modal-header">
							<h5 class="modal-title">Ajouter un tuteur</h5>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
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
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuler</button>
							<button class="btn btn-primary" type="submit">Ajouter</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div id="sendBillModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form id="sendBillForm">
						<div class="modal-header">
							<h5 class="modal-title">Envoyer une facture</h5>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label>Destinataire</label>
								<select id="destBillInput" class="form-control">
									<?php echo $destsBillHtml; ?>
								</select>
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuler</button>
							<button class="btn btn-primary" type="submit">Envoyer</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div id="sendRecapModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form id="sendRecapForm">
						<div class="modal-header">
							<h5 class="modal-title">Envoyer le récapitulatif d'inscription</h5>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label>Destinataire</label>
								<select id="destRecapInput" class="form-control">
									<?php echo $destsBillHtml; ?>
								</select>
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuler</button>
							<button class="btn btn-primary" type="submit">Envoyer</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>