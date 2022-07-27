<?php
// ================================
// ==== Controleur Inscription ====
// ================================
// ==== Demande d'informations sur les tuteurs ====

global $router;

$script = "
	<script type='text/javascript' src='view/js/inscription_2.js'></script>
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
			<h2>Liste des représentants légals</h2>
			<span>Liste des tuteurs, tuteurs, ou adhérents majeurs.</span>
		</div>
		<button id='addTuteurButton' class='btn btn-primary' type='button'>
			<i class='fas fa-user-plus'></i> Ajouter un resprésentant légal
		</button>
	</div>
	
	<div id='guide' class='col-md-4'>
		<div class='card card-snake'>
			<div class='card-header'>
				<h4>Guide d'aide</h4>
			</div>
			<div class='card-body'>
				<p class='title'>Seconde étape</p>
				<p>
					Le seconde étape est d'indiquer le ou les représentants légaux des adhérents inscrit précédement.<br />
					Veuillez remplir le formulaire si contre correspondant à chaque tuteur.
				</p>
				<p class='alert alert-info'>
					<b>Note :</b> Si vous êtes <b>majeur</b> ou si vous souhaitez simplement recevoir les E-mail d'informations, veuillez vous ajouter ici.
				</p>
				<p class='alert alert-info'>
					Pour inscrire plusieurs représentant, veuillez cliquer sur le bouton <span class='btn btn-primary'><i class='fas fa-user-plus'></i> Ajouter un resprésentant légal</span> en haut à droite de la page.	
				</p>
				<p class='alert alert-danger'>
					<b>Note :</b> Tous les champs du formulaire doivent être remplis.
				</p>
			</div>
		</div>
	</div>

	<div id='inscription' class='col-md-8'>
		<div id='tuteurs'>
		</div>
		
		<div id='templateTuteur' class='tuteur card'>
			<div class='card-header'>
				<span class='tuteur-title'>
					Statut :
					<select class='tuteur-title' name='statusInput'>
						<option value='adherent'>Adhérent</option>
						<option value='father'>Père</option>
						<option value='mother'>Mère</option>
						<option value='tutor'>Tuteur</option>
					</select>
				</span>
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
					<label for='emailInput'>E-mail</label>
					<input class='form-control' name='emailInput' type='email'>
				</div>
				<div class='col-sm-6 form-group'>
					<label for='phoneInput'>Téléphone</label>
					<input class='form-control' name='phoneInput' type='text'>
				</div>
				<div class='col-sm-12'>
					<small class='form-text text-muted'>
						Nous vous enverrons par E-mail les informations relative au club et aux activités de votre ou vos enfants.
						Votre numéro de téléphone nous permettra de vous contacter en cas de problème ou de retard de l'un de nos coachs.
					</small>
				</div>
			</div>
		</div>
	</div>

	<div id='nextButton' class='col-md-12'>
		<div class='text-center'>
			<button id='validButton' class='btn btn-primary'>Valider et continuer</button>
		</div>
	</div>
";
?>