<?php global $router; ?>

<footer class="container">
	<div class="row">
		<ul class="breadcrumb">
			<li class="breadcrumb-item"><a href=<?php $router->Url("accueil"); ?> title="Présentation de mon institut à domicile ainsi que ma biographie.">Accueil</a></li>
			<li class="breadcrumb-item"><a href=<?php $router->Url("gallery"); ?> title="Retrouvez l ensemble des soins et services que je vous propose.">Galerie</a></li>
			<li class="breadcrumb-item"><a href=<?php $router->Url("contact"); ?> title="Prenez rendez-vous en me contactant par téléphone ou par message.">Contact</a></li>
			<li class="breadcrumb-item"><a href=<?php $router->Url("cgu"); ?> title="Toute les mentions légales de ma société.">Mentions légales</a></li>
		</ul>
		<p id="footer_copyright">
			Copyright &copy; <?php echo date("Y"); ?> <?php echo TITLE; ?>. Tous droits réservés - Développeur et Webmaster: Florent LAVIGNOTTE
		</p>
	</div>
</footer>