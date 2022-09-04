<?php
include_once(ABSPATH . "model/system/Database.php");
include_once(ABSPATH . "model/system/RememberMe.php");
include_once(ABSPATH . "model/system/AdminToken.php");
include_once(ABSPATH . "model/system/Admin.php");

$cookieName = "rememberme-admin";
$errorHtml = "";

$session = Session::GetInstance();

if($session->admin_isConnected)
{
	if(isset($_GET['logout']) && $_GET['logout'] == "true")
	{
		$session->admin_isConnected = false;

		// On supprime le cookie de connexion
		RememberMe::RemoveCookie($cookieName);

		$admin = Admin::GetById($session->admin_id);
		$admin->SaveToDatabase();
	}
	else
	{
		WebSite::Redirect("home", true);
	}
}
else
{
	// Vérifie si l'option Remember Me est active
	$cookie = isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName] : false;

	if ($cookie)
	{
		list ($id_admin, $token, $mac) = explode('-', $cookie);
		
		// Vérification de la correspondant avec le mac.
        if (!hash_equals(hash_hmac('sha256', $id_admin . '-' . $token, AUTH_SALT), $mac))
            return false;
		
			$token = AdminToken::GetById($id_token);
			
		if($admin != null) // Remember Me opotion trouvé
		{
			if (hash_equals($admin->GetToken(), $tokenKey))
			{
				$admin = Admin::GetById($token->GetIdAdmin());
				SaveAdminInSession($admin);
				WebSite::Redirect("home", true);
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
				SaveAdminInSession($admin);

				// Ajoute le cookie si souhaité
				if(isset($_POST[$cookieName]) && $_POST[$cookieName] == "on")
				{
					$tokenKey = AdminToken::GenerateRandomToken() . sha1("flyarts-admin");

					$token = new AdminToken();
					$token->SetIdAdmin($admin->GetId());
					$token->SetToken($tokenKey);
					$token->SaveToDatabase();

					RememberMe::CreateCookie($cookieName, $token->GetId(), $tokenKey);
				}
				
				global $gmm;
				$gmm->RemoveValue("logout");
				
				WebSite::Redirect("home", true);
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

function SaveAdminInSession($admin)
{
	$session = Session::GetInstance();

	$session->admin_isConnected = true;
	$session->admin_id = $admin->GetId();
	$session->admin_name = $admin->GetName();
	$session->admin_email = $admin->GetEmail();
	$session->admin_password = $admin->GetPassword();
	$session->admin_roles = $admin->GetRoles();
}
?>