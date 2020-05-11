<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\docmgr;

class config extends \config {
	const docmgr_db_version = 0.02;

    const label = 'Store and Retrieve';

	static protected $_DOCMGR_VERSION = 0;

	static $DOCMGR_ACCEPT = [
		'image/png',
		'image/x-png',
		'image/jpeg',
		'image/pjpeg',
		'image/tiff',
		'image/gif',
		'text/plain',
		'application/pdf'

	];

	static protected function docmgr_version( $set = null) {
		$ret = self::$_DOCMGR_VERSION;

		if ( (float)$set) {
			$config = self::docmgr_config();

			$j = file_exists( $config) ?
				json_decode( file_get_contents( $config)):
				(object)[];

			self::$_DOCMGR_VERSION = $j->docmgr_version = $set;

			file_put_contents( $config, json_encode( $j, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

		}

		return $ret;

	}

	static function docmgr_checkdatabase() {
		if ( self::docmgr_version() < self::docmgr_db_version) {
			config::docmgr_version( self::docmgr_db_version);

			$dao = new dao\dbinfo;
			$dao->dump( $verbose = false);

		}

		// sys::logger( 'bro!');

	}

	static function docmgr_config() {
		return implode( DIRECTORY_SEPARATOR, [
            rtrim( self::dataPath(), '/ '),
            'docmgr.json'

        ]);

	}

    static function docmgr_init() {
		if ( file_exists( $config = self::docmgr_config())) {
			$j = json_decode( file_get_contents( $config));

			if ( isset( $j->docmgr_version)) {
				self::$_DOCMGR_VERSION = (float)$j->docmgr_version;

			};

		}


	}

	static function docmgr_Path() {
		$path = implode( DIRECTORY_SEPARATOR, [
			trim( self::dataPath(), '/'),
			'docMgr'

		]);

		if ( ! is_dir( $path)) {
			mkdir( $path);
			chmod( $path, 0777 );

		}

		return $path;

	}

}

config::docmgr_init();
