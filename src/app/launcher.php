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
use dvc\application;

class launcher {
	static function run() {
		if ( class_exists('application')) {
			/**
			 * Extended example, uses an application directory structure
			 *
			 * To use this example, install bravedave/dvc
			 * 	composer require bravedave/dvc
			 *
			 * then review the folders
			 *  controller
			 *  app
			 */

			new application( dirname( __DIR__));

		}
		else {
			/**
			 * Yeah - the Minimum Viable Product
			 */
			header( 'Content-Type: text/plain');
			print \file_get_contents( __DIR__ . '/readme.txt');

		}

	}

}
