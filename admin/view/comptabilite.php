<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/comptabilite.css" />
		<script type="text/javascript" src="admin/view/js/comptabilite.js"></script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		<div class="container">
			<div class="row">
				<section class="col-md-12">
					<div id="mainTitle">
						<h1>Comptabilité</h1>
						<a class="btn btn-primary" href=<?= $router->url('home') ?>>
							Retour
						</a>
					</div>
					<div class="card">
						<table id="comptabilite" class="table">
							<thead class="thead-dark">
							<tr>
								<th scope="col">Méthode</th>
								<th scope="col">Nombre</th>
								<th scope="col">Montant</th>
							</tr>
							</thead>
							<tbody>
								<?php echo $paymentHtml; ?>
							</tbody>
						</table>
					</div>
				</section>
			</div>
		</div>
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>