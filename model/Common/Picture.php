<?php
namespace Common;

use GdImage;

/**
 * Tools to transform pictures.
 */
class Picture
{
	// ==== ATTRIBUTS ====
	/**
	 * @var string $_path Path of the picture.
	 */
	private string $_path = '';
	
	/**
	 * @var string $_name Name of the file without extension.
	 */
	private string $_name = '';
	
	/**
	 * @var string $_ext Extension of the file.
	 */
	private string $_ext = '';
	
	/**
	 * @var GdImage|null $_source Content (source) of the picture.
	 */
	private ?GdImage $_source = null;
	
	
	// ==== GETTER ====
	/**
	 * Return the content of the picture.
	 * 
	 * @return GdImage|false Return False if the content is empty.
	 */
	public function getSource(): GdImage|false
	{
		if ($this->_source !== null) {
			return $this->_source;
		}
		
		return false;
	}
	
	/**
	 * Return the width of the picture.
	 * 
	 * @return int|false Return False if the file is not define.
	 */
	public function getWidth(): int|false
	{
		if ($this->_source !== null) {
			return imagesx($this->_source);
		}

		return false;
	}
	
	/**
	 * Return the height of the picture.
	 * 
	 * @return int|false Return False if the file is not define.
	 */
	public function getHeight(): int|false
	{
		if ($this->_source !== null) {
			return imagesy($this->_source);
		}

		return false;
	}

	/**
	 * Return the full path of the picture.
	 * 
	 * @return int|false Return False if the path cannot be build.
	 */
	public function getFullpath(): string|false
	{
		if ($this->_path !== '' && $this->_name !== null && $this->_ext !== null) {
			return $this->_path . '/' . $this->_name . $this->_ext;
		}

		return false;
	}

	// ==== SETTER ====
	/**
	 * Define if the picture must be interlace or not.
	 * 
	 * @param bool $isInterlace
	 * @return bool Return True if the process succeed, else False.
	 */
	public function setInterlace(bool $isInterlace): bool
	{
		if ($this->_source !== null) {
			return imageinterlace($this->_source, $isInterlace);
		}

		return false;
	}

	// ==== PUBLIC METHODS ====
	/**
	 * Load the picture from a file.
	 * 
	 * @param string $path Path of the file to load.
	 * @return bool Return True if the load succeed, else False.
	 */
	public function loadFromFilePath(string $path): bool
	{
		$this->readPath($path);

		switch ($this->_ext) {
			case '.bmp':
				$this->_source = imagecreatefrombmp($path);
				break;
				
			case '.wbmp':
				$this->_source = imagecreatefromwbmp($path);
				break;
			
			case '.png':
				$this->_source = imagecreatefrompng($path);
				break;
				
			case '.jpg':
			case '.jpeg':
				$this->_source = imagecreatefromjpeg($path);
				break;
				
			case '.gif':
				$this->_source = imagecreatefromgif($path);
				break;
			
			case '.gd':
				$this->_source = imagecreatefromgd($path);
				break;
				
			case '.gd2':
				$this->_source = imagecreatefromgd2($path);
				break;
				
			case '.webp':
				$this->_source = imagecreatefromwebp($path);
				break;
				
			case '.xbm':
				$this->_source = imagecreatefromxbm($path);
				break;
				
			case '.xpm':
				$this->_source = imagecreatefromxpm($path);
				break;

			default:
				return false;
		}
		
		return true;
	}
	
	/**
	 * Load the picture from a content string.
	 * 
	 * @param string $content Raw data of the file in string.
	 * @return bool Return True if the load succeed, else False.
	 */
	public function loadFromFileContent(string $content): bool
	{
		$source = imagecreatefromstring($content);

		if ($source === false) {
			return false;
		}
		
		$this->_source = $source;
		return true;
	}

	/**
	 * Save the picture into a file.
	 * 
	 * @param string $ouputPath Path where the file must be saved.
	 * @param int $quality Quality of the picture (compression) if the file must be a JPG (from 0 to 100).
	 * @return bool Return True if the save succeed, else False.
	 */
	public function savePicture(string $ouputPath, int $quality = 100): bool
	{
		$this->readPath($ouputPath);
		
		if ($this->_source === null) {
			return false;
		}

		switch ($this->_ext) {
			case ".bmp":
				return imagebmp($this->_source, $ouputPath);
				
			case ".wbmp":
				return imagewbmp($this->_source, $ouputPath);
			
			case ".png":
				return imagepng($this->_source, $ouputPath, $quality);
				
			case ".jpg":
			case ".jpeg":
				return imagejpeg($this->_source, $ouputPath, $quality);
				
			case ".gif":
				return imagegif($this->_source, $ouputPath);
				
			case ".gd":
				return imagegd($this->_source, $ouputPath);
				
			case ".gd2":
				return imagegd2($this->_source, $ouputPath);
				
			case ".webp":
				return imagewebp($this->_source, $ouputPath);
				
			case ".xbm":
				return imagexbm($this->_source, $ouputPath);

			default:
				return false;
		}
	}
	
