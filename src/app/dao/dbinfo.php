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

use bravedave;

class dbinfo extends bravedave\dvc\dbinfo {
  protected $_store = '';

  protected function check() {

    $this->checkDIR(__DIR__);
  }
}
