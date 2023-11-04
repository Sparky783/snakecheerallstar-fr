<?php
namespace System;

// ==================================================================================================================
// ==== Objet principal du site internet. Permet de lancer le site et les outils nécessaire à son fonctionnement ====
// ==================================================================================================================

class Installer
{
	private $pages = array(); // Pages list of the website.
	private $default_page = ""; // Display page if the request page is null.
	private $website_path = ABSPATH; // Path to the website.
	private $admin_mode = false;
	private $includes = array();


	public function __construct($isAdmin = false) {
		if($isAdmin)
		{
			$this->admin_mode = true;
			$this->website_path = ABSPATH . "admin/";
		}
	
		$this->InitModules();
		$this->InitError();
		$this->InitOptions();
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
		// Check if the page is registrated
		if(!in_array($page, $this->pages))
			return false;

		$this->default_page = $page;

		global $router;
		$router->SetDefaultPage($this->default_page);

		return true;
	}

	// Add a new common include file for all pages.
	public function AddIncludes($include_link)
	{
		// Check if the include link exists.
		if(!file_exists($include_link))
			throw new Exception("The include link (" . $include_link . ") does not exist.");

		$this->includes[] = $include_link;
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
	
	// Init website options
	private function InitOptions()
	{
		$session = Session::getInstance();
		
		if(!isset($session->websiteOptions))
		{
			$options = new Options();
			$options->LoadFromDatabase();
			$session->websiteOptions = serialize($options);
		}	
	}

	// Load the good page with the page request and display it.
	private function LoadPage()
	{
		// Manage maintenance mode
		if(MAINTENANCE_MODE && !$this->admin_mode)
		{
			include_once(ABSPATH . "view/maintenance.php");
			return;
		}

		// Get information of the asked page.
		$page = $this->default_page;
		$gmm = new GetMethodManager();

		if($gmm->GetValue("page") !== false)
			$page = $gmm->GetValue("page");
	
		// Check if the page is registrated
		if(!in_array($page, $this->pages))
		{
			include_once(ABSPATH . "view/error.php");
			return;
		}
		
		// Display the page.
		if(file_exists($this->website_path . "view/" . $page . ".php"))
		{
			// == For administrators ==
			$session = Session::getInstance();
								
			if($this->admin_mode && $page != $this->default_page && !$session->admin_isConnected)
			{
				$gmm->setValue("page", $this->default_page);
				$this->LoadPage();
				return;
			}
			// ========================

			foreach ($this->includes as $include)
				include_once($include);

			if(file_exists($this->website_path . "controller/system/" . $page . ".php"))
				include_once($this->website_path . "controller/system/" . $page . ".php");

			include_once($this->website_path . "view/" . $page . ".php");
		}
		else
		{
			include_once(ABSPATH . "view/error.php");
		}
	}

	static public function Redirect($page, $isAdmin = false, $gmm = null)
	{
		global $router;
		header('Location: ' . $router->GetUrl($page, $gmm));
	}
}