	/**
	 * Resize the picture proportionnaly to its width.
	 * 
	 * @param int $newWidth New width for the picture.
	 * @return bool Return True if the load succeed, else False.
	 */
	public function resizeWidth(int $newWidth): bool
	{
		if ($this->_source === null) {
			return false;
		}

		$currentWidth = imagesx($this->_source);
		$currentHeight = imagesy($this->_source);
		
		$newHeight = $currentHeight * $newWidth / $currentWidth;
		
		return $this->resize($newWidth, (int)$newHeight);
	}
	
	/**
	 * Resize the picture proportionnaly to its height.
	 * 
	 * @param int $newHeight New height for the picture.
	 * @return bool Return True if the load succeed, else False.
	 */
	public function resizeHeight(int $newHeight): bool
	{
		if ($this->_source === null) {
			return false;
		}

		$currentWidth = imagesx($this->_source);
		$currentHeight = imagesy($this->_source);
		
		$newWidth = $currentWidth * $newHeight / $currentHeight;
		
		return $this->resize((int)$newWidth, $newHeight);

	}
	
	/**
	 * Resize the picture.
	 * 
	 * @param int $newWidth New height for the picture.
	 * @param int $newHeight New height for the picture.
	 * @return bool Return True if the load succeed, else False.
	 */
	public function resize(int $newWidth, int $newHeight): bool
	{
		if ($this->_source === null) {
			return false;
		}

		$currentWidth = imagesx($this->_source);
		$currentHeight = imagesy($this->_source);
		
		$image_finale = imagecreatetruecolor($newWidth, $newHeight);
		imagealphablending($image_finale, false);
		imagesavealpha($image_finale, true);
		
		$transparent = imagecolorallocatealpha($image_finale, 255, 255, 255, 127);
		imagefilledrectangle($image_finale, 0, 0, $newWidth, $newHeight, $transparent);
		
		if (imagecopyresampled($image_finale, $this->_source, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight)) {
			$this->_source = $image_finale;
			return true;
		}

		return false;
	}

	/**
	 * Add an image on the picture.
	 * The image mus be smaller than the picture.
	 * 
	 * @param GdImage $newImageSource Image to add.
	 * @param int|null $top Top position of the image. If Null is set, the image is place on the top of the picture.
	 * @param int|null $right Right position of the image. If Null is set, the image width is concerved.
	 * @param int|null $bottom Bottom position of the image. If Null is set, the image height is concerved.
	 * @param int|null $left Left position of the image. If Null is set, the image is place on the left of the picture.
	 * @return bool Return True if the load succeed, else False.
	 */
	public function insertImage(GdImage $newImageSource, ?int $top = null, ?int $right = null, ?int $bottom = null, ?int $left = null): bool
	{
		if ($this->_source === null) {
			return false;
		}

		$newImage = $newImageSource;
		$largeur_source = imagesx($this->_source);
		$hauteur_source = imagesy($this->_source);
		$largeur_image = imagesx($newImage);
		$hauteur_image = imagesy($newImage);
		
		if ($largeur_image > $largeur_source || $hauteur_image > $hauteur_source) {
			return false;
		}

		// Placement de la nouvelle image par rapport à l'ancienne.
		$pos_x = 0;
		$pos_y = 0;
		
		if ($top !== null) {
			$pos_y = $top;
		} elseif ($bottom !== null) {
			$pos_y = $hauteur_source - $hauteur_image - $bottom;
		}
		
		if ($left !== null) {
			$pos_x = $left;
		} elseif ($right !== null) {
			$pos_x = $largeur_source - $largeur_image - $right;
		}

		// On met le logo (source) dans l'image de destination (la photo)
		return imagecopy($this->_source, $newImage, $pos_x, $pos_y, 0, 0, $largeur_image, $hauteur_image);
	}

	/**
	 * Save a copy of the picture
	 * 
	 * @param string $ouputPath Path where the file must be saved.
	 * @param int|null $maxSize Define the max size of the output picture.
	 * @param int $quality Quality of the picture (compression) if the file must be a JPG (from 0 to 100).
	 * @return bool Return True if the save succeed, else False.
	 */
	public function saveCopy(string $outputPath, ?int $maxSize = null, int $quality = 100): bool
	{
		$copy = clone $this;
		
		if ($maxSize !== null) {
			if ($copy->getWidth() > $copy->getHeight()) {
				$copy->resizeWidth($maxSize);
			} else {
				$copy->resizeHeight($maxSize);
			}
		}

		return $copy->savePicture($outputPath, $quality);
	}
	
	// ==== PRIVATE METHODS ====
	/**
	 * Read the path of a file to extract informations.
	 * 
	 * @param string $fullpath Path to analyse.
	 * @return void
	 */
	private function readPath(string $fullpath): void
	{
		$this->_name = strrchr($fullpath, '/');
		$this->_path = str_replace("/" . $this->_name, "", $fullpath);
		$this->_ext = mb_strtolower(strrchr($this->_name, '.'));
	}
}