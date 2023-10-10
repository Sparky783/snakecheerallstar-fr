<?php
namespace Snake;


/**
 * Represent days of the week
 */
enum EDay: string
{
    /**
     * Lundi
     */
	case Monday = 'Lundi';

    /**
     * Mardi
     */
	case Tuesday = 'Mardi';
    
    /**
     * Mercredi
     */
	case Wednesday = 'Mercredi';
    
    /**
     * Jeudi
     */
	case Thursday = 'Jeudi';
    
    /**
     * Vendredi
     */
	case Friday = 'Vendredi';
    
    /**
     * Samedi
     */
	case Saturday = 'Samedi';
    
    /**
     * Dimanche
     */
	case Sunday = 'Dimanche';
}