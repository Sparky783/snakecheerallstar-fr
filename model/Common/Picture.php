<?php
namespace Common;
use \imagecreatefromstring;

// ===================================================
//  Utilitaire pour télécharger et modifier une image
// ===================================================

class Picture
{
	// == ATTRIBUTS ==
	private $path = "";
	private $name = "";
	private $ext = ""; // Extension
	private $source = null; // Contenu du fichier
	
	
	// == METHODES PRIMAIRES ==
	public function __construct()
	{
	}
	
	// == PUBLIC METHODS ==
	public function GetSource()
	{
		if($this->source != null)
			return $this->source;
		
		return false;
	}
	
	public function GetWidth()
	{
		if($this->source != null)
			return imagesx($this->source);

		return false;
	}
	
	public function GetHeight()
	{
		if($this->source != null)
			return imagesy($this->source);

		return false;
	}

	public function GetFullpath()
	{
		if($this->path != "" && $this->name != null && $this->ext != null)
			return $this->path . "/" . $this->name . $this->ext;

		return false;
	}

	// == AUTRES METHODES ==
	// Charge une image depuis un fichier
	public function LoadFromFilePath($path)
	{
		$this->ReadPath($path);

		switch($this->ext)
		{
			case ".bmp":
				$this->source = imagecreatefrombmp($path);
				break;
				
			case ".wbmp":
				$this->source = imagecreatefromwbmp($path);
				break;
			
			case ".png":
				$this->source = imagecreatefrompng($path);
				break;
				
			case ".jpg":
			case ".jpeg":
				$this->source = imagecreatefromjpeg($path);
				break;
				
			case ".gif":
				$this->source = imagecreatefromgif($path);
				break;
			
			case ".gd":
				$this->source = imagecreatefromgd($path);
				break;
				
			case ".gd2":
				$this->source = imagecreatefromgd2($path);
				break;
				
			case ".webp":
				$this->source = imagecreatefromwebp($path);
				break;
				
			case ".xbm":
				$this->source = imagecreatefromxbm($path);
				break;
				
			case ".xpm":
				$this->source = imagecreatefromxpm($path);
				break;

			default:
				return false;
		}
		
		return true;
	}
	
	public function LoadFromFileContent($content)
	{
		$source = imagecreatefromstring($content);

		if($source === false)
			return false;
		
		$this->source = $source;
		return true;
	}

	public function SetInterlace($bool)
	{
		if($this->source != null)
			return (bool)imageinterlace($this->source, (int)$bool);

		return false;
	}
	
	// Sauvegarde la photo dans un fichier.
	public function SavePicture($path, $quality = 100)
	{
		$this->ReadPath($path);
		
		if($this->source != null)
		{
			switch($this->ext)
			{
				case ".bmp":
					return imagebmp($this->source, $path);
					
				case ".wbmp":
					return imagewbmp($this->source, $path);
				
				case ".png":
					return imagepng($this->source, $path, $quality);
					
				case ".jpg":
				case ".jpeg":
					return imagejpeg($this->source, $path, $quality);
					
				case ".gif":
					return imagegif($this->source, $path);
					
				case ".gd":
					return imagegd($this->source, $path);
					
				case ".gd2":
					return imagegd2($this->source, $path);
					
				case ".webp":
					return imagewebp($this->source, $path);
					
				case ".xbm":
					return imagexbm($this->source, $path);
			}
		}

		return false;
	}
	
	// Redimentionne l'image de manière proportionnelle.
	// La largeur est définie.
	public function ResizeWidth($newWidth)
	{
		if($this->source != null)
		{
			$currentWidth = imagesx($this->source);
			$currentHeight = imagesy($this->source);
			
			$newHeight = $currentHeight * $newWidth / $currentWidth;
			
			return $this->Resize($newWidth, $newHeight);
		}
		
		return false;
	}
	
	// Redimentionne l'image de manière proportionnelle.
	// La hauteur est définie.
	public function ResizeHeight($newHeight)
	{
		if($this->source != null)
		{
			$currentWidth = imagesx($this->source);
			$currentHeight = imagesy($this->source);
			
			$newWidth = $currentWidth * $newHeight / $currentHeight;
			
			return $this->Resize($newWidth, $newHeight);
		}

		return false;
	}
	
	// Redimentionne l'image à la taille voulue.
	public function Resize($newWidth, $newHeight)
	{
		if($this->source != null)
		{
			$currentWidth = imagesx($this->source);
			$currentHeight = imagesy($this->source);
			
			$image_finale = imagecreatetruecolor($newWidth, $newHeight);
			imagealphablending($image_finale, false);
			imagesavealpha($image_finale, true);
			
			$transparent = imagecolorallocatealpha($image_finale, 255, 255, 255, 127);
			imagefilledrectangle($image_finale, 0, 0, $newWidth, $newHeight, $transparent);
			
			if(imagecopyresampled($image_finale, $this->source, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight))
			{
				$this->source = $image_finale;
				
				return true;
			}
		}

		return false;
	}
	
	// Ajoute le image à la photo. L'image doit être plus petite que la photo.
	public function InsertImage($new_image_source, $top = null, $right = null, $bottom = null, $left = null)
	{
		if($this->source != null)
		{
			$new_image = $new_image_source;
			$largeur_source = imagesx($this->source);
			$hauteur_source = imagesy($this->source);
			$largeur_image = imagesx($new_image);
			$hauteur_image = imagesy($new_image);
			
			if($largeur_image <= $largeur_source and $hauteur_image <= $hauteur_source)
			{
				// Placement de la nouvelle image par rapport à l'ancienne.
				$pos_x = 0;
				$pos_y = 0;
				
				if($top != null)
					$pos_y = $top;
				else if($bottom != null)
					$pos_y = $hauteur_source - $hauteur_image - $bottom;
				
				if($left != null)
					$pos_x = $left;
				else if($right != null)
					$pos_x = $largeur_source - $largeur_image - $right;

				// On met le logo (source) dans l'image de destination (la photo)
				if(imagecopy($this->source, $new_image, $pos_x, $pos_y, 0, 0, $largeur_image, $hauteur_image))
					return true;
			}
		}
		
		return false;
	}

	// Savegarde une copy de l'image
	public function SaveCopy($path, $maxSize = null, $quality = 100)
	{
		$obj = $this;
		$copy = clone $obj;
		
		if($maxSize != null)
		{
			if($copy->GetWidth() > $copy->GetHeight())
				$copy->ResizeWidth($maxSize);
			else
				$copy->ResizeHeight($maxSize);
		}

		return $copy->SavePicture($path, $quality);
	}
	
	// == PRIVATE METHODS ==
	// Lit les informations depuis le chemin du fichier
	private function ReadPath($fullpath)
	{
		$this->name = strrchr($fullpath, '/');
		$this->path = str_replace("/" . $this->name, "", $fullpath);
		$this->ext = mb_strtolower(strrchr($this->name, '.'));
	}
}