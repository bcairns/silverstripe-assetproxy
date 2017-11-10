<?php

/**
 * Class AssetProxy
 * Controller extension, handles requests for non-existent assets, tries to fetch from AssetProxy host
 */
class AssetProxy extends Extension
{

	public static function getHost(){
		return defined( 'ASSETPROXY_SOURCE' ) ? ASSETPROXY_SOURCE : false;
	}

	public static function ensureDirectoryExists($path){
		$dirPath = dirname( Director::baseFolder() . '/' . $path );
		if( !file_exists($dirPath) ){
			Filesystem::makeFolder($dirPath);
		}
	}

	public static function copyFromSource($path){
		return self::getHost() && copy( self::getHost() . '/' . $path, '../' . $path );
	}

	public function onBeforeHTTPError404($request){
		$path = $request->getURL();
		if( substr($path,0,7) == 'assets/' && self::copyFromSource($path) ){
			$relPath = '../'.$path; // path relative to cwd
			header('Content-Type: '.HTTP::get_mime_type( $relPath ) );
			header('Content-Length: '.filesize( $relPath ) );
			readfile( $relPath );
			exit;
		}
	}

}
