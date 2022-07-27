<?php
/*
    API Maganer
	Developped by Florent Lavignotte
	
	To add a new root: Exemple :
	
	$app->Get("/sayname/{name}", function($args){
		Api::SendJSON("Hi " . $args['name'] . " !");
	});
	
	To use it, call the API like that : http://Url_API?request=sayname/Florent
	Return: {"Hi Florent !"}
*/


include_once 'Tools.php';
include_once 'Route.php';


class Api
{
    private $request_type;
    private $request_string;
    private $routes;


    public function __construct() {
        $this->routes = array();
        $this->request_type = strtoupper($_SERVER['REQUEST_METHOD']);

        if (isset($_GET['request']))
        {
            $request_string = $_GET['request'];
            $this->request_string = "/" . str_replace("\\", "/", $request_string);
        } else
            Tools::Error404();
    }


    // Add a rule to the Application with the method GET
    public function Get($route, $callback) {
        $this->routes[] = new route("GET", $route, $callback);
    }
	
	// Add a rule to the Application with the method POST
    public function Post($route, $callback) {
        $this->routes[] = new route("POST", $route, $callback);
    }
	
	// Add a rule to the Application with the method PUT
    public function Put($route, $callback) {
        $this->routes[] = new route("PUT", $route, $callback);
    }
	
	// Add a rule to the Application with the method DELETE
    public function Delete($route, $callback) {
        $this->routes[] = new route("DELETE", $route, $callback);
    }

    // Run the Application
    public function Run() {
		if($this->request_type != "OPTIONS") {
			$response = false;

			foreach ($this->routes as $route) {
				if ($route->GetMethod() == $this->request_type && $route->Match($this->request_string)) {
					$route->Execute($this->request_string);
					$response = true;
					break;
				}
			}

			if(!$response)
				self::SendJSON("Error: The route does not exist.");
		} else
			self::SendJSON("Error: The method request by your application is of the OPTIONS type.");
    }


    // Send a JSON response to the client.
    static public function SendJSON($data) {
        header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json");
        echo json_encode($data);
		exit;
    }

    // Send a JSON response to the client.
    static public function SendHTML($html) {
        header("Access-Control-Allow-Origin: *");
        echo $html;
		exit;
    }
}
?>