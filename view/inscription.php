<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Site officiel de cheerleading en France et dans le Sud-Ouest.</title>
		<meta name="description" content="">
		
		<link href="https://fonts.googleapis.com/css?family=Poppins:700" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="view/css/inscription.css" />
		
		<?php echo $script; ?>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		<main class="container snake-body">
			<div class="col-sm-12">
				<div class="row">
					<?php echo $htmlStep; ?>
				</div>
			</div>
			<div class="row">
				<?php echo $html; ?>
			</div>
		</main>
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>