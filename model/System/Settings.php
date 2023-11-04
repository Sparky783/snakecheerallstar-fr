<?php
namespace System;
use ErrorException;

/**
 * Manage all website settings
 */
class Settings
{
    // ==== ATTRIBUTS ====
	/**
	 * @var array $settings Setting list.
	 */
    private array $settings = [];

	// ==== OTHER METHODS ====
    /**
     * Load all settings from database.
     *
     * @return void
     */
    public function loadFromDatabase(): void
    {
        $database = new Database();
        $rech = $database->query("SELECT * FROM settings");
        
        while ($data = $rech->fetch()) {
            $param = new SettingsItem($data);
            $this->settings[$param->getId()] = $param;
        }
    }

    /**
     * Update all settings into database.
     *
     * @return bool Return True if all settings was saved, else False.
     */
    public function saveToDatabase(): bool
    {
        $result = true;

        foreach ($this->settings as $option) {
            $result &= $option->saveToDatabase();
        }

        return $result;
    }

    // ==== OVERRIDE ====
    // Override Set, Get, Isset and Unset function.
    // That is used to do Setting->MyVariable;

    /**
     * Set a setting by its name.
     *
     * @param string $name Name of the setting to set.
     * @param mixed $value Value of the setting.
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->settings[$name]->setValue($value);
    }

    /**
     * Get a setting by its name.
     *
     * @param string $name Name of the setting to set.
     * @return mixed Value of the setting
     * @throws ErrorException
     */
    public function __get(string $name): mixed
    {
        if (isset($this->settings[$name])) {
            return $this->settings[$name]->getValue();
        }

        throw new ErrorException("The setting '$name' does not exist.");
    }

    /**
     * Say if the setting exist of not.
     *
     * @param string $name Name of the setting to test.
     * @return bool Return True if the setting exist, else False.
     */
    public function __isset(string $name): bool
    {
        return isset($this->settings[$name]);
    }

    /**
     * Remove a setting from the manager.
     * Warning: the setting is not remove from the database.
     *
     * @param string $name Name of the setting to remove.
     * @return void
     */
    public function __unset(string $name): void
    {
        unset($this->settings[$name]);
    }
}
?>