<?php
/*
 * Gestionnaire de routage (ou de chemin) du site
 */

 include_once("GetMethodManager.php");

class Router
{
	private $admin_mode = false;
	
	
	public function __construct($isAdmin = false) {
		$this->admin_mode = $isAdmin;
		
		global $gmm;
		$gmm = new GetMethodManager();
	}


	// Fournis l'url de la page souhaité.
	public function GetCurrentPage() {
		global $gmm;
		return $gmm->GetValue("page");
	}

	// Fournis l'url de la page souhaité.
	public function GetUrl($page, $options = array()) {
		global $gmm;
		
		if(count($options) > 0)
		{
			foreach($options as $key => $value)
				$gmm->ModifyValue($key, $value);
		}
		else
			$gmm->Clear();
		
		$gmm->ModifyValue("page", $page);
			
		if($this->admin_mode)
			return URL . "/admin.php" . $gmm->GetString();
		else
			return URL . "/index.php" . $gmm->GetString();
	}

	// Fournis l'url de la page souhaité.
	public function Url($page, $options = array()) {
		echo $this->GetUrl($page, $options);
	}

	// Fournis l'url de la page souhaité.
	public function GetAPI($request = "") {
		if($this->admin_mode)
			return URL . "/admin-api.php?request=" . $request;
		
		return URL . "/api.php?request=" . $request;
	}
	
	public function API($request = "") {
		echo $this->GetAPI($request);
	}
}