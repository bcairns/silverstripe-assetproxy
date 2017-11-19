<?php

/**
 * Class AssetProxy_Image
 * Drop-in Image replacement that will always output markup, and attempt proxy download for formatted image generation
 */
class AssetProxy_Image extends Image
{

	protected static $flush = false;

	/**
	 * @return true if Image exists (in DB, not on disk)
	 */
	public function exists(){
		return $this->recordExists();
	}

	/**
	 * @return true if this file exists in the DB
	 */
	public function recordExists(){
		return isset($this->record['ID']) && $this->record['ID'] > 0;
	}

	/**
	 * @return true if this file exists in the local filesystem
	 */
	public function fileExists(){
		return file_exists($this->getFullPath());
	}

	/**
	 * Return an XHTML img tag for this Image
	 *
	 * @return string
	 */
	public function getTag() {
		if ($this->recordExists()){
			AssetProxy::ensureDirectoryExists($this->Filename);
			$url = $this->getURL();
			$title = ($this->Title) ? $this->Title : $this->Filename;
			if($this->Title) {
				$title = Convert::raw2att($this->Title);
			} else {
				if(preg_match("/([^\/]*)\.[a-zA-Z0-9]{1,6}$/", $title, $matches)) {
					$title = Convert::raw2att($matches[1]);
				}
			}
			return "<img src=\"$url\" alt=\"$title\" />";
		}
	}

	public function getFilename() {
		$filename = parent::getFilename();
		AssetProxy::ensureDirectoryExists($filename);
		return $filename;
	}

	/**
	 * Return an image object representing the image in the given format.
	 * This image will be generated using generateFormattedImage().
	 * The generated image is cached, to flush the cache append ?flush=1 to your URL.
	 *
	 * Just pass the correct number of parameters expected by the working function
	 *
	 * @param string $format The name of the format.
	 * @return Image_Cached|null
	 */
	public function getFormattedImage($format) {
		$args = func_get_args();

		if(
			$this->recordExists() &&
			( file_exists($this->getFullPath()) || AssetProxy::copyFromSource($this->Filename) )
		) {

			$cacheFile = call_user_func_array(array($this, "cacheFilename"), $args);

			if(!file_exists(Director::baseFolder()."/".$cacheFile) || self::$flush) {
				call_user_func_array(array($this, "generateFormattedImage"), $args);
			}

			$cached = Injector::inst()->createWithArgs('Image_Cached', array($cacheFile, false, $this));
			return $cached;
		}
	}

	/**
	 * Get the dimensions of this Image.
	 * @param string $dim If this is equal to "string", return the dimensions in string form,
	 * if it is 0 return the height, if it is 1 return the width.
	 * @return string|int|null
	 */
	public function getDimensions($dim = "string") {
		if($this->getField('Filename')) {

			$imagefile = $this->getFullPath();
			if( $this->fileExists() ) {
				$size = getimagesize($imagefile);
				return ($dim === "string") ? "$size[0]x$size[1]" : $size[$dim];
			} else {
				return ($dim === "string") ? "file '$imagefile' not found" : null;
			}
		}
	}

}
