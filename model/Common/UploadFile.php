<?php
namespace Common;

/*
 *	Gère l'upload de fichier lourd (> 2Mo).
 */
class UploadFile
{
	private $name;
	private $tot_parts;
	private $datas;

	
	// Crée un objet de gestion d'upload.
	// $name : Nom du fichier
	// $tot_parts : Nombre le block composant le fichier
	public function __construct($name = "", $tot_parts = 1)
	{
		$this->name = $name;
		$this->tot_parts = $tot_parts;
		$this->datas = array();

		for($i = 0; $i < $tot_parts; $i ++)
			$this->datas[] = null;
	}

	// Ajout un block au fichier.
	// $pos_part : Position du block dans le fichier
	// $data : Données composant le block
	public function AddPart($pos_part, $data)
	{
		$pos = intval($pos_part);
		$this->datas[$pos] = $data;
	}

	// Retourn True si le fichier est complet (possède tous ses blocks), sinon False.
	public function IsComplete()
	{
		foreach ($this->datas as $data)
		{
			if($data == null)
				return false;
		}

		return true;
	}
	
	// Retourne le nom du fichier.
	public function GetName()
	{
		return $this->name;
	}

	// Retourne le contenu du fichier, sinon False si le fichier n'est pas complet.
	public function GetFileContent()
	{
		if($this->IsComplete())
		{
			$content = "";

			foreach ($this->datas as $data)
				$content .= $data;
			
			return $content;
		}
		
		return false;
	}
}