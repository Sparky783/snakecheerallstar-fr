<?php
// ================================
// ==== Controleur Inscription ====
// ================================
// ==== Inscription terminé, récapitulatif des pièces à fournir ====

include_once(ABSPATH . "model/system/Session.php");
include_once(ABSPATH . "model/snake/SnakeTools.php");
include_once(ABSPATH . "model/snake/Inscription.php");
include_once(ABSPATH . "model/PHPMailer/src/PHPMailer.php");
include_once(ABSPATH . "model/PHPMailer/src/SMTP.php");
include_once(ABSPATH . "model/EmailTemplates.php");

global $router;

$session = Session::getInstance();
$payment = $session->inscription->GetPayment();

// ================================================
// E-mail récapitulatif
// ================================================
foreach($session->inscription->GetTuteurs() as $tuteur)
	SnakeTools::SendRecap($payment, $tuteur);

// ================================================
// E-mail facture
// ================================================
foreach($session->inscription->GetTuteurs() as $tuteur)
	SnakeTools::SendBill($payment, $tuteur);

// ================================================
// HTML Response
// ================================================
$script = "
	<script type='text/javascript' src='view/js/inscription_5.js'></script>
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
			<h2>Confirmation de l'inscription</h2>
		</div>
	</div>

	<div id='inscription' class='col-md-12'>
		<div id='confirmation'>
			<p id='confirmBox' class='alert alert-success text-center'>
				<i class='fas fa-thumbs-up'></i>
				<span>Votre inscription à bien été prise en compte !</span><br />
				Vous avez presque fini
			</p>
			<p>
				Afin que les membres du bureau puissent valider l'inscription, veuillez fournir auprès d'un des coachs ou d'un membre du conseil les éléments suivants:
			</p>
			" . $list . "
			<p class='alert alert-info'>
				<i class='fas fa-info-circle'></i>
				Un E-mail récapitulatif a été envoyé à l'ensemble des représentant légaux.
			</p>
		</div>
	</div>

	<div id='nextButton' class='col-md-12'>
		<div class='text-center'>
		<a id='validButton' class='btn btn-primary' href='" . $router->GetUrl("accueil") . "'>Retour à l'accueil</a>
		</div>
	</div>
";
?>