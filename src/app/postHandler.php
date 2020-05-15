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
        // $debug = true;

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

            $secs = 0;
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
                        $_target = sprintf( '%s.%s.%s', date('Y-m-d_his'), $secs++, preg_replace( '@^[a-z]*/@', '', $strType));
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

                            /* special filter */
                            $a = (function( array $a) : array {
                                $debug = false;
                                $debug = true;

                                if ( !\class_exists( '\dao\properties')) return $a;
                                // \sys::logger( sprintf('<%s> %s', 'got class', __METHOD__));

                                if ( preg_match( '/^ComplianceCert_/', $a[ 'name'])) {
                                    if ( preg_match( '/[0-9]{8}\.(pdf|jpg|tiff)$/', $a[ 'name'])) {
                                        // then we are left with - 1_15_Trackson_Street_
                                        // this is a smoke alarm for 1/15 Trackson Street
                                        $street = trim( preg_replace([
                                            '/^ComplianceCert_/',
                                            '/[0-9]{8}\.(pdf|jpg|tiff)$/i'

                                        ], '', $a[ 'name']), ' _');

                                        $_street = preg_replace( '/^[0-9_\-]*/', '', $street );
                                        if ( $debug) \sys::logger( sprintf('<%s> %s', $_street, __METHOD__));

                                        $_streetNo = trim( str_replace( $_street, '', $street ), ' _');
                                        if ( $debug) \sys::logger( sprintf('<no.%s> %s', $_streetNo, __METHOD__));
                                        $_streetNo = str_replace( '_', '/', $_streetNo );
                                        $_street = str_replace( '_', ' ', $_street );

                                        if ( $_streetNo && $_street) {
                                            $_search = $_streetNo . ' ' . $_street;
                                            $dao = new \dao\properties;
                                            if ( $dto = $dao->getPropertyByStreet( $_search)) {
                                                $a['property_id'] = $dto->id;
                                                $a['folder'] = \json_encode( ['SmokeAlarm']);
                                                $a['tags'] = \json_encode( ['Smoke Alarm']);
                                                if ( $debug) \sys::logger( sprintf('<%s = %s> %s', $_search, $dto->address_street, __METHOD__));

                                            }
                                            elseif ( $debug) {
                                                \sys::logger( sprintf('<%s> - not found - %s', $_search, __METHOD__));

                                            }

                                        }
                                        elseif ( $debug) {
                                            \sys::logger( sprintf('<%s %s> %s', $_streetNo, $_street, __METHOD__));

                                        }

                                    }
                                    elseif ( $debug) {
                                        \sys::logger( sprintf('<%s> : end : %s', $a[ 'name'], __METHOD__));

                                    }

                                }
                                elseif ( $debug) {
                                    \sys::logger( sprintf('<%s> : start : %s', $a[ 'name'], __METHOD__));

                                }

                                return $a;

                            })( $a);
                            /* special filter */

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