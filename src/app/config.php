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

class config extends \config {}

config::route_register( 'home', 'dvc\docmgr\controller');
\sys::logger( sprintf('%s : %s', 'set path for home', __METHOD__));
