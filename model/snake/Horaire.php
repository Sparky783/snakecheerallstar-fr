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
	private EDay $_day;
	private DateTime $_startTime = null;
	private DateTime $_endTime = null;
	
	// ==== CONSTRUCTOR ====
    public function __construct(EDay $day, string $startTime, string $endTime)
    {
        $this->_day = $day;
        $this->_startTime = new DateTime($startTime);
        $this->_endTime = new DateTime($endTime);
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