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
use currentUser;
use Json;
use sys;

class controller extends \Controller {

	protected $label = config::label;

	protected function before() {
		config::docmgr_checkdatabase();
		parent::before();

		// sys::logger( sprintf('<%s> %s', 'hear me !', __METHOD__));

	}

	protected function getView( $viewName = 'index', $controller = null, $logMissingView = true) {
		$view = sprintf( '%s/views/%s.php', __DIR__, $viewName );		// php
		if ( file_exists( $view))
			return ( $view);

		return parent::getView( $viewName, $controller, $logMissingView);

	}

	protected function posthandler() {
		$action = $this->getPost('action');

		if ( 'explode' == $action) {
			if ( $file = $this->getPost( 'file')) {
				$docmgrDao = new dao\docmgr;
				if ( $dto = $docmgrDao->getByFileName( $file)) {
					$src = $dto->path . $dto->file;
					//~ sys::logger( $src);

					$pdf = new setasign\Fpdi\Fpdi;
					$pagecount = $pdf->setSourceFile( $src); // How many pages?
					$error = false;

					// Split each page into a new PDF
					for ($i = 1; $i <= $pagecount; $i++) {
						$new_pdf = new setasign\Fpdi\Fpdi;
						$new_pdf->AddPage();
						$new_pdf->setSourceFile( $src);
						$new_pdf->useTemplate( $new_pdf->importPage( $i));

						try {
							$new_filename = sprintf( '%s-%s.pdf', str_replace( '.pdf', '', $dto->file), $i);
							$new_pdf->Output( $dto->path . $new_filename, 'F');
							$new_pdf->close();

							chmod( $dto->path . $new_filename, 0666 );
							$a = [
								'path' => $dto->path,
								'file' => $new_filename,
								'uploaded' => \db::dbTimeStamp(),
								'updated' => \db::dbTimeStamp(),
								'user_id' => currentUser::id()];
							$docmgrDao->Insert( $a);

							sys::logger( sprintf( 'docmgr/posthandler("explode") => page $s.%d split into %s', $dto->file, $i, $new_filename));

						}
						catch ( Exception $e) {
							sys::logger( sprintf( 'docmgr/posthandler("explode") exception: %s',  $e->getMessage()));
							$error = true;

						}

					}

					$pdf->close();
					if ( !$error) {
						$docmgrDao->UpdateByID( ['filed' => 1], $dto->id);

					}

					\Json::ack( $action);

				} else { \Json::nak( $action); }

			} else { \Json::nak( $action); }

		}
		elseif ( 'filed' == $action) {
			if ( $id = (int)$this->getPost('id')) {
				$dao = new dao\docmgr;
				if ( $dto = $dao->getByID( $id)) {
					$dao->UpdateByID( ['filed' => 1], $id);
					Json::ack( $action);

				} else { Json::nak( $action); }

			} else { Json::nak( $action); }

		}
		elseif ( 'property-set' == $action) {
			if ( $id = (int)$this->getPost('id')) {
				if ( $pid = (int)$this->getPost('property_id')) {
					$dao = new dao\docmgr;
					if ( $dto = $dao->getByID( $id)) {
						$dao->UpdateByID( ['property_id' => $pid], $id);
						Json::ack( $action);

					} else { Json::nak( $action); }

				} else { Json::nak( $action); }

			} else { Json::nak( $action); }

		}
		elseif ( 'get-queue' == $action) {
			$dao = new dao\docmgr;
			\Json::ack( $action)->add('data', $dao->queue());

		}
		elseif ( 'tag-add' == $action) {
			if ( $id = (int)$this->getPost('id')) {
				if ( $tag = $this->getPost( 'tag')) {
					$dao = new dao\docmgr;
					if ( $dto = $dao->getByID( $id)) {
						$dao->addTag( $dto, $tag);

						Json::ack( $action);

					} else { Json::nak( $action); }

				} else { Json::nak( $action); }

			} else { Json::nak( $action); }

		}
		elseif ( 'tag-delete' == $action) {
			if ( $id = (int)$this->getPost('id')) {
				if ( $tag = $this->getPost( 'tag')) {
					$dao = new dao\docmgr;
					if ( $dto = $dao->getByID( $id)) {
						$dao->deleteTag( $dto, $tag);

						Json::ack( $action);

					} else { Json::nak( $action); }

				} else { Json::nak( $action); }

			} else { Json::nak( $action); }

		}
		elseif ( 'upload' == $action) {
			postHandler::user( currentUser::user());
			postHandler::upload();	// generates Json response

		}
		else { Json::nak( $action); }

	}

	protected function _index() {
		$dao = new dao\docmgr;
		if ( $file = $this->getParam( 'f')) {
			if ( $dto = $dao->getByFileName( $file)) {
				if ( 'serve' == $this->getParam( 'v')) {
					//~ print $dto->path . $dto->file;
					sys::serve( $dto->path . $dto->file);

				}
				else {
					$this->data = (object)[
						'dto' => $dto

					];

					if ( 'handler' == $this->getParam( 'v')) {
						// \sys::logger( sprintf('<%s> %s', $this->route, __METHOD__));
						$this->load( 'handler');

					}
					elseif ( 'tags' == $this->getParam( 'v')) {
						$this->load( 'tags');

					}
					else {
						$this->load( 'viewer');

					}

				}

			}

		}
		else {
			$this->render([
				'title' => $this->title = $this->label,
				'primary' => 'blank',
				'secondary' => ['index','uploader','queue']]);

		}

	}

}