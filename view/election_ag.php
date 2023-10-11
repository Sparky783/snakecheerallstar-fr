<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Site officiel de cheerleading en France et dans le Sud-Ouest.</title>
		<meta name="description" content="">
		
		<link href="https://fonts.googleapis.com/css?family=Poppins:700" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="view/css/election_ag.css" />

		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<script type="text/javascript" src="view/js/form.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#electionSend").hide();
				$("#electionResponse").hide();

				$("#electionForm form").SpForm(
					function(){
						$("#electionForm").hide();
						$("#electionSend").show();
					},
					function(data){
						console.log(data);
						if(data.error)
							console.log(data.errorMessage);
						$("#electionResponse").html('<p class="text-center"><i class="fa fa-grin-alt"></i> ' + data.message + '</p>');
						$("#electionSend").hide();
						$("#electionResponse").show();
					}
				);
			});

			function sendFormCallback() {
				$("#electionForm form").submit();
			}
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		
		<main class="container snake-body">
			<div class="row">
				<div class="col-md-12">
					<h1 class="text-center">
						Election de l'assemblé générale<br /><?php echo (date("Y") - 1) . "/" . date("Y"); ?>
					</h1>
				</div>
				<section class="col-md-6 offset-md-3">
					<div id="electionResponse" class="snake-form-response">
					</div>
					<div id="electionForm">
						<form class="snake-form" action=<?php $router->API("election_ag"); ?> method="post">
							<small class="form-text text-muted"><span class="snake-form-star">*</span> Ces champs sont obligatoires.</small>
							<div class="form-group">
								<label for="inputName"><span class="snake-form-star">*</span>Nom et prénom :</label>
								<input id="inputName" class="form-control" type="text" name="name" aria-describedby="nameHelp" placeholder="Entrez votre nom et votre prénom">
							</div>
							<div class="form-group">
								<label for="inputEmail"><span class="snake-form-star">*</span>E-mail :</label>
								<input id="inputEmail" class="form-control" type="email" name="email" aria-describedby="emailHelp" placeholder="Entrez votre adresse E-mail">
							</div>
							<div class="card">
								<div class="card-body">
									<h5 class="card-title">Rapports</h5>
								</div>
								<table class="table table-sm table-borderless table-striped">
									<tbody>
										<tr>
											<td>
												<label for='inputMessage'>Rapport moral</label>
											</td>
											<td class='choice'>
												<div class='custom-control custom-radio custom-control-inline'>
													<input id='rapportMoralyes' class='custom-control-input' type='radio' name='rapportMoral' value='yes'>
													<label class='custom-control-label' for='rapportMoralyes'>Oui</label>
												</div>
												<div class='custom-control custom-radio custom-control-inline'>
													<input id='rapportMoralno' class='custom-control-input' type='radio' name='rapportMoral' value='no'>
													<label class='custom-control-label' for='rapportMoralno'>Non</label>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<label for='inputMessage'>Rapport financier</label>
											</td>
											<td class='choice'>
												<div class='custom-control custom-radio custom-control-inline'>
													<input id='rapportFinancieryes' class='custom-control-input' type='radio' name='rapportFinancier' value='yes'>
													<label class='custom-control-label' for='rapportFinancieryes'>Oui</label>
												</div>
												<div class='custom-control custom-radio custom-control-inline'>
													<input id='rapportFinancierno' class='custom-control-input' type='radio' name='rapportFinancier' value='no'>
													<label class='custom-control-label' for='rapportFinancierno'>Non</label>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<label for='inputMessage'>Nouveau réglement intérieur</label>
											</td>
											<td class='choice'>
												<div class='custom-control custom-radio custom-control-inline'>
													<input id='cotisationyes' class='custom-control-input' type='radio' name='cotisations' value='yes'>
													<label class='custom-control-label' for='cotisationyes'>Oui</label>
												</div>
												<div class='custom-control custom-radio custom-control-inline'>
													<input id='cotisationno' class='custom-control-input' type='radio' name='cotisations' value='no'>
													<label class='custom-control-label' for='cotisationno'>Non</label>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="card">
								<div class="card-body">
									<h5 class="card-title">Candidats au conseil</h5>
								</div>
								<table class="table table-sm table-borderless table-striped">
									<?php echo $html; ?>
								</table>
							</div>
							<div class="text-right">
								<?php if(ENV == "DEV") { ?>
									<button class="btn btn-snake" type="submit" data-callback="sendFormCallback">Envoyer</button>
								<?php } else { ?>
									<button class="btn btn-snake g-recaptcha" type="submit" data-sitekey="<?= RECAPTCHA_PUBLIC_KEY ?>" data-callback="sendFormCallback">Envoyer</button>
								<?php } ?>
							</div>
						</form>
					</div>
					<div id="electionSend" class="snake-form-send">
						<p class="text-center">
							Envoi en cours ...<br />
							Veuillez patienter
							<br /><br />
							<i class="fas fa-spinner fa-spin"></i>
						</p>
					</div>
				</section>
			</div>
		</main>
		
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>