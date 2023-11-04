<?php
namespace System;
use ErrorException;

/**
 * Class used to manage Session object.
 */
class Session
{
    // States
	/**
	 * @var bool SESSION_STARTED Admin's ID.
	 */
    public const SESSION_STARTED = TRUE;

	/**
	 * @var bool SESSION_STARTED Admin's ID.
	 */
    public const SESSION_NOT_STARTED = FALSE;

    // ==== ATTRIBUTS ====
	/**
	 * @var bool $_sessionState Session state.
	 */
    private bool $_sessionState = self::SESSION_NOT_STARTED; // The state of the session

	/**
	 * @var Session $_instance Session object.
	 */
    private static Session $_instance; // THE only instance of the class


    // ==== CONSTRUCTOR ====
    private function __construct() {}

    // ==== STATIC METHODS ====
    /**
     * Returns THE instance of 'Session'.
     * The session is automatically initialized if it wasn't.
     *
     * @return Session
     */
    public static function getInstance(): Session
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }
       
        self::$_instance->startSession();
       
        return self::$_instance;
    }

    // == PUBLIC METHODS ==
    /**
     * (Re)starts the session.
     *
     * @return bool Return True if the session has been initialized, else False.
     */
    public function startSession(): bool
    {
        if ($this->_sessionState === self::SESSION_NOT_STARTED) {
            $this->_sessionState = session_start();
        }
       
        return $this->_sessionState;
    }

    /**
     * Destroys the current session.
     *
     * @return bool Return True if session has been deleted, else False.
     */
    public function destroy(): bool
    {
        if ($this->_sessionState === self::SESSION_STARTED) {
            $this->_sessionState = !session_destroy();
            unset($_SESSION);

            return !$this->_sessionState;
        }

        return FALSE;
    }

    // == OVERRIDE ==
    // Override Set, Get, Isset and Unset function.
    // That is used to do Session->MyVariable;

    /**
     * Stores datas in the session.
     * Example: $instance->foo = 'bar';
     *
     * @param string $name Name of the datas.
     * @param mixed $value Your datas.
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Gets datas from the session.
     * Example: echo $instance->foo;
     *
     * @param string $name Name of the datas to get.
     * @return mixed Datas stored in session.
     * @throws ErrorException
     */
    public function __get(string $name): mixed
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        throw new ErrorException("The setting '$name' does not exist.");
    }

    /**
     * Say if the variable exist in the session or not.
     *
     * @param string $name Variable to test.
     * @return bool Return True if the variable exist, else False.
     */
    public function __isset(string $name): bool
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Remove the variable from the session.
     *
     * @param string $name Variable to remove.
     * @return void
     */
    public function __unset(string $name): void
    {
        unset($_SESSION[$name]);
    }
}