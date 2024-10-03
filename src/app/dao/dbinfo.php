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

use bravedave\dvc\{dbinfo as dvcDbInfo, logger};

class dbinfo extends dvcDbInfo {
	/*
	 * it is probably sufficient to copy this file into the <application>/app/dao folder
	 *
	 * from there store you structure files in <application>/dao/db folder
	 */
	protected function check() {

		logger::info(sprintf('<%s> %s', 'runnning', __METHOD__));

		parent::check();
		parent::checkDIR(__DIR__);
	}
}
