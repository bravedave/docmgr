<?php
/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\docmgr;

if ( class_exists('application')) {
	class launcher extends \application {
		function __construct( $rootPath) {
			$this->defaultController = 'dvc\docmgr\controller';
			parent::__construct( $rootPath);

		}

		static function run( $dir = null) {
			new self( $dir ? $dir : dirname( __DIR__));

		}

	}

}
else {
	class launcher {
		static function run() {
			/**
			 * Yeah - the Minimum Viable Product
			 */
			header( 'Content-Type: text/plain');
			print \file_get_contents( __DIR__ . '/readme.txt');

		}

	}

}
