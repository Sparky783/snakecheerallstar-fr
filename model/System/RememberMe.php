<?php
namespace System;
use Exception;

/**
 * This class give some tools to manage cookies for Keep Login (Remember Me).
 */
class RememberMe
{
    /**
     * Generate a new login token.
     *
     * @return string Token generated.
     * @throws Exception
     */
	public static function generateRandomToken(): string
	{
		return hash('sha512', random_bytes(256));
	}

    /**
     * Make a cookie for the Remember Me.
     *
     * @param string $cookieName Name of the cookie.
     * @param int $id_token Token ID in database.
     * @param string $token Token string store in cookie and database.
     * @return bool Return True if the cookie is correctly store, else False.
     */
	public static function createCookie(string $cookieName, int $id_token, string $token): bool
	{
		$cookie = $id_token . '-' . $token;
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

    /**
     * Remove the cookie from user's browser.
     *
     * @param string $cookieName Name of the cookie to remove.
     * @return void
     */
	public static function removeCookie(string $cookieName): void
	{
		if (isset($_COOKIE[''])) {
			unset($_COOKIE[$cookieName]); 
			setcookie($cookieName, null, -1, '/'); 
		}
	}
}
?>