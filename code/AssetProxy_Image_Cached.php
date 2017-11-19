<?php
/**
 * Without this class, Injector uses the constructor for AssetProxy_Image for new instances of Image_Cached, which breaks
 */
class AssetProxy_Image_Cached extends Image_Cached {

	/**
	 * Create a new cached image.
	 * @param string $filename The filename of the image.
	 * @param boolean $isSingleton This this to true if this is a singleton() object, a stub for calling methods.
	 *                             Singletons don't have their defaults set.
	 */
	public function __construct($filename = null, $isSingleton = false, Image $sourceImage = null) {
		parent::__construct($filename, $isSingleton, $sourceImage);
	}

}
