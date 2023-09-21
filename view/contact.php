<?php global $router; ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("HEAD.php"); ?>
		<title><?php echo TITLE; ?> - Site officiel de cheerleading en France et dans le Sud-Ouest.</title>
		<meta name="description" content="">
		
		<link href="https://fonts.googleapis.com/css?family=Poppins:700" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="view/css/contact.css" />

		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<script type="text/javascript" src="view/js/form.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#contactSend").hide();
				$("#contactResponse").hide();

				$("#contactForm form").SpForm(
					function(){
						$("#contactForm").hide();
						$("#contactSend").show();
					},
					function(data){
						if (data.error) {
							console.log(data);
						}
						
						$("#contactResponse").html('<p class="text-center">' + data.message + '</p>');
						$("#contactSend").hide();
						$("#contactResponse").show();
					}
				);
			});

			function sendFormCallback() {
				$("#contactForm form").submit();
			}
		</script>
	</head>
	<body>
		<?php include_once("HEADER.php"); ?>
		
		<main class="container snake-body">
			<div id="block" class="row">
				<section class="col-sm-6">
					<div>
						<h2>Information</h2>
						<p>Si vous désirez nous contacter pour de plus amples informations concernant notre club ou pour diverses manifestations, n’hésitez pas. Nous nous ferons un plaisir de vous répondre dans les plus brefs délais.</p>
						<address>
							<strong>Snake Cheer All Star</strong><br />
							19 bis Avenue de Buros<br />
							64000 Pau
						</address>
						<p>
							<a title="Envoyer un Email directement avec votre boite" href="mailto:<?php echo EMAIL_CONTACT; ?>"><?php echo EMAIL_CONTACT; ?></a>
						</p>
					</div>
					<div>
						<h2>Nos partenaires</h2>
						<p>Le cheerleading est un sport géré par la fédération françaide de football américain (FFFA). Nous sommes affiliées à cette fédération et si vous désirez des informations concernant les règlements, les championnats et les autres sports de la fédération, vous pouvez aller visiter leur site internet en cliquant sur le lien.</p>
						<p>Nous avons aussi intégré la MJC des Fleurs de Pau. Vous pouvez également consulter leur site internet en cliquant sur leur lien. La MJC des Fleurs propose divers sports et activités qui pourront sûrement vous satisfaire.</p>
					</div>
				</section>

				<section class="col-sm-6">
					<div id="contactResponse" class="ap-form-response">
					</div>

					<div id="contactForm">
						<form class="ap-form" action=<?php $router->API("contact"); ?> method="post">
							<div class="form-group">
								<label for="inputName"><span class="ap-form-star">*</span>Nom et prénom :</label>
								<input id="inputName" class="form-control" type="name" name="name" aria-describedby="nameHelp" placeholder="Entrez votre nom et votre prénom">
							</div>
							<div class="form-group">
								<label for="inputEmail"><span class="ap-form-star">*</span>E-mail :</label>
								<input id="inputEmail" class="form-control" type="email" name="email" aria-describedby="emailHelp" placeholder="Entrez votre adresse E-mail">
							</div>
							<div class="form-group">
								<label for="inputMessage"><span class="ap-form-star">*</span>Message :</label>
								<textarea id="inputMessage" class="form-control" type="message" name="message" rows="5"></textarea>
							</div>
							<small class="form-text text-muted"><span class="ap-form-star">*</span> Ces champs sont obligatoires.</small>

							<?php if(ENV === 'DEV') { ?>
								<button class="btn btn-snake" type="submit">Envoyer</button>
							<?php } else { ?>
								<button class="btn btn-snake g-recaptcha" type="submit" data-sitekey="6LeNSLcUAAAAAM7PeKkzChmiNANlzTdK6HxY7JP9" data-callback="sendFormCallback">Envoyer</button>
							<?php } ?>
						</form>
					</div>

					<div id="contactSend" class="ap-form-send">
						<p class="text-center">
							Envoi en cours ...<br />
							Veuillez patienter
							<br /><br />
							<i class="fas fa-spinner fa-spin"></i>
						</p>
					</div>
				</section>
				<section id="partenaireList" class="col-sm-12">
					<p>
						<img src="view/img/sponsors/credit_agricole.png" alt="credit_agricole">
						<img src="view/img/sponsors/fffa.png" alt="fffa">
						<img src="view/img/sponsors/mjc.png" alt="mjc">
					</p>
				</section>
			</div>
		</main>
		
		<?php include_once("FOOTER.php"); ?>
	</body>
</html>