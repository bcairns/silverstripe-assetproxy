<?php

/**
 * Class AssetProxy
 * Controller extension, handles requests for non-existent assets, tries to fetch from AssetProxy host
 */
class AssetProxy extends Extension
{

	public static function getHost(){
		return defined( 'ASSETPROXY_HOST' ) ? ASSETPROXY_HOST : false;
	}

	public static function ensureDirectoryExists($rootPath){
		$dirPath = dirname( Director::baseFolder() . $rootPath );
		if( !file_exists($dirPath) ){
			Filesystem::makeFolder($dirPath);
		}
	}

	public function onBeforeHTTPError404($request){
		$url = $request->getURL();
		if( substr($url,0,7) == 'assets/' && ( $host = self::getHost() ) ){
			$dest = '../' . $url;
			$source = $host . '/' . $url;
			if( copy( $source, $dest ) ){
				header('Content-Type: '.HTTP::get_mime_type( $dest ) );
				header('Content-Length: '.filesize( $dest ) );
				readfile( $dest );
				exit;
			}
		}
	}

	public function onAfterInit(){

		$owner = $this->owner;
		if( $owner->hasMethod('inheritedDatabaseFields') ){
			$fields = $owner->inheritedDatabaseFields();
			foreach( $fields as $field => $type ){
				if( $type === 'ForeignKey' && $field !== 'ParentID' ){
					$obj = $owner->obj(substr($field,0,-2));
					if( $obj instanceof File ){
						self::ensureDirectoryExists('/'.$obj->Filename);

						// todo: aggressive mode, fetch image immediately
					}

				}
			}
		}

	}

}
