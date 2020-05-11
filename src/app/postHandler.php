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
use dvc\Json;

abstract class postHandler {
    protected static $_user = null;

    static public function user( $user ) {
        self::$_user = $user;

    }

    static public function Upload() : Json {
        $debug = false;
        $debug = true;

        $response = __METHOD__;
        $uploads = [];

        if ( $_FILES) {

            $path = implode( DIRECTORY_SEPARATOR, [
                config::docmgr_Path(),
                date('Y')

            ]);

            if ( !is_dir( $path)) {
                mkdir($path, 0777);

            }

            foreach ($_FILES as $file) {
                # code...
                if ( $file['error'] == UPLOAD_ERR_INI_SIZE ) {
                    $response = sprintf( '%s :: %s is too large (ini)', __METHOD__, $file['name']);

                }
                elseif ( $file['error'] == UPLOAD_ERR_FORM_SIZE ) {
                    $response = sprintf( '%s :: %s is too large (form)', __METHOD__, $file['name']);

                }
                elseif ( is_uploaded_file( $file['tmp_name'] )) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                    $strType = finfo_file($finfo, $file['tmp_name']);

                    if ( $debug) \sys::logger( sprintf( '%s (%s) : %s', $file['name'], $strType, __METHOD__));

                    $ok = true;

                    if ( in_array( $strType, config::$DOCMGR_ACCEPT )) {
                        $source = $file['tmp_name'];
                        $_target = sprintf( '%s.%s', date('Y-m-d_his'), preg_replace( '@^[a-z]*/@', '', $strType));
                        $target = sprintf( '%s/%s', $path, $_target);

                        if ( file_exists( $target )) {
                            unlink( $target );

                        }

                        if ( move_uploaded_file( $source, $target)) {
                            chmod( $target, 0666 );

                            $uploads[] = $_target;
                            $a = [
                                'path' => $path . DIRECTORY_SEPARATOR,
                                'file' => $_target,
                                'name' => $file['name'],
                                'uploaded' => \db::dbTimeStamp(),
                                'updated' => \db::dbTimeStamp(),
                                'user_id' => self::$_user->id

                            ];

                            $dao = new dao\docmgr;
                            $dao->Insert( $a);

                        }

                    }
                    elseif ( $strType == "" ) {
                        $response = sprintf( '%s :: invalid file type : %s', __METHOD__, print_r( $file, true));

                    }
                    else {
                        $response = sprintf( 'upload: %s file type not permitted - %s', $file['name'], $strType);

                    }

                }	// elseif ( is_uploaded_file( $file['tmp_name'] ))
                else {
                    $response = sprintf( '%s :: not :: is_uploaded_file( %s)', __METHOD__, print_r( $file, true));

                }

            }	// elseif ( is_uploaded_file( $file['tmp_name'] ))

        }

        if ( $uploads) {
            return Json::ack( __METHOD__)
                ->add( 'data', $uploads);

        }

        return Json::nak( $response);

    }

}