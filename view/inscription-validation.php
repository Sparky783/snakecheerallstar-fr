<?php global $router; ?>

<section id="validation">
	<div id='inscriptionMenu' class='col-md-12 inscription-menu'>
		<h2>Confirmation de l'inscription</h2>
	</div>

	<div id='inscription' class='col-md-12'>
		<div id='confirmation'>
			<p class='text-center check'>
				<i class="fas fa-check-circle"></i>
			</p>
			<p id='confirmBox' class='alert alert-success text-center'>
				<span>Votre inscription à bien été prise en compte !</span>
				
			</p>
			<p class='text-center'>
				<b>Vous avez presque fini</b>
			</p>
			<p>
				Afin que les membres du bureau puissent valider votre inscription, veuillez fournir auprès d'un des coachs ou d'un membre du conseil les éléments qui vous ont été envoyés par mail.
				<br /><br />
				Si vous n'avez pas reçu d'E-mail récapitulatif, veuillez vérifier dans vos Spam. Sinon veulliez vous adresser à l'un des membres du bureau, ou un coach, pour avoir plus d'information.
			</p>
			<p class='alert alert-info'>
				<i class='fas fa-info-circle'></i>
				<span id="validationEmailMessage"></span>
			</p>
		</div>
	</div>

	<div id='nextButton' class='col-md-12 mb-4'>
		<div class='text-center'>
			<a id='validButton' class='btn btn-primary' href='" . $router->GetUrl("accueil") . "'>Retour à l'accueil</a>
		</div>
	</div>
</section>