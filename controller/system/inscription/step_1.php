<?php
// ================================
// ==== Controleur Inscription ====
// ================================
// ==== Demande d'informations sur les adhérents ====

global $router;

$script = "
	<script type='text/javascript' src='view/js/inscription_1.js'></script>
	<script>
		$(document).ready(function(){
			$('#inscription').InscriptionManager({
				UrlApi: '" . $router->GetAPI("") . "'
			});
		});
	</script>
";

$html = "
	<div id='inscriptionMenu' class='col-md-12'>
		<div>
			<h2>Liste des adhérents à inscrire</h2>
			<span>Liste des adhérents d'une même famille</span>
		</div>
		<button id='addAdherentButton' class='btn btn-primary' type='button'>
			<i class='fas fa-user-plus'></i> Ajouter un adhérent
		</button>
	</div>

	<div id='guide' class='col-md-4'>
		<div class='card card-snake'>
			<div class='card-header'>
				<h4>Guide d'aide</h4>
			</div>
			<div class='card-body'>
				<p>
					Bienvenue sur le guide d'inscription. Ce guide sera présent tout au long de l'inscription pour vous aider à vous inscrire ou à inscrire vos enfants.
				</p>
				<p class='title'>Première étape</p>
				<p>
					La première étape de l'inscription consiste à indiquer l'adhérent ou les adhérents (élèves) qui pratiqueront le cheerleading au cours de cette saison.<br />
					Veuillez simplement remplir le formulaire ci-contre correspondant à un adhérent.
				</p>
				<p class='alert alert-info'>
					Pour inscrire plusieurs adhérents, veuillez cliquer sur le bouton <span class='btn btn-primary'><i class='fas fa-user-plus'></i> Ajouter un adhérent</span> en haut à droite de la page.<br />
					Le tarif fratrie sera appliqué suivant le nombre d'adhérent inscrit sur cette page.
				</p>
				<p class='alert alert-danger'>
					<b>Note :</b> Tous les champs du formulaire doivent être remplis.
				</p>
			</div>
		</div>
	</div>

	<div id='inscription' class='col-md-8'>
		<div id='adherents'>
		</div>
		
		<form id='templateAdherent' class='adherent card'>
			<div class='card-header'>
				<span class='adherent-title'>Adhérent</span>
				<button class='remove-button btn btn-danger'><i class='fas fa-trash'></i></button>
			</div>
			<div class='row card-body'>
				<div class='col-sm-6 form-group'>
					<label for='nomInput'>Nom</label>
					<input class='form-control' name='nomInput' type='text'>
				</div>
				<div class='col-sm-6 form-group'>
					<label for='prenomInput'>Prénom</label>
					<input class='form-control' name='prenomInput' type='text'>
				</div>
				<div class='col-sm-6 form-group'>
					<label for='birthdayInput'>Date de naissance</label>
					<input class='form-control' name='birthdayInput' type='date'>
				</div>
				<div class='clearfix'></div>
				<div class='col-sm-6 form-group'>
					<div class='form-check form-check-inline'>
						<label class='form-check-label'>Traitement médical :</label>
					</div>
					<div class='form-check form-check-inline'>
						<input class='form-check-input inputRadioMedicine1' name='medicineInput' type='radio' value='yes'>
						<label class='form-check-label inputRadioMedicine1'>Oui</label>
					</div>
					<div class='form-check form-check-inline'>
						<input class='form-check-input inputRadioMedicine2' name='medicineInput' type='radio' value='no'>
						<label class='form-check-label inputRadioMedicine2'>Non</label>
					</div>
					<div class='form-inline traitementInfo'>
						<label for='traitementInfoInput'>Préciser</label>
						<input class='form-control' name='traitementInfoInput' type='text'>
					</div>
				</div>
				<!--<div class='col-sm-12 form-group'>
					<div class='form-check form-check-inline'>
						<label class='form-check-label'>Avez-vous la tenue du club pour cette adhérent ? </label>
					</div>
					<div class='form-check form-check-inline'>
						<input class='form-check-input inputRadioTenue1' name='tenueInput' type='radio' value='yes'>
						<label class='form-check-label inputRadioTenue1'>Oui</label>
					</div>
					<div class='form-check form-check-inline'>
						<input class='form-check-input inputRadioTenue2' name='tenueInput' type='radio' value='no'>
						<label class='form-check-label inputRadioTenue2'>Non</label>
					</div>
				</div>-->
				<!--<div class='col-sm-12 form-group'>
					<div class='form-check form-check-inline'>
						<label class='form-check-label'>Adhésion Sportmut (optionnel) :</label>
					</div>
					<div class='form-check form-check-inline'>
						<input class='form-check-input inputRadioSportmut1' name='sportmutInput' type='radio' value='yes'>
						<label class='form-check-label inputRadioSportmut1'>Oui</label>
					</div>
					<div class='form-check form-check-inline'>
						<input class='form-check-input inputRadioSportmut2' name='sportmutInput' type='radio' value='no'>
						<label class='form-check-label inputRadioSportmut2'>Non</label>
					</div>
					<small class='form-text text-muted'>
						La Sportmut est une assurance pour les sportifs. Si elle est choisi, elle viens remplacer l'assurance de la FFFA (Fédération Française de Football Américain).
						La Sportmut est à rajouter en plus du pris de la cotisation, ce qui n'est pas le cas pour l'assurancede la fédération.<br />
						Pour plus d'information, consultez le document d'adhésion disponible <a href='' title=''>ici</a>.
					</small>
				</div>
				<div class='col-sm-12'>
					<div class='row form-group'>
						<label class='col-sm-3' for='inputIdCard'>Copie de la carte d'identité :</label>
						<input class='col-sm-9 form-control-file inputIdCard' name='idCardInput' type='file'>
						<small class='col-sm-12'>5 Mo Maximum. Format accepté: PDF, JPG, PNG, GIF, BMP</small>
					</div>
					<div class='row'>
						<label class='col-sm-3' for='inputPhoto'>Photo d'identité :</label>
						<input class='col-sm-9 form-control-file inputPhoto' name='photoInput' type='file'>
						<small class='col-sm-12'>5 Mo Maximum. Format accepté: PDF, JPG, PNG, GIF, BMP</small>
					</div>
				</div>-->
			</div>
		</form>
	</div>

	<div id='nextButton' class='col-md-12'>
		<div class='text-center'>
			<button id='validButton' class='btn btn-primary'>Valider et continuer</button>
		</div>
	</div>
";
?>