<?php global $router; ?>
<section id="adherents" class="row">
	<div id='adherentsMenu' class='inscription-menu col-md-12'>
		<div class='inscription-menu-text'>
			<h2>Liste des adhérents à inscrire</h2>
			<span>Liste des adhérents d'une même famille</span>
		</div>
	</div>

	<div class='col-md-4 guide'>
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
					Pour inscrire plusieurs adhérents, veuillez cliquer sur le bouton <span class='adherentsAddBtn btn btn-primary'><i class='fas fa-user-plus'></i> Ajouter un adhérent</span> en haut à droite de la page.<br />
					Le tarif fratrie sera appliqué suivant le nombre d'adhérent inscrit sur cette page.
				</p>
				<p class='alert alert-danger'>
					<b>Note :</b> Tous les champs du formulaire doivent être remplis.
				</p>
			</div>
		</div>
	</div>

	<div class='col-md-8'>
		<div id='adherentsList'></div>
		<button class='adherentsAddBtn btn btn-primary' type='button'>
			<i class='fas fa-user-plus'></i> Ajouter un adhérent
		</button>
	</div>

	<div class='col-md-12 nextButton'>
		<div class='text-center'>
			<button class='btn btn-primary'>Valider et continuer</button>
		</div>
	</div>
</section>