<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao;

$dbc = 'sqlite' == \config::$DB_TYPE ?
	new \dvc\sqlite\dbCheck( $this->db, 'docmgr' ) :
	new \dao\dbCheck( $this->db, 'docmgr' );

$dbc->defineField( 'file', 'varbinary');
$dbc->defineField( 'path', 'varbinary', 256);
$dbc->defineField( 'name', 'varchar', 256);
$dbc->defineField( 'uploaded', 'datetime');
$dbc->defineField( 'updated', 'datetime');
$dbc->defineField( 'tags', 'varchar', 256);
$dbc->defineField( 'user_id', 'bigint');
$dbc->defineField( 'filed', 'tinyint');

$dbc->defineIndex('file', 'file' );

$dbc->check();
