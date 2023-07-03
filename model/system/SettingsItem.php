<?php
namespace System;

// ==============================
// ==== Website setting item ====
// ==============================
class SettingsItem
{
    private $id = null;	// ID of the setting item.
    private $type = null; // Setting type (Boolean, Date, Int, etc ...)
    private $value = null; // Setting value (save as Text type in database)
   
   
    public function __construct($dbData = null)
    {
        if($dbData != null)
        {
            $this->id = $dbData['id_setting'];
            $this->type = $dbData['type'];
            $this->SetValue($dbData['value']);
        }
    }


    public function SetValue($value)
    {
        if($this->type != null)
        {
            switch($this->type)
            {
                case "boolean":
                    $this->value = Toolbox::StringToBool($value);
                    break;

                case "date":
                    $this->value = new DateTime($value);
                    break;

                default:
                    $this->value = $value;
                    break;
            }

            return true;
        }
        
        return false;
    }

    public function GetId()
    {
        return $this->id;
    }

    public function GetType()
    {
        return $this->type;
    }

    public function GetValue()
    {
        return $this->value;
    }

    // Update database
    public function SaveToDatabase()
    {
        $database = new Database();

        if($this->id != null && $this->type != null)
        {
            $value = null;
            switch($this->type)
            {
                case "boolean":
                    $value = Toolbox::BoolToString($this->value);
                    break;

                case "date":
                    $value = $this->value->format("Y-m-d");
                    break;

                default:
                    $value = $this->value;
                    break;
            }

            $database->Update("settings", "id_setting", $this->id, array("value" => $value));

            return true;
        }

        return false;
    }
}
?>