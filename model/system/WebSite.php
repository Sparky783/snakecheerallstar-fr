<?php
require_once(ABSPATH . "model/system/LogManager.php");
require_once(ABSPATH . "model/system/GetMethodManager.php");
require_once(ABSPATH . "model/system/Router.php");
require_once(ABSPATH . "model/system/Session.php");


class WebSite
{
	private $pages = array(); // Pages list of the website.
	private $default_page = ""; // Display page if the request page is null.
	private $website_path = ABSPATH; // Path to the website.
	private $admin_mode = false;


	public function __construct($isAdmin = false)
	{
		if($isAdmin)
		{
			$this->admin_mode = true;
			$this->website_path = ABSPATH . "admin/";
		}
	
		$this->InitModules();
		$this->InitError();
	}


	// =========================
	// ==== PUBLIC METHODS =====
	// =========================

	// Set the pages list of the website
	public function SetPages($pages)
	{
		if(is_array($pages))
		{
			$this->pages = $pages;
			return true;
		}

		return false;
	}

	// Set the default page dislayed if the resquest page is empty.
	public function DefaultPage($page)
	{
		$this->default_page = $page;
	}

	// Lunch the web site and send the good page.
	public function Run()
	{
		$this->LoadPage();
	}


	// ==========================
	// ==== PRIVATE METHODS =====
	// ==========================
	private function InitModules()
	{
		global $router;
		$router = new Router($this->admin_mode);
	}

	// Init error display for the developpment.
	private function InitError()
	{
		if(ENV == "PROD")
		{
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
		}
		else
		{
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		}
	}

	// Load the good page with the page request and display it.
	private function LoadPage()
	{
		if(!MAINTENANCE_MODE || $this->admin_mode)
		{
			// Get information of the asked page.
			$page = $this->default_page;
			
			global $gmm;
			if($gmm->GetValue("page") !== false)
				$page = $gmm->GetValue("page");
			
			// Display the page.
			if(in_array($page, $this->pages))
			{
				if(file_exists($this->website_path . "view/" . $page . ".php"))
				{
					// == For administrators ==
					$session = Session::getInstance();
										
					if($this->admin_mode && $page != $this->default_page && !$session->isConnected)
					{
						$gmm->ModifyValue("page", $this->default_page);
						$this->LoadPage();
						return;
					}
					// ========================

					if(file_exists($this->website_path . "controller/system/" . $page . ".php"))
						include_once($this->website_path . "controller/system/" . $page . ".php");

					include_once($this->website_path . "view/" . $page . ".php");
				}
				else
				{
					include_once(ABSPATH . "view/error.php");
				}	
			}
			else
			{
				include_once(ABSPATH . "view/error.php");
			}
		}
		else
		{
			include_once(ABSPATH . "view/maintenance.php");
		}
	}

	static public function Redirect($page, $isAdmin = false)
	{
		//$router = new Router($isAdmin);
		global $router;
		header('Location: ' . $router->GetUrl($page));
	}
}