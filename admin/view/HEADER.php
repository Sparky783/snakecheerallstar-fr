<?php
use System\ToolBox;

global $router;
?>

<header>
	<nav class="navbar navbar-expand-lg navbar-dark bg-snake">
		<a class="navbar-brand" href=<?php echo URL; ?>>Snake Admin (<span id="headerSaison"><?php echo $session->selectedSaison; ?></span>)</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div id="navbarMenu" class="collapse navbar-collapse navbar-right">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item active">
					<a class="nav-link" href=<?php $router->Url("home"); ?>>Accueil</a>
				</li>
				
				<?php if(ToolBox::SearchInArray($session->admin_roles, array("admin"))) { ?>
					<li class="nav-item">
						<a class="nav-link" href=<?php $router->Url("admins"); ?>>Administrateurs</a>
					</li>
				<?php } ?>
				
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Profil
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
						<a class="dropdown-item" href=<?php $router->Url("profil"); ?>>Profil</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href=<?php $router->Url("login", array("logout" => "true")); ?>>Se déconnecter</a>
					</div>
				</li>
			</ul>
		</div>
	</nav>
</header>