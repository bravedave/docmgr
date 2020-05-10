<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class hello extends Controller {
	protected function posthandler() {
		$action = $this->getPost('action');

		if ( 'gibblegok' == $action) { \Json::ack( $action); }
		else { \Json::nak( $action); }

	}

	protected function _index() {
		$this->render([
			'title' => 'hello world',
			'primary' => 'hello',
			'secondary' =>'index'
		]);

	}

	function tictactoe() {
		$this->modal([
			'title' => 'tic tac toe',
			'load' => 'tictactoe',
		]);

	}

	function info() {
		/* default setting
		 * in case you forget to disable this on a production server
		 * - only running on localhost
		 */
		if ( $this->Request->ServerIsLocal()) {
			$this->render([
				'title' => 'hello world',
				'primary' => 'info',
				'secondary' =>'blank'
			]);

		}

	}

}
