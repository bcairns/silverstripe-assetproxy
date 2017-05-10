<?php

/**
 * Class AssetProxy_Parser
 * Extends ShortcodeParser, looks for links to /assets/ in HTML fields and ensures their directories exist
 */
class AssetProxy_Parser extends Extension
{

	/**
	 * We have to do this prior to the actual HTTP request, otherwise we get 403 instead of 404 for non-existent dirs
	 * @param $rootPath string root-relative path of file, eg "/assets/Uploads/foo.jpg"
	 */
	protected static function ensureDirectoryExists($rootPath){
		$dirPath = dirname( Director::baseFolder() . $rootPath );
		if( !file_exists($dirPath) ){
			Filesystem::makeFolder($dirPath);
		}
	}

	public function onAfterParse($content){

		if( AssetProxy::getHost() ){
			// find images and files linked to /assets/, ensure their directory exists
			preg_match_all(
				'@(["|\'])/?(assets/.+?)\\1@',
				$content,
				$matches
			);
			foreach( $matches[2] as $match ){
				self::ensureDirectoryExists('/'.$match);
			}
		}

	}

}
