<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Site officiel de cheerleading en France et dans le Sud-Ouest.</title>
		<meta name="description" content="">
		
		<link href="https://fonts.googleapis.com/css?family=Poppins:700" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="view/css/cours.css" />
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		
		<main class="container snake-body">
			<section id="introduction">
				<p>
					Retrouvez dans cette section, tous les renseignements concernant les heures des cours, les équipes concernées ainsi que les lieux ou ils sont effectués.
				</p>
			</section>
			
			<section id="horaires" class="row">
				<div class="col-sm-12">
					<h2>Horaires</h2>
				</div>
				<?= $htmlHoraires ?>
			</section>
			
			<section id="lieux">
				<h2>Lieu</h2>
				<p>
					Gymnase à la MJC des Fleurs, 19 Bis Avenue de Buros 64000 Pau
				</p>
				<div class="map">
					<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2903.288893643883!2d-0.35573318427462286!3d43.30821148268764!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd5648eae1e7b48b%3A0xe74d30d11435f612!2s19+Avenue+de+Buros%2C+64000+Pau!5e0!3m2!1sfr!2sfr!4v1566290411859!5m2!1sfr!2sfr" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
				</div>
			</section>
			
			<section id="inscription">
				<h2>Inscrivez-vous sans plus attendre !</h2>
				<div id="prices">
					<table>
						<thead>
							<tr>
								<?= $htmlPricesHeader ?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<?= $htmlPricesBody ?>
							</tr>
						</tbody>
					</table>
					<p>Des tarifs pour les fratries sont pratiqués. Renseignez-vous auprès de notre bureau.</p>
				</div>
				<p class="alert alert-info">
					Les tarifs ci-dessus n'incluent pas le prix de location de la tenue de <b>35€</b> pour l'année ainsi qu'un chèque de caution de <b>150€</b>.
				</p>
				<p>
					Pour rejoindre l'équipe, cliquez ici
				</p>
				<p>
					<a class="btn btn-snake" href=<?php $router->Url("inscription"); ?> title="">Formulaire d'inscription</a>
				</p>
			</section>
		</main>
		
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>