<?php
namespace System;
use System\GetMethodManager;

/**
 * Object used to manage routing system.
 */
class Router
{
    // == ATTRIBUTES ==
	private bool $_adminMode;
	private string $_defaultPage = ''; // Display page if the request page is null.


    // == CONSTRUCTORS ==
	/**
	 * Create routing system.
	 * 
	 * @param bool $isAdmin Say if the kernel is used for administration pages.
	 */
	public function __construct(bool $isAdmin = false)
	{
		$this->_adminMode = $isAdmin;
	}

	/**
	 * Set the default page dislayed if the resquest page is empty.
	 * 
	 * @param string $page Default page that must be display if no one else are selected.
	 */
	public function setDefaultPage(string $page): void
	{
		$this->_defaultPage = $page;
	}

	/**
	 * Get the selected page. this information is present into GET method.
	 * 
	 * @return string Name of the selected page.
	 */
	public function getCurrentPage(): string
	{
		$gmm = new GetMethodManager();

		if ($gmm->getValue('page') !== false)
			return $gmm->getValue('page');

		return $this->_defaultPage; // default page
	}

	/**
	 * Get the URL to a choose page.
	 * 
	 * @param string $page Page where to go.
	 * @param GetMethodManager $gmm GetMethodManager object to use for this new URL.
	 * @return string URL to put into HTML or to send to the user.
	 */
	public function getUrl(string $page, GetMethodManager $gmm = null): string
	{
		if ($gmm === null)
			$gmm = new GetMethodManager();

		$gmm->setValue("page", $page);

		if ($this->_adminMode)
			return URL . "/admin.php" . $gmm->getString();
		else
			return URL . "/index.php" . $gmm->getString();
	}

	/**
	 * Display a choosen URL.
	 * 
	 * @param string $page Page to display in URL.
	 * @param array $options Options to include into URL.
	 */
	public function Url(string $page, array $options = array()): void
	{
		$gmm = new GetMethodManager();

		if (count($options) > 0) {
			foreach ($options as $key => $value)
				$gmm->setValue($key, $value);
		}

		echo $this->getUrl($page, $gmm);
	}

	/**
	 * Get the API URL with the wanted request.
	 * 
	 * @param string $request Request to send to the API.
	 * @return string URL to the API.
	 */
	public function getApi(string $request = ''): string
	{
		if ($this->_adminMode)
			return URL . '/admin-api.php?request=' . $request;

		return URL . '/api.php?request=' . $request;
	}

	/**
	 * Display the API URL with the wanted request.
	 * 
	 * @param string $request Request to send to the API.
     * @return void
	 */
	public function api(string $request = ''): void
	{
		echo $this->getApi($request);
	}
}