<?php
include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.
include_once(ABSPATH . "model/system/Sitemap.php");

global $router;

header("Content-type: text/xml");

$sitemap = new Sitemap();

$sitemap->AddPage($router->GetURL("accueil"), "weekly");
$sitemap->AddPage($router->GetURL("actualites"), "weekly");
$sitemap->AddPage($router->GetURL("club"), "monthly");
$sitemap->AddPage($router->GetURL("cours"), "monthly");
$sitemap->AddPage($router->GetURL("inscription"), "monthly");
$sitemap->AddPage($router->GetURL("galerie"), "weekly");
$sitemap->AddPage($router->GetURL("contact"), "monthly");
$sitemap->AddPage($router->GetURL("cgu"), "monthly");

$sitemap->Make();