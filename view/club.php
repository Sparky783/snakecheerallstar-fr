<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Site officiel de cheerleading en France et dans le Sud-Ouest.</title>
		<meta name="description" content="">
		
		<link href="https://fonts.googleapis.com/css?family=Poppins:700" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="view/css/club.css" />
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		
		<main class="container snake-body">
			<section id="presentation">
				<h2>Qui sommes nous ?</h2>
				<p>
					Créé en 2014, nous essayons de développer et de promouvoir le cheerleading qui est encore un sport mal connu en France. Souvent dénigré et associé à l'image de la "pom pom girl", nous essayons de faire évoluer les mentaliés. Ce sport requiert énormément de capacités physiques et permet de développerson esprit d'équipe. Nous espérons faire évoluer notre club grâce à vous et votre motivation tout en respectant la sécurité et la philosophie de notre sport. Voici la présentation de nos dirigeants et coachs, car un club ne serais rien sans tous ces bénévoles qui donnent de leur temps et de la passion pour faire évoluer le cheerleading.
				</p>
			</section>
			
			<section id="coach">
				<h2>Nos coachs</h2>
				<p>
					Nos coachs tous diplomés de la FFFA (Fédération Française de Football Américain), et titulaires de formation de secours sont là pour vous aider, vous guider et vous faire prendre plaisir lors de chaque entrainement.
				</p>
				<div class="row people">
					<div class="col-sm-4">
						<div class="card">
							<img class="card-img-top" src="view/img/aurelie_guerin.jpg" alt="...">
							<div class="card-body">
								<h5 class="card-title">Aurélie Guérin</h5>
								<p class="card-text">Coach de la section des grande</p>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<img class="card-img-top" src="view/img/celine_denhaene.jpg" alt="...">
							<div class="card-body">
								<h5 class="card-title">Céline Denhaene (Tabs)</h5>
								<p class="card-text">Coach de la section des grandes et petites</p>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<img class="card-img-top" src="view/img/clea.jpg" alt="...">
							<div class="card-body">
								<h5 class="card-title">Cléa Castéres</h5>
								<p class="card-text">Coach de la section des petites</p>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<img class="card-img-top" src="view/img/julie_darbary.jpg" alt="...">
							<div class="card-body">
								<h5 class="card-title">Julie Darbary</h5>
								<p class="card-text">Coach de la section des petites</p>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<img class="card-img-top" src="view/img/laure_dufau.jpg" alt="...">
							<div class="card-body">
								<h5 class="card-title">Laure Dufau</h5>
								<p class="card-text">Coach de la section des petites</p>
							</div>
						</div>
					</div>
				</div>
			</section>
			
			<section id="bureau">
				<h2>Notre bureau</h2>
				<p>
					La gestion et l'administration d'un club demande énormément de temps. Tout club nécéssite un bureau et des membres permettant ainsi de scinder la masse de travail entre tous et de partager nos différents points de vue. Voici les principaux dirigeants qui permettent à ce club de vivre, aidés dans leur fonction par 7 membres du conseil d'administration.
				</p>
				<div class="row people">
					<div class="col-sm-4">
						<div class="card">
							<img class="card-img-top" src="view/img/rachid_benabdeljalil.jpg" alt="...">
							<div class="card-body">
								<h5 class="card-title">Rachid Benabdeljalil</h5>
								<p class="card-text">
									Président
								</p>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<img class="card-img-top" src="view/img/florent_lavignotte.jpg" alt="...">
							<div class="card-body">
								<h5 class="card-title">Florent Lavignotte</h5>
								<p class="card-text">
									Trésorier
								</p>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<img class="card-img-top" src="view/img/aurelie_guerin.jpg" alt="...">
							<div class="card-body">
								<h5 class="card-title">Aurélie Guérin</h5>
								<p class="card-text">
									Secrétaire
								</p>
							</div>
						</div>
					</div>
				</div>
			</section>
		</main>
		
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>