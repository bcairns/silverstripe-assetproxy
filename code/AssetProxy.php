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

	public function isAggressiveMode($file){
		return defined('ASSETPROXY_AGGRESSIVE') ? ASSETPROXY_AGGRESSIVE : true;
	}

	public function onAfterInit(){

		if( self::getHost() && $this->owner instanceof ContentController ){
			$this->checkObject( $this->owner->data() );
		}

	}

	/**
	 * Check given DataObject for File fields
	 * Recursively checks $has_many relationships
	 * @param $object
	 */
	protected function checkObject($object){

		if( $object->hasMethod('inheritedDatabaseFields') ){

			$fields = $object->inheritedDatabaseFields();

			foreach( $fields as $fieldname => $type ){

				if( $type === 'ForeignKey' && $fieldname !== 'ParentID' ){
					$field = $object->obj(substr($fieldname,0,-2));
					if( $field instanceof File && ($path = $field->getFullPath()) && !file_exists($path) ){

						self::ensureDirectoryExists('/'.$field->Filename);

						// aggressive mode, fetch image immediately
						if( $this->isAggressiveMode($field) ){
							$source = self::getHost() . '/' . $field->getFilename();
							copy( $source, $path );
						}

					}
				}

			}

		}

		$has_many = $object->config()->get('has_many');
		if( is_array( $has_many ) ){
			foreach( $has_many as $field => $type ){
				foreach( $object->$field() as $child ){
					$this->checkObject($child);
				}
			}
		}

	}

}
