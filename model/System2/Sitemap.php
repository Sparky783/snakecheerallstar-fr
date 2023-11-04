<?php
namespace System;

/**
 * Class to generate sitemap file.
 * This file is use by searchbots to know which page to scan.
 */
class Sitemap
{
	// ==== ATTRIBUTS ====
	/**
	 * @var array $pages List of page of reference.
	 */
	public array $pages = [];

	// ==== OTHER METHODS ====
	/**
	 * Add a page to the sitemap.
	 * 
	 * @param string $loc URL of the page to add.
	 * @param string $changeFreq Frequence when page must be scaned.
	 * @param string $lastmod Date of the last modification on this page.
	 * @param string $priority Priority for the scan.
	 * @return bool Return True if the page was added, else False.
	 */
	public function addPage(string $loc, string $changeFreq = null, string $lastmod = null, string $priority = null): bool
	{
		if (isset($loc)) {
			$this->pages[] = [
				'loc' => $loc,
				'lastmod' => $lastmod,
				'changefreq' => $changeFreq,
				'priority' => $priority
			];

			return true;
		}

		return false;
	}

	/**
	 * Generate the sitemap content.
	 * 
	 * @return void
	 */
	public function make(): void
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ($this->pages as $page) {
			$xml .= '<url>';
			$xml .= '<loc>' .  $page['loc'] . '</loc>';

			if (isset($page['lastmod']) && $page['lastmod'] !== null) {
				$xml .= '<lastmod>' .  $page['lastmod'] . '</lastmod>';
			}

			if (isset($page['changefreq']) && $page['changefreq'] !== null) {
				$xml .= '<changefreq>' .  $page['changefreq'] . '</changefreq>';
			}

			if(isset($page['priority']) && $page['priority'] !== null) {
				$xml .= '<priority>' .  $page['priority'] . '</priority>';
			}

			$xml .= '</url>';
		}
		
		$xml .= '</urlset>';

		echo $xml;
	}
}