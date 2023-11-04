<?php
namespace System;

use DateTime;
use ErrorException;
use System\Database;
use System\ToolBox;

/**
 * Represent a setting item.
 */
class SettingsItem
{
    // ==== ATTRIBUTS ====
    /**
     * @var string $_id ID of the setting item.
     */
    private string $_id = '';
    
    /**
     * @var string $_type Setting type (Boolean, Date, Int, etc ...)
     */
    private string $_type = '';
    
    /**
     * @var mixed $_value Setting value (save as Text type in database)
     */
    private mixed $_value = null;
   
    // ==== CONSTRUCTOR ====
    /**
     * Make a new instance of setting item.
     *
     * @param array $dbData Data from database to load the setting.
     */
    public function __construct($dbData = [])
    {
        if (count($dbData) > 0) {
            $this->_id = $dbData['id_setting'];
            $this->_type = $dbData['type'];
            $this->setValue($dbData['value']);
        }
    }

    // == GETTERS ==
    /**
     * Get the setting ID.
     * 
     * @return string ID of the setting.
     */
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * Get the setting type.
     * 
     * @return string Type of the setting.
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * Get the setting value.
     * 
     * @return mixed Value of the setting.
     */
    public function getValue(): mixed
    {
        return $this->_value;
    }


    // == SETTERS ==
    /**
     * Set the value of the setting depending of the type.
     * 
     * @param mixed $value Value to set.
     * @return bool Return True if the value is set, else False.
     */
    public function setValue(mixed $value): bool
    {
        if (empty($this->_type)) {
            return false;
        }

        switch ($this->_type) {
            case 'int':
                $this->_value = intval($value);
                break;

            case 'float':
                $this->_value = floatval($value);
                break;

            case 'boolean':
                $this->_value = Toolbox::StringToBool($value);
                break;

            case 'date':
                $this->_value = new DateTime($value);
                break;

            default:
                $this->_value = $value;
                break;
        }

        return true;
    }

    // == OTHER METHODS ==
    /**
     * Prepare the new created setting to be used.
     * 
     * @param string $type Type of the setting.
     * @return void
     */
    public function initialize(string $type): void
    {
        $this->_type = $type;
        $this->_id = '';
    }

    /**
	 * Save this setting object into database.
	 * If this setting is a new one, create id and affect a new ID.
	 * 
	 * @return bool Return True if the token was correctly saved, else False.
	 */
    public function saveToDatabase()
    {
        if (empty($this->_type)) {
            throw new ErrorException('The type of the setting cannot be null.');
        }

        if (empty($this->_id)) {
            return false;
        }

        $database = new Database();
        $value = null;

        switch ($this->_type) {
            case 'boolean':
                $value = Toolbox::boolToString($this->_value);
                break;

            case 'date':
                $value = $this->_value->format('Y-m-d');
                break;

            default:
                $value = $this->_value;
                break;
        }

        return $database->update('settings', 'id_setting', $this->_id, ['value' => $value]);
    }
}
?>