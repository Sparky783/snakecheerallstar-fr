<?php
// ================================
// ==== Controleur Inscription ====
// ================================
// ==== Acceptation des conditions générales et du réglement ====

global $router;

$script = "
	<script type='text/javascript' src='view/js/inscription_3.js'></script>
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
			<h2>Autorisations</h2>
			<span>Droits à l'image et autres éléments.</span>
		</div>
	</div>

	<div id='guide' class='col-md-4'>
		<div class='card card-snake'>
			<div class='card-header'>
				<h4>Guide d'aide</h4>
			</div>
			<div class='card-body'>
				<p class='title'>Troisième étape (mi-chemin)</p>
				<p>
					L'étape la plus simple et la plus importante. Elle vous engage à accepter les conditions ci-contre. Celles-ci sont obligatoires pour s'inscrire.
					<br /><br />
					Si vous <b>acceptez</b> ces conditions, vous devez cochez la case et passer à l'étape suivante.
					<br /><br />
					Si vous <b>refusez</b> ces conditions, vous pouvez quitter cette fenêtre en cliquant sur le bouton \"Accueil\" du menu. L'inscription ne sera donc pas valide.
				</p>
			</div>
		</div>
	</div>

	<div id='inscription' class='col-md-8'>
		<div id='authorization' class='card'>
			<div class='card-body'>
				<h3>Droit à l'image</h3>
				<p>
					En inscrivant vous ou vos enfants au club des Snake Cheer All Star, vous autorisez l'association à filmer, photographier et enregistrer votre image ou celle de vos enfants lors des activités et à reproduire, diffuser et exploiter librement les images ainsi réalisées.
					<br /><br />
					Le club s'engage à publier dans la presse les événements sportifs importants. Pour tout article personnel, un accord préalable devra être demandé à l'association. Toute publication sans accord pourra être sanctionnée.
				</p>
				<h3>Décharge</h3>
				<p>
					L'association décline toute responsabilité hors des horaires d'entrainements. Si vous souhaitez que votre enfant soit accompagné par une personne tierse, Veuillez en informer directement cette personne.
				</p>
				<h3>Accident</h3>
				<p>
					En cas d'accident, vous autorisez l'hospitalisation de votre ou vos enfants. Les pompiers seront appelé ainsi que le tuteur ou les responsables légaux.
				</p>
				
				<div class='authorization-validation text-center'>
					<input class='form-control' name='validInput' type='checkbox'>
					<label for='validInput'>En cochant cette case, j'accèpte les termes ci-dessus.</label>
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