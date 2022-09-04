<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Administration</title>
		
		<link rel="stylesheet" type="text/css" href="admin/view/css/login.css" />
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div id="title" class="col-md-12">
					<h1 class="text-center">Snake Cheer All Star</h1>
				</div>
				<section id="loginForm" class="col-md-4 offset-md-4">
					<div class="card border-snake">
						<div class="card-header bg-snake text-white text-center">
							Espace administrateur
						</div>
						<form class="card-body" action="#" method="post">
							<?php echo $errorHtml; ?>
							<div class="form-group">
								<label for="email">E-mail</label>
								<input class="form-control" type="email" name="email" placeholder="Enter email">
							</div>
							<div class="form-group">
								<label for="password">Password</label>
								<input class="form-control" type="password" name="password" placeholder="Password">
							</div>
							<div class="form-group form-check">
								<input class="form-check-input" type="checkbox" name="rememberme-admin" checked="checked">
								<label class="form-check-label" for="rememberme-admin">Se souvenir de moi</label>
							</div>
							<button type="submit" class="btn btn-snake">Se connecter</button>
						</form>
					</div>
				</section>
			</div>
		</div>
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>