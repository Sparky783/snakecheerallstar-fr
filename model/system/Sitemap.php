<?php
class Sitemap
{
	public $pages = array();

	
	public function __construct() {}


	public function AddPage($loc, $changefreq = null, $lastmod = null, $priority = null) {
		if(isset($loc)) {
			$this->pages[] = array(
				'loc' => $loc,
				'lastmod' => $lastmod,
				'changefreq' => $changefreq,
				'priority' => $priority
			);

			return true;
		}

		return false;
	}


	public function Make() {
		$xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach ($this->pages as $page) {
			$xml .= '<url>';
			$xml .= '<loc>' .  $page['loc'] . '</loc>';

			if(isset($page['lastmod']) && $page['lastmod'] != null)
				$xml .= '<lastmod>' .  $page['lastmod'] . '</lastmod>';

			if(isset($page['changefreq']) && $page['changefreq'] != null)
				$xml .= '<changefreq>' .  $page['changefreq'] . '</changefreq>';

			if(isset($page['priority']) && $page['priority'] != null)
				$xml .= '<priority>' .  $page['priority'] . '</priority>';

			$xml .= '</url>';
		}
		
		$xml .= '</urlset>';

		echo $xml;
	}
}