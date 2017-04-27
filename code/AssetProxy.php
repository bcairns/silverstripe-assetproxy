<?php

/**
 * Class AssetProxy
 * Controller extension, handles requests for non-existent assets, tries to fetch from AssetProxy host
 */
class AssetProxy extends Extension
{

	public function getHost(){
		return defined( 'ASSETPROXY_HOST' ) ? ASSETPROXY_HOST : false;
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

}
