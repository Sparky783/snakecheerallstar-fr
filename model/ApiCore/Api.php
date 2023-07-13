<?php
/*
include_once 'Route.php'
*/

namespace ApiCore;
use ApiCore\Route;

/**
 * API Maganer
 *
 * To add a new root:
 *     $app->Get("/sayname/{name}", function($args){
 *         Api::SendJSON("Hi " . $args['name'] . " !");
 *     });
 *
 * To use it, call the API like that:
 *     http://Url_API?request=sayname/Florent
 *     Return: {"Hi Florent !"}
 */
class Api
{
    private string $_requestType = "";
    private string $_requestString = "";
    private array $_routes = array();

    // == CONSTRUCTOR ==

    /**
     * Make an instance of API core.
     */
    public function __construct()
    {
        $this->_requestType = strtoupper($_SERVER['REQUEST_METHOD']);

        if (isset($_GET['request'])) {
            $requestString = $_GET['request'];
            $this->_requestString = "/" . str_replace("\\", "/", $requestString);
            
        } else {
            header("Access-Control-Allow-Origin: *");
            header('HTTP/1.0 404 Not Found');
            echo "HTTP/1.0 404 Not Found";
            exit;
        }
    }

    /**
     * Add a rule to the API with the method GET.
     *
     * @param string $route Route to call the API request. This route accept parameters {exemple}.
     * @param mixed $callback Function to execute when the request is asked.
     * @return void
     */
    public function get(string $route, mixed $callback): void
    {
        $this->_routes[] = new Route("GET", $route, $callback);
    }

    /**
     * Add a rule to the API with the method POST.
     *
     * @param string $route Route to call the API request. This route accept parameters {exemple}.
     * @param mixed $callback Function to execute when the request is asked.
     * @return void
     */
    public function post(string $route, mixed $callback): void
    {
        $this->_routes[] = new Route("POST", $route, $callback);
    }

    /**
     * Add a rule to the API with the method PUT.
     *
     * @param string $route Route to call the API request. This route accept parameters {exemple}.
     * @param mixed $callback Function to execute when the request is asked.
     * @return void
     */
    public function put(string $route, mixed $callback): void
    {
        $this->_routes[] = new Route("PUT", $route, $callback);
    }

    /**
     * Add a rule to the API with the method DELETE.
     *
     * @param string $route Route to call the API request. This route accept parameters {exemple}.
     * @param mixed $callback Function to execute when the request is asked.
     * @return void
     */
    public function delete(string $route, mixed $callback): void
    {
        $this->_routes[] = new Route("DELETE", $route, $callback);
    }

    /**
     * Run the API and execute the correct request.
     *
     * @return void
     */
    public function run(): void
    {
		if($this->_requestType != "OPTIONS") {
			$response = false;

			foreach ($this->_routes as $route) {
				if ($route->getMethod() == $this->_requestType && $route->match($this->_requestString)) {
					$route->execute($this->_requestString);
					$response = true;
					break;
				}
			}

			if(!$response)
				self::sendJSON("Error: The route does not exist.");
		} else {
            self::sendJSON("Error: OPTIONS request type is not allowed.");
        }
    }


    /**
     * Send a JSON response to the client.
     *
     * @param mixed $data Data to send.
     * @return void
     */
    public static function sendJSON(mixed $data): void
    {
        header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json");
        echo json_encode($data);
		exit;
    }
}
?>