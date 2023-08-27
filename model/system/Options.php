<?php
namespace System;
use ErrorException;
use System\Database;
use System\OptionParam;

/**
 * Manage all website options.
 * 
 * Options are different from website settings (Settings class).
 * They can be created, remove and modify by the user.
 */
class Options
{
    // ==== ATTRIBUTES ====
	/**
	 * @var array $_options Options list.
	 */
    private array $_options = [];
   
   
    // ==== CONSTRUCTORS ====
    /**
     * Make a new instance of option manager.
     */
    public function __construct() {}
   
    // ==== OTHER METHODS ====
    /**
     * Load all options from database
     * 
     * @return void
     */
    public function loadFromDatabase(): void
    {
        $database = new Database();
        $rech = $database->query("SELECT * FROM `options`");

        while ($data = $rech->fetch()) {
            $param = new OptionParam($data);
            $this->_options[$param->getId()] = $param;
        }
    }

    /**
     * Update all options into database
     * 
     * @return bool Return True if all the process succeed, else False.
     */
    public function saveToDatabase(): bool
    {
        $result = true;

        foreach ($this->_options as $option) {
            $result &= $option->saveToDatabase();
        }

        return $result;
    }
   

    // ==== OVERRIDE ====
    // Override Set, Get, Isset and Unset function.
    // That is used to do Options->MyVariable;

    /**
     * Set an option by its name.
     * 
     * @param string $name Name of the option to set.
     * @param mixed $value Value of the option.
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        if (isset($this->_options[$name])) {
            $this->_options[$name]->setValue($value);
        } else {
            $option = new OptionParam();
            $option->initialize($this->getTypeFromValue($value));
            $option->setValue($value);

            $this->_options[$name] = $option;
        }
    }

    /**
     * Get an option by its name.
     *
     * @param string $name Name of the option to set.
     * @return mixed Value of the option
     * @throws ErrorException
     */
    public function __get(string $name): mixed
    {
        if (isset($this->_options[$name])) {
            return $this->_options[$name]->getValue();
        }

        throw new ErrorException("The option '$name' does not exist.");
    }
   
    /**
     * Say if the option exist of not.
     * 
     * @param string $name Name of the option to test.
     * @return bool Return True if the option exist, else False.
     */
    public function __isset(string $name): bool
    {
        return isset($this->_options[$name]);
    }

    /**
     * Remove an option from the manager.
     * Warning: the option is not remove from the database.
     * 
     * @param string $name Name of the option to remove.
     * @return void 
     */
    public function __unset(string $name): void
    {
        unset($this->_options[$name]);
    }

    // ==== PRIVATE METHODS ====
    /**
     * Find the type depending of the value.
     * 
     * @param mixed $value Value to analyze.
     * @return string Type of the value.
     */
    private function getTypeFromValue(mixed $value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_int($value)) {
            return 'int';
        }

        if (is_float($value)) {
            return 'float';
        }

        return 'default'; // Default type.
    }
}
?>