<?php
/*
 * A route manager for the Api.
 */


class Route
{
	private $method;
	private $route;
	private $callback;


	public function __construct($method, $route, $callback){
		$this->method = $method;
		$this->route = $route;
		$this->callback = $callback;
	}


	// Return the method of the route.
	public function GetMethod() {
		return $this->method;
	}
	
	// Check if this route match with a schema.
	public function Match($route) {
		$regex = "#^" . preg_replace("/\{[a-zA-Z0-9_-]+\}/", ".+", $this->route) . "$#";

		if (preg_match($regex, $route))
			return true;
		return false;
	}

	// Execute the callback function.
	public function Execute($route) {
		$callback = $this->callback;
		
		switch ($this->method) {
            case "GET":
                $callback($this->MakeArgs($route));
                break;
            case "POST":
                $callback($_POST);
                break;
            case "PUT":
                $callback($_PUT);
                break;
            case "DELETE":
                $callback($this->MakeArgs($route));
                break;
        }
	}

	// Make the Args table from the route.
	private function MakeArgs($route) {
		$values = explode("/", $route);
		$keys = explode("/", $this->route);
		$args = array();

		$tot = count($keys);
		
		for ($i=0; $i < $tot; $i++) { 
			if(preg_match("/\{[a-zA-Z0-9_-]+\}/", $keys[$i]))
				$args[str_replace(array('{', '}'), '', $keys[$i])] = $values[$i];
		}

		return $args;
	}
}
?>