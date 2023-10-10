<?php
namespace System;

/**
 * Loader for PHP to manage classes.
 */
class SplClassLoader
{
    // ==== ATTRIBUTS ====
	/**
	 * @var string $_classFolder Folder where classes are contents.
	 */
    private string $_classFolder;

	// ==== CONSTRUCTOR ====
    /**
     * Creates a new <tt>SplClassLoader</tt> that loads classes.
     * 
     * @param string $$classesFolder Folder to include (optionnal).
     */
    public function __construct(string $folder)
    {
        $this->_classFolder = $folder . '/';
    }

	// ==== OTHER METHODS ====
    /**
     * Installs this class loader on the SPL autoload stack.
     */
    public function register(): void
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     */
    public function unregister(): void
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     * @return void
     */
    public function loadClass(string $className): void
    {
        $className = str_replace('\\', '/', $className);
        $wantedFile = $this->_classFolder . $className . '.php';

        if (file_exists($wantedFile)) {
            require_once $wantedFile;
        }
    }
}