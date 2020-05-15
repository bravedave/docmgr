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

use DirectoryIterator;

class folders {
    protected $_folders = [];

    static function Create( string $folder) : bool {
        $path = implode( DIRECTORY_SEPARATOR, [
            config::docmgr_Folders(),
            $folder

        ]);

		if ( ! is_dir( $path)) {
			mkdir( $path);
			chmod( $path, 0777 );

		}

        return is_dir( $path);

    }

    static function Iterator() {
        return new DirectoryIterator( config::docmgr_Folders());

    }

    function __construct() {
        $iterator = self::Iterator();

        $this->_folders = [];

        foreach ( $iterator as $fileInfo) {
            if($fileInfo->isDot()) continue;

            $this->_folders[] = $fileInfo->getFilename();

        }

    }

    function get() {
        return $this->_folders;

    }

    function getFiles( string $folder) : array {
        $dao = new dao\docmgr;
        return $dao->getOfFolder( $folder);

    }

}
