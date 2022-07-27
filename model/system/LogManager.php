<?php
require_once(ABSPATH . "model/system/ToolBox.php");

class LogManager
{
	static public function AddLine($message)
	{
		// Préparation du fichier
		$path = ABSPATH . "logs";

		if(TollBox::IsDirectoryOrCreateIt($path)){
			$file = $path . "/Report " . date("Y") . ".txt";
			$line = date("Y-m-d H:i:s") . " - " . $message . "\n";
			file_put_contents($file, $line, FILE_APPEND);
		}
	}
}