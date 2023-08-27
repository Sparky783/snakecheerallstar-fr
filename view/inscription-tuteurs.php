<?php global $router; ?>

<section id="tuteurs" class="row">
	<div id="tuteursMenu" class="col-md-12">
		<div>
			<h2>Liste des représentants légals</h2>
			<span>Liste des tuteurs, tuteurs, ou adhérents majeurs.</span>
		</div>
	</div>
	
	<div class="col-md-4 guide">
		<div class="card card-snake">
			<div class="card-header">
				<h4>Guide d"aide</h4>
			</div>
			<div class="card-body">
				<p class="title">Seconde étape</p>
				<p>
					Le seconde étape est d'indiquer le ou les représentants légaux des adhérents inscrit précédement.<br />
					Veuillez remplir le formulaire si contre correspondant à chaque tuteur.
				</p>
				<p class="alert alert-info">
					<b>Note :</b> Si vous êtes <b>majeur</b> ou si vous souhaitez simplement recevoir les E-mail d"informations, veuillez vous ajouter ici.
				</p>
				<p class="alert alert-info">
					Pour inscrire plusieurs représentant, veuillez cliquer sur le bouton <span class="tuteursAddBtn btn btn-primary"><i class="fas fa-user-plus"></i> Ajouter un resprésentant légal</span> en haut à droite de la page.	
				</p>
				<p class="alert alert-danger">
					<b>Note :</b> Tous les champs du formulaire doivent être remplis.
				</p>
			</div>
		</div>
	</div>

	<div class="col-md-8">
		<div id="tuteursList"></div>
		<button class="tuteursAddBtn btn btn-primary" type="button">
			<i class="fas fa-user-plus"></i> Ajouter un resprésentant légal
		</button>
	</div>

	<div class="col-md-12 nextButton">
		<div class="text-center">
			<button class="btn btn-primary">Valider et continuer</button>
		</div>
	</div>
</section>