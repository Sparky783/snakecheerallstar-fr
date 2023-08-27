<?php
namespace System;

use DateTime;
use ErrorException;
use System\Database;
use System\ToolBox;

/**
 * Represent a custom option that can be created, edit and remove by the user.
 */
class OptionParam
{
    // ==== ATTRIBUTS ====
	/**
	 * @var int|null $_id Option's ID.
	 */
    private string $_id = '';
    
	/**
	 * @var string $_type Option's type (Boolean, Date, Int, etc ...).
	 */	
    private string $_type = '';
    
	/**
	 * @var mixed $_value Option's value (Save as Text type in database).
	 */
    private mixed $_value = null;
   

    // ==== CONSTRUCTOR ====
    /**
     * Make a new instance of option.
     *
     * @param array $dbData Data from database to load the option.
     */
    public function __construct(array $dbData = [])
    {
        if (count($dbData) > 0) {
            $this->_id = $dbData['id_option'];
            $this->_type = $dbData['type'];
            $this->setValue($dbData['value']);
        }
    }

    // ==== GETTERS ====
    /**
     * Get the option ID.
     * 
     * @return string ID of the option.
     */
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * Get the option type.
     * 
     * @return string Type of the option.
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * Get the option value.
     * 
     * @return mixed Value of the option.
     */
    public function getValue(): mixed
    {
        return $this->_value;
    }

    // ==== SETTERS ====
    /**
     * Set the value of the option depending of the type.
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

    // ==== OTHER METHODS ====
    /**
     * Prepare the new created option to be used.
     * 
     * @param string $type Type of the option.
     * @return void
     */
    public function initialize(string $type): void
    {
        $this->_type = $type;
        $this->_id = '';
    }

	/**
	 * Save this option object into database.
	 * If this option is a new one, create id and affect a new ID.
	 * 
	 * @return bool Return True if the token was correctly saved, else False.
	 */
    public function saveToDatabase(): bool
    {
        if ($this->_type == null) {
            throw new ErrorException('The type of the option cannot be null.');
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

        return $database->update('options', 'id_option', $this->_id, ['value' => $value]);
    }
}
?>