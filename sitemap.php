<?php
include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.

use System\Sitemap;

global $router;

header("Content-type: text/xml");

$sitemap = new Sitemap();

$sitemap->addPage($router->getURL('accueil'), 'weekly');
$sitemap->addPage($router->getURL('actualites'), 'weekly');
$sitemap->addPage($router->getURL('club'), 'monthly');
$sitemap->addPage($router->getURL('cours'), 'monthly');
$sitemap->addPage($router->getURL('inscription'), 'monthly');
$sitemap->addPage($router->getURL('galerie'), 'weekly');
$sitemap->addPage($router->getURL('contact'), 'monthly');
$sitemap->addPage($router->getURL('cgu'), 'monthly');

$sitemap->make();