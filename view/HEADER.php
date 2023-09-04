<?php global $router; ?>

<header>
	<nav class="navbar navbar-expand-lg navbar-light">
		<div class="container">
			<a class="navbar-brand" href="#">
				<img src="view/img/logoMenu.jpg" width="30" height="30" class="d-inline-block align-top" alt="">
				Snake Cheer All Star
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div id="navbarSupportedContent" class="collapse navbar-collapse">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href=<?php $router->url('accueil'); ?>>Accueil</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href=<?php $router->url('club'); ?>>Le Club</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href=<?php $router->url('cours'); ?>>Les Cours</a>
					</li>
					<!--<li class="nav-item">
						<a class="nav-link" href=<?php $router->url('galerie'); ?>>Galerie</a>
					</li>-->
					<li class="nav-item">
						<a class="nav-link" href=<?php $router->url('contact'); ?>>Nous contacter</a>
					</li>
					<li class="nav-item">
						<a class="nav-link btn btn-snake" href=<?php $router->url('inscription'); ?>>Inscription</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>
</header>