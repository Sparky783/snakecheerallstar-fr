<?php
namespace System;
use System\ToolBox;

/**
 * Tool to manage logs.
 */
class LogManager
{
	/**
	 * Add a new line in log file.
	 * 
	 * @param string $message Message text to add in log file.
	 * @return void
	 */
	static public function addLine(string $message): void
	{
		// Préparation du fichier
		$path = ABSPATH . 'logs';

		if (ToolBox::makeDirectory($path)) {
			$file = $path . '/Report ' . date("Y") . '.txt';
			$line = date('Y-m-d H:i:s') . ' - ' . $message . '\n';
			file_put_contents($file, $line, FILE_APPEND);
		}
	}
}