<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Site officiel de cheerleading en France et dans le Sud-Ouest.</title>
		<meta name="description" content="">
		
		<link href="https://fonts.googleapis.com/css?family=Poppins:700" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="view/css/accueil.css" />
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>

		<section id="welcome" class="container-fluid">
			<div class="bg">
				<video class="img-responsive" autoplay="" loop="" poster="/view/img/parallax-home.jpg">
					<source src="view/vids/PresentationHome.mp4" type="video/mp4">
				</video>
				<div class="bg-black home"></div>
			</div>
			<div class="front">
				<div class="welcomeTitle">
					<h1>Snake Cheer All Star</h1>
					<p>
						Club sportif de cheerleading sur Pau
					</p>
				</div>
			</div>
		</section>
		<div class="container-fluid">
			<section id="intro" class="row">
				<div class="container">
					<h1 class="text-center">Snake Cheer All Star équipe de cheerleading sur Pau<br />rejoignez-nous!</h1>
					<p>
						Tout jeune club de cheerleading, nous pratiquons un sport collectif trés enrichissant et varié mélant gymnastique, souplesse et force. Si tu n'as pas peur de relever le défis et si tu cherches un sport complet physiquement tout  en faisant partie d'une équipe soudée, rejoins-nous! Sport ouvert dès l'age de 6 ans.
					</p>
				</div>
			</section>
			<section id="presentation" class="row">
				<div class="container">
					<h2>Essai gratuit</h2>
					<p>
						Venez dès maintenant essayer le cheerleading dans notre club. Pour cela c'est très simple. Rendez-vous sur le formulaire d'inscription en cliquant ici:
						<br /><br />
						<a class="btn btn-snake" href=<?php $router->Url("inscription"); ?> title="Inscription chez les Snakes">Inscription</a>
						<br /><br />
						Si vous souhaitez avoir plus d'informations, n'hésitez pas à nous poser vos questions directement grâce à la page "Nous contacter" de notre site.
					</p>
				</div>
				</section>
			<section id="informations" class="row">
				<div class="container">
					<h2>Qu'est ce que c'est</h2>
					<p>
						Le cheerleading est un sport d'équipe originaire des Etats Unis. Il s'est fortement développé en France depuis une quizaine d'année. Cette discipline mixte est composée de pyramides humaines, de sauts, de portés ainsi que de tumbling (gym) et de danse. Le cheerleading permet de développer différents qualités physiques telles que la force, la vitesse et la souplesse, et contribue fortement à améliorer son savoir être. Esprit d'équipe, confiance, partage, entraide, voilà les maitres mots de ce sport.
					</p>
				</div>
				</section>
			<section id="partenariats" class="row">
				<div class="container">
					<h2>Suivez-nous</h2>
					<p>
						Retrouvez touts nos actualités, nos meilleurs moments, ainsi que tout nos événements sur la page Facebook officiel des Snake Cheer All Star. Vous pourrez également toute ces informations sur notre site en suivant les actualités présentes ici.
					</p>
				</div>
			</section>
		</div>
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>