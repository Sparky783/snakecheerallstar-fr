<?php
include_once("prepare.php"); // Chargement de la configuration et de l'ensemble des éléments communs.

use Snake\Section;
use Snake\Horaire;
use Snake\EDay;

echo "LITTLESTARS (tiny)<br />";
$section = Section::getById(10);
$section->clearHoraire();
$section->addHoraire(new Horaire(EDay::Saturday, '13:00:00', '14:00:00', 'MJC'));
$section->saveToDatabase();
echo "OK<br /><br />";


echo "GREENSTARS (mini)<br />";
$section = Section::getById(11);
$section->clearHoraire();
$section->addHoraire(new Horaire(EDay::Wednesday, '18:00:00', '19:30:00', 'MJC'));
$section->addHoraire(new Horaire(EDay::Saturday, '11:00:00', '13:00:00', 'MJC'));
$section->saveToDatabase();
echo "OK<br /><br />";


echo "SHOOTINGSTARS (cadet)<br />";
$section = Section::getById(12);
$section->clearHoraire();
$section->addHoraire(new Horaire(EDay::Monday, '18:00:00', '20:00:00', 'MJC'));
$section->addHoraire(new Horaire(EDay::Friday, '17:30:00', '19:00:00', 'MJC'));
$section->saveToDatabase();
echo "OK<br /><br />";


echo "WHITESTARS (junior)<br />";
$section = Section::getById(13);
$section->clearHoraire();
$section->addHoraire(new Horaire(EDay::Thursday, '18:00:00', '20:00:00', 'MJC'));
$section->addHoraire(new Horaire(EDay::Saturday, '09:00:00', '11:00:00', 'MJC'));
$section->saveToDatabase();
echo "OK<br /><br />";


echo "BLACKSTARS (senior)<br />";
$section = Section::getById(14);
$section->clearHoraire();
$section->addHoraire(new Horaire(EDay::Monday, '20:00:00', '22:00:00', 'MJC'));
$section->addHoraire(new Horaire(EDay::Thursday, '20:00:00', '22:00:00', 'MJC'));
$section->saveToDatabase();
echo "OK<br /><br />";