<?php
use System\Session;
use System\WebSite;
use System\RememberMe;
use System\Admin;
use System\AdminToken;

$cookieName = "rememberme-admin";
$errorHtml = "";

$session = Session::getInstance();

if($session->admin_isConnected)
{
	if(isset($_GET['logout']) && $_GET['logout'] == "true")
	{
		$session->admin_isConnected = false;

		// Supprimer le token de la BDD s'il existe.
		if($session->user_idToken > -1) {
			AdminToken::removeFromDatabase($session->admin_idToken);

			// On supprime le cookie de connexion
			RememberMe::removeCookie($cookieName);
		}

		$session->destroy();
	}
	else
	{
		WebSite::redirect("home", true);
	}
}
else
{
	// Vérifie si l'option Remember Me est active
	$cookie = isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName] : false;

	if ($cookie)
	{
		list ($idToken, $tokenKey, $mac) = explode('-', $cookie);
		
		// Vérification de la correspondant avec le mac.
        if (!hash_equals(hash_hmac('sha256', $idToken . '-' . $tokenKey, AUTH_SALT), $mac)) {
            return false;
		}
		
		$token = AdminToken::getById($idToken);
			
		if($token != null) // Remember Me option trouvé
		{
			if (hash_equals($token->getValue(), $tokenKey))
			{
				$admin = Admin::getById($token->getIdAdmin());
				saveAdminInSession($admin, $token->getId());
				WebSite::redirect("home", true);
			}
		}
    }

	// Sinon on tente de connecter la personne.
	if(isset($_POST['email']) && isset($_POST['password']))
	{
		$email = strip_tags($_POST['email']);
		$password = strip_tags($_POST['password']);
		
		if($email != "" && $password != "")
		{
			$password = sha1(sha1(AUTH_SALT) . sha1($password));
			$admin = Admin::Login($email, $password);
			
			if($admin != null) // Connexion réussi
			{
				$idToken = -1;

				// Ajoute le cookie si souhaité
				if(isset($_POST[$cookieName]) && $_POST[$cookieName] == "on")
				{
					$tokenKey = AdminToken::generateRandomToken();

					$token = new AdminToken();
					$token->setIdAdmin($admin->getId());
					$token->setValue($tokenKey);
					$token->saveToDatabase();

					$idToken = $token->getId();

					RememberMe::CreateCookie($cookieName, $token->getId(), $tokenKey);
				}

				saveAdminInSession($admin, $idToken);
				WebSite::redirect("home", true);
			}
			else  // Connexion échoué
			{
				$errorHtml = "L'E-mail ou le mot de passe est incorrect.";
			}
		}
		else
		{
			$errorHtml = "L'un des champs est vide.";
		}
	}
}

function saveAdminInSession(Admin $admin, int $idToken = -1): void
{
	$session = Session::getInstance();

	$session->admin_isConnected = true;
	$session->admin_id = $admin->getId();
	$session->admin_name = $admin->getName();
	$session->admin_email = $admin->getEmail();
	$session->admin_password = $admin->getPassword();
	$session->admin_roles = $admin->getRoles();
	$session->admin_idToken = $idToken;
}
?>