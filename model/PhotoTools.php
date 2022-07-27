<?php
// ===================================================
//  Utilitaire pour télécharger et modifier une image
// ===================================================

require_once(ABSPATH . "model/system/ToolBox.php");

class PhotoTools {
	// == ATTRIBUTS ==
	private $extensions = array('.png', '.gif', '.jpg', '.jpeg', '.PNG', '.GIF', '.JPG', '.JPEG');
	
	private $photo_name;
	private $photo_path;
	private $photo_source;
	
	// == METHODES PRIMAIRES ==
	public function __construct(){
		$this->photo_name = "";
		$this->photo_path = "";
		$this->photo_source = null;
	}
	
	// == METHODES GETTERS ==
	public function getPhotoSource() {
		return $this->photo_source;
	}
	
	// == METHODES SETTERS ==
	private function setPathAndName($path) {
		$this->photo_name = strrchr($path, '/');
		$this->photo_path = str_replace("/".$this->photo_name, "", $path);
	}

	public function setPhoto($path_image) {
		$this->setPathAndName($path_image);
		$ext = strrchr($path_image, '.');

		if(in_array($ext, $this->extensions)) {
			if($ext == ".png" or $ext == ".PNG")
				$this->photo_source = imagecreatefrompng($path_image);
			else if($ext == ".jpg" or $ext == ".jpeg" or $ext == ".JPG" or $ext == ".JPEG")
				$this->photo_source = imagecreatefromjpeg($path_image);
			else if($ext == ".gif" or $ext == ".GIF")
				$this->photo_source = imagecreatefromgif($path_image);

			return true;
		}
		return false;
	}

	public function setInterlace($bool) {
		return (bool)imageinterlace($this->photo_source, (int)$bool);
	}
	
	// == AUTRES METHODES ==
	// Sauvegarde la photo dans un fichier.
	public function savePhoto($type, $new_path = null, $new_name = null, $quality = 100){
		$type = mb_strtolower(".".$type);
		if(in_array($type, $this->extensions)){
			if($new_path == null)
				$new_path = $this->photo_path;
			if($new_name == null)
				$new_name = $this->photo_name;
			
			if($type == ".png" or $type == ".PNG")
				return imagepng($this->photo_source, $new_path."/".$new_name.$type, $quality);
			else if($type == ".jpg" or $type == ".jpeg" or $type == ".JPG" or $type == ".JPEG")
				return imagejpeg($this->photo_source, $new_path."/".$new_name.$type, $quality);
			else if($type == ".gif" or $type == ".GIF")
				return imagegif($this->photo_source, $new_path."/".$new_name.$type);
		}
		return false;
	}
	
	// Télécharge une photo.
	public function upload($file_image_source, $file_image_destination, $file_size_max = 5242880) {
		$this->setPathAndName($file_image_destination);
		
		if(ToolBox::IsDirectoryOrCreateIt($this->photo_path)){
			$ext = strrchr($file_image_source['name'], '.');
			
			if(in_array($ext, $this->extensions) and $file_image_source['size'] <= $file_size_max) {
				$path = $this->photo_path ."/". $this->photo_name;
				
				if(move_uploaded_file($file_image_source['tmp_name'], $path)){
					if($this->setPhoto($path))
						return true;
				}
			}
		}
		return false;
	}
	
	// Redimentionne l'image fournis en paramêtre a la taille voulu.
	public function resize($dimention){
		$dim_image = array(imagesx($this->photo_source), imagesy($this->photo_source));
		$correction = false;
		if($dim_image[0] >= $dim_image[1]){ // paysage
			if($dim_image[0] > $dimention){
				$newLargeur = $dimention;
				$newHauteur = ($dimention*$dim_image[1])/$dim_image[0];
				$correction = true;
			}
		}else{ // portrait
			if($dim_image[1] > $dimention){
				$newLargeur = ($dimention*$dim_image[0])/$dim_image[1];
				$newHauteur = $dimention;
				$correction = true;
			}
		}
		if($correction){
			$image_finale = imagecreatetruecolor($newLargeur, $newHauteur);
			imagealphablending($image_finale, false);
			imagesavealpha($image_finale, true);
			$transtuteur = imagecolorallocatealpha($image_finale, 255, 255, 255, 127);
			imagefilledrectangle($image_finale, 0, 0, $newLargeur, $newHauteur, $transtuteur);
			if(imagecopyresampled($image_finale, $this->photo_source, 0, 0, 0, 0, $newLargeur, $newHauteur, $dim_image[0], $dim_image[1])){
				$this->photo_source = $image_finale;
				return true;
			}
		}
		return false;
	}
	
	// Ajoute le image à la photo. L'image doit être plus petite que la photo.
	public function addImage($new_image_source, $top = null, $right = null, $bottom = null, $left = null){
		$new_image = $new_image_source;
		$largeur_source = imagesx($this->photo_source);
		$hauteur_source = imagesy($this->photo_source);
		$largeur_image = imagesx($new_image);
		$hauteur_image = imagesy($new_image);
		
		if($largeur_image <= $largeur_source and $hauteur_image <= $hauteur_source){
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
			if(imagecopy($this->photo_source, $new_image, $pos_x, $pos_y, 0, 0, $largeur_logo, $hauteur_logo))
				return true;
		}
		return false;
	}
}