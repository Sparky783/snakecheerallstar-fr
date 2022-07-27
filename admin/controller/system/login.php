<?php
include_once(ABSPATH . "model/system/Database.php");
include_once(ABSPATH . "model/system/ToolBox.php");
include_once(ABSPATH . "model/system/RememberMe.php");
include_once(ABSPATH . "model/system/Admin.php");

$cookieName = "rememberme-admin";
$errorHtml = "";

$session = Session::GetInstance();

if($session->isConnected)
{
	if(isset($_GET['logout']) && $_GET['logout'] == "true")
	{
		$session->isConnected = false;

		// On supprime le cookie de connexion
		RememberMe::RemoveCookie($cookieName);

		$user = Admin::GetById($session->id_user);
		$user->SetToken("");
		$user->SaveToDatabase();
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
		list ($id_user, $token, $mac) = explode('-', $cookie);
		
        if (!hash_equals(hash_hmac('sha256', $id_user . '-' . $token, AUTH_SALT), $mac))
            return false;
		
		$user = Admin::GetById($id_user);
			
		if($user != null) // Remember Me opotion trouvé
		{
			if (hash_equals($user->GetToken(), $token))
			{
				SaveUserInSession($user);
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
			$user = Admin::Login($email, $password);
			
			if($user != null) // Connexion réussi
			{
				SaveUserInSession($user);

				// Ajoute le cookie si souhaité
				if(isset($_POST[$cookieName]) && $_POST[$cookieName] == "on")
				{
					$token = ToolBox::GenerateRandomToken(); // generate a token, should be 128 - 256 bit

					$user->SetToken($token);
					$user->SaveToDatabase();

					RememberMe::CreateCookie($cookieName, $user->GetId(), $token);
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

function SaveUserInSession($user)
{
	$session = Session::GetInstance();

	$session->isConnected = true;
	$session->id_user = $user->GetId();
	$session->name = $user->GetName();
	$session->email = $user->GetEmail();
	$session->password = $user->GetPassword();
	$session->roles = $user->GetRoles();
}
?>