<?php
namespace System;
use Exception;
use System\Session;
use System\Option;
use System\Router;
use System\GetMethodManager;

// ======================
// ==== Website core ====
// ======================

/**
 * It is used to create main website logic and load good file depending of the demands.
 */
class WebSite
{
	// ==== ATTRIBUTS ====
	/**
	 * @var array $_pages Pages list of the website.
	 */
	private array $_pages = [];
	
	/**
	 * @var string $_defaultPage Default page if the request page is null.
	 */
	private string $_defaultPage = '';
	
	/**
	 * @var string $_websitePath Path to the website.
	 */
	private string $_websitePath = '';
	
	/**
	 * @var bool $_adminMode Say if the website is in admin mode.
	 */
	private bool $_adminMode = false;
	
	/**
	 * @var array $_includes File list to include during loading function.
	 */
	private array $_includes = [];


	/**
	 * Initialize a new website kernel.
	 * 
	 * @param string $path Define where the website files are placed (controllers, api, views).
	 * @param bool $isAdmin Say if the kernel is used for administration pages.
	 */
	public function __construct(string $path, bool $isAdmin = false)
	{
		$this->_websitePath = $path;

		if(substr($this->_websitePath, -1) !== '/') {
			$this->_websitePath .= '/';
		}

		if ($isAdmin) {
			$this->_adminMode = true;
		}

		$this->initModules();
		$this->initOptions();
	}


	// =========================
	// ==== PUBLIC METHODS =====
	// =========================

	/**
	 * Set the pages list of the website.
	 * 
	 * @param array $pages List of pages that can be displayed by the website.
	 * @return bool Return True if the list has been added, else False.
	 */
	public function setPages(array $pages): bool
	{
		if (is_array($pages)) {
			$this->_pages = $pages;
			return true;
		}

		return false;
	}

	/**
	 * Set the default page dislayed if the resquest page is empty.
	 * This function must be called after setPages(). 
	 * 
	 * @param string $page First page that must be display if no one else are selected.
	 * @return bool Return True if the default page has been added, else False.
	 */
	public function defaultPage(string $page): bool
	{
		// Check if the page is registrated
		if (!in_array($page, $this->_pages)) {
			return false;
		}

		$this->_defaultPage = $page;

		global $router;
		$router->setDefaultPage($this->_defaultPage);

		return true;
	}

	/**
	 * Add common includes for all pages.
	 * 
	 * @param string $includePath Path of the file to include.
	 * @return void
	 */
	public function addIncludes(string $includePath): void
	{
		// Check if the include link exists.
		if (!file_exists($includePath)) {
			throw new Exception('The include link (' . $includePath . ') does not exist.');
		}

		$this->_includes[] = $includePath;
	}

	/**
	 * Lunch the website kernel and display the good page.
	 * 
	 * @return void
	 */
	public function run(): void
	{
		$this->loadPage();
	}


	// ==========================
	// ==== PRIVATE METHODS =====
	// ==========================
	/**
	 * Initialize all necessaries modules used by the website.
	 * 
	 * @return void
	 */
	private function initModules(): void
	{
		global $router;
		$router = new Router($this->_adminMode);
	}

	/**
	 * Initialize website options.
	 * TODO: a voir si c'est utile par rapport a l'objet Setting.
	 * 
	 * @return void
	 */
	private function initOptions(): void
	{
		$session = Session::getInstance();
		
		if (!isset($session->websiteOptions)) {
			$options = new Options();
			$options->loadFromDatabase();
			$session->websiteOptions = serialize($options);
		}
	}

	/**
	 * Load the good page depending of the user's request and display it.
	 * If the page has a controller, execute it before display.
	 * 
	 * @return void
	 */
	private function loadPage(): void
	{
		// Manage maintenance mode
		if (MAINTENANCE_MODE && !$this->_adminMode) {
			include_once($this->_websitePath . 'view/maintenance.php');
			return;
		}

		// Get information of the asked page.
		global $gmm;
		
		if (!isset($gmm)) {
			$gmm = new GetMethodManager();
		}
		
		$page = $this->_defaultPage;

		if ($gmm->getValue('page') !== false) {
			$page = $gmm->getValue('page');
		}

		// Check if the page is registrated
		if (!in_array($page, $this->_pages)) {
			include_once(ABSPATH . 'view/error.php');
			return;
		}

		// Check if page to display exists.
		if (!file_exists($this->_websitePath . 'view/' . $page . '.php')) {
			
			var_dump($this->_websitePath . 'view/' . $page . '.php');
			include_once(ABSPATH . 'view/error.php');
			return;
		}

		// == For administrators ==
		$session = Session::getInstance();
		
		if ($this->_adminMode && $page !== $this->_defaultPage && !$session->admin_isConnected) {
			$gmm->setValue('page', $this->_defaultPage);
			$this->loadPage();
		}
		// ========================

		foreach ($this->_includes as $include) {
			include_once($include);
		}

		if (file_exists($this->_websitePath . 'controller/system/' . $page . '.php')) {
			include_once($this->_websitePath . 'controller/system/' . $page . '.php');
		}

		include_once($this->_websitePath . 'view/' . $page . '.php');
	}

	/**
	 * Redirect the user on a spesific page.
	 * 
	 * @param string $page Page where the user have to be redirected.
	 * @param bool $isAdmin Say if the contexte is for the administration pages.
	 * @param GetMethodManager $gmm GetMethodManager object to use to send specific data.
	 * @return void
	 */
	public static function redirect(string $page, bool $isAdmin = false, GetMethodManager $gmm = null): void
	{
		global $router;
		header('Location: ' . $router->getUrl($page, $gmm));
	}
}