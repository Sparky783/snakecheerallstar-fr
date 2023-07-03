<?php
namespace Snake;

use DateTime;

class Horaire
{
	// == ATTRIBUTS ==
	private $day = "";
	private $start_time = null;
	private $end_time = null;
	
	// == METHODES PRIMAIRES ==
    public function __construct(string $day, string $start_time, string $end_time)
    {
        $this->day = $day;
        $this->start_time = new DateTime($start_time);
        $this->end_time = new DateTime($end_time);
    }
	
    // == METHODES GETTERS ==
    public function GetDay() : string
    {
        return $this->day;
    }

    public function GetStartTime() : DateTime
    {
        return $this->start_time;
    }

    public function GetEndtime() : DateTime
    {
        return $this->end_time;
    }
	
	// == METHODES SETTERS ==
	
	// == AUTRES METHODES ==
	public function Duration()
	{
        $interval = $this->start_time->diff($this->start_time);
        return $interval->format('%h:%i');
	}
}