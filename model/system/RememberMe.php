
<?php
// =========================================================================
// ==== Boite à outils du site. Fonctions statique a utiliser au besoin ====
// =========================================================================

include_once("Session.php");

class RememberMe
{
	// Génère un token de connexion automatique
	static public function GenerateRandomToken()
	{
		return hash("sha512", random_bytes(256));
	}

	// Crée le cookie
	static public function CreateCookie($cookieName, $id_user, $token)
	{
		$cookie = $id_user . '-' . $token;
		$mac = hash_hmac('sha256', $cookie, AUTH_SALT);
		$cookie .= '-' . $mac;

		return setcookie(
			$cookieName,
			$cookie,
			time() + 365*24*3600,
			"",
			DOMAIN,
			true,
			true
		);
	}

	// Génère un mot de passe
	static public function RemoveCookie($cookieName)
	{
		if (isset($_COOKIE['']))
		{
			unset($_COOKIE[$cookieName]); 
			setcookie($cookieName, null, -1, '/'); 
		}
	}
}
?>