<?php
namespace Common;
use InvalidArgumentException;

/**
 * Tool to manage uplad of big files (> 2Mo).
 */
class UploadFile
{
	// ==== ATTRIBUTS ====
	/**
	 * @var string $_name Name of the file.
	 */
	private string $_name;

	/**
	 * @var int $_totParts Number part needed.
	 */
	private int $_totParts;
	
	/**
	 * @var array $_name Data parts of the file.
	 */
	private $_datas;

	// ==== CONSTRUCTOR ====
	/**
	 * Create a instance of the upload file manager.
	 * 
	 * @param string $name Name of the file.
	 * @param int $totParts Number of part needed for this file.
	 */
	public function __construct(string $name = '', int $totParts = 1)
	{
		if ($totParts < 1) {
			throw new InvalidArgumentException('The number of parts must be superior to 1.');
		}

		$this->_name = $name;
		$this->_totParts = $totParts;
		$this->_datas = [];

		for ($i = 0; $i < $totParts; $i ++) {
			$this->_datas[] = null;
		}
	}

	/**
	 * Add a new data part.
	 * 
	 * @param int $posPart Data part position
	 * @param string $data Data part
	 */
	public function addPart(int $posPart, string $data): void
	{
		$this->_datas[$posPart] = $data;
	}

	/**
	 * Return if the file is complete. If it has all data parts.
	 * 
	 * @return bool return True if the file is complete, else False.
	 */
	public function isComplete(): bool
	{
		foreach ($this->_datas as $data) {
			if($data === null) {
				return false;
			}
		}

		return true;
	}
	
	/**
	 * Return the file name.
	 * 
	 * @return string
	 */
	public function getName(): string
	{
		return $this->_name;
	}

	/**
	 * Return the file content.
	 * 
	 * @return string|false Return False if the file content is not complete.
	 */
	public function getFileContent(): string|false
	{
		if ($this->isComplete()) {
			$content = '';

			foreach ($this->_datas as $data) {
				$content .= $data;
			}
			
			return $content;
		}
		
		return false;
	}
}