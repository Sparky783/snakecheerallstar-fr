<?php
require_once(ABSPATH . "model/system/Database.php");
require_once(ABSPATH . "model/system/ToolBox.php");
require_once(ABSPATH . "model/OptionParam.php");

class Options
{
    private $options = array();
   
   
    public function __construct() {}
   

    // Charge les options depuis la base de donnée.
    public function LoadFromDatabase()
    {
        $database = new Database();
        $rech = $database->Query("SELECT * FROM options");
        
        while($data = $rech->fetch())
        {
            $param = new OptionParam($data);
            $this->options[$param->GetId()] = $param;
        }
    }

    // Met à jour la basse de donnée
    public function SaveToDatabase()
    {
        $result = true;
        $database = new Database();

        foreach($this->options as $option)
            $result = $result & $option->SaveToDatabase();

        return $result;
    }
   

    // Redéfinie les fonctions Set, Get, Isset et Unset pour l'objet Options.
    // Ceci permet de l'utiliser en tappant Options->MaVariable;
    public function __set($name, $value)
    {
        $this->options[$name]->SetValue($value);
    }

    public function __get($name)
    {
        if(isset($this->options[$name]))
            return $this->options[$name]->GetValue();
    }
   
    public function __isset($name)
    {
        return isset($this->options[$name]);
    }
   
    public function __unset($name)
    {
        unset($this->options[$name]);
    }
}
?>