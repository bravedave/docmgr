<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\docmgr\dao;

use sys;

$dbc = sys::dbCheck('docmgr');

$dbc->defineField('file', 'varbinary');
$dbc->defineField('path', 'varbinary', 256);
$dbc->defineField('name', 'varchar', 256);
$dbc->defineField('uploaded', 'datetime');
$dbc->defineField('updated', 'datetime');
$dbc->defineField('folder', 'varchar');
$dbc->defineField('tags', 'varchar', 256);
$dbc->defineField('user_id', 'bigint');
$dbc->defineField('property_id', 'bigint');
$dbc->defineField('filed', 'tinyint');

$dbc->defineIndex('file', 'file');
$dbc->check();
