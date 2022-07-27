<?php global $router; ?>

<header>
	<div class="container">
		<nav class="navbar navbar-expand-lg navbar-light">
			<a class="navbar-brand" href="#">
				<img src="view/img/logoMenu.jpg" width="30" height="30" class="d-inline-block align-top" alt="">
				Snake Cheer All Star
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse navbar-right" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href=<?php $router->Url("accueil"); ?>>Accueil</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href=<?php $router->Url("club"); ?>>Le Club</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href=<?php $router->Url("cours"); ?>>Les Cours</a>
					</li>
					<!--<li class="nav-item">
						<a class="nav-link" href=<?php $router->Url("galerie"); ?>>Galerie</a>
					</li>-->
					<li class="nav-item">
						<a class="nav-link" href=<?php $router->Url("contact"); ?>>Nous contacter</a>
					</li>
					<li class="nav-item">
						<a class="nav-link btn btn-snake" href=<?php $router->Url("inscription"); ?>>Inscription</a>
					</li>
				</ul>
			</div>
		</nav>
	</div>
</header>