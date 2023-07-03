<?php
namespace ApiCore;

use ElodieEsthetique\News;
use ErrorException;

/**
 * A route manager for the API.
 */
class Route
{
	private string $_method = "";
	private string $_route = "";
	private mixed $_callback = null;

    /**
     * Make a new instance of Route.
     *
     * @param $method Method used for this route.
     * @param $route Route URL to call this route.
     * @param $callback Function to call when this route is executed.
     */
	public function __construct($method, $route, $callback){
		$this->_method = $method;
		$this->_route = $route;
		$this->_callback = $callback;
	}


    /**
     * Return the method of the route.
     *
     * @return string
     */
	public function getMethod(): string
    {
		return $this->_method;
	}

    /**
     * Check if this route match with a schema.
     *
     * @param string $request Request received by the API.
     * @return bool Return True if this route match with the schema, else False.
     */
	public function match(string $request): bool
    {
		$regex = "#^" . preg_replace("/\{[a-zA-Z]+\}/", ".+", $this->_route) . "$#";

		if (preg_match($regex, $request))
			return true;

		return false;
	}

    /**
     * Execute the callback function.
     *
     * @param string $request Request received by the API.
     * @return void
     */
	public function execute(string $request): void
    {
        if($this->_callback === null) {
            throw new ErrorException('The callback function must be initialized before execute API request.');
        }

		switch ($this->_method) {
            case "GET":
                call_user_func($this->_callback, $this->makeArgs($request));
                break;

            case "POST":
                call_user_func($this->_callback, $_POST);
                break;

            case "PUT":
                call_user_func($this->_callback, $_PUT);
                break;

            case "DELETE":
                call_user_func($this->_callback, $this->makeArgs($request));
                break;
        }
	}

    /**
     * Make the Args table from the request.
     *
     * @param $request
     * @return array
     */
	private function makeArgs($request): array
    {
		$values = explode("/", $request);
		$keys = explode("/", $this->_route);
		$args = array();

		$tot = count($keys);
		
		for ($i = 0; $i < $tot; $i++) {
			if(preg_match("/\{[a-zA-Z]+\}/", $keys[$i]))
				$args[str_replace(array('{', '}'), '', $keys[$i])] = $values[$i];
		}

		return $args;
	}
}
?>