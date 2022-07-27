<?php
/*
 * Object with some functions to help in the api.
 */

class Tools
{
	static public function Error404() {
		header("Access-Control-Allow-Origin: *");
		header('HTTP/1.0 404 Not Found');
		echo "HTTP/1.0 404 Not Found";
		exit;
	}

	static public function Success200() {
		header("Access-Control-Allow-Origin: *");
		header('HTTP/1.0 200 OK');
		echo "HTTP/1.0 200 OK";
		exit;
	}
}
?>