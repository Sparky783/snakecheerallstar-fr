<?php
namespace Snake;

use DateTime;
use Snake\EDay;

/**
 * Représente une horaire de cours d'entrainement.
 */
class Horaire
{
	// ==== ATTRIBUTS ====
    /**
     * @var EDay $_day Jour de l'entrainement.
     */
	private EDay $_day;
    
    /**
     * @var DateTime $_startTime Heure de début de l'entrainement.
     */
	private DateTime $_startTime = null;
    
    /**
     * @var DateTime $_endTime Heure de fin de l'entrainement.
     */
	private DateTime $_endTime = null;
    
    /**
     * @var string $_place Lieux de l'entrainement.
     */
    private string $_place = '';

	
	// ==== CONSTRUCTOR ====
    public function __construct(EDay $day, string $startTime, string $endTime, string $place)
    {
        $this->_day = $day;
        $this->_startTime = new DateTime($startTime);
        $this->_endTime = new DateTime($endTime);
        $this->_place = $place;
    }
	

    // ==== GETTERS ====
    /**
     * Retourne le jour d'entrainement.
     * 
     * @return EDay
     */
    public function getDay(): EDay
    {
        return $this->_day;
    }

    /**
     * Retourne l'heure de début du cours.
     * 
     * @return DateTime
     */
    public function getStartTime(): DateTime
    {
        return $this->_startTime;
    }

    /**
     * Retourne l'heure de fin du cours.
     * 
     * @return DateTime
     */
    public function getEndtime(): DateTime
    {
        return $this->_endTime;
    }

    /**
     * Retourne le lieu de l'entrainement.
     * 
     * @return string
     */
    public function getPlace(): string
    {
        return $this->_place;
    }
	
	// ==== SETTERS ====
	
	// ==== AUTRES METHODES ====
    /**
     * Retourne la durée du cours d'entrainement.
     * 
     * @return string
     */
	public function duration(): string 
	{
        $interval = $this->_startTime->diff($this->_startTime);
        return $interval->format('%h:%i');
	}
}