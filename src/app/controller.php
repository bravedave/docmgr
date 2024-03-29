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
use bravedave\dvc\json;
use Exception;
use setasign\Fpdi\Fpdi;
use strings;
use sys;

class controller extends \Controller {

	protected $label = config::label;

	protected function before() {

		config::docmgr_checkdatabase();

		parent::before();
		$this->viewPath[] = __DIR__ . '/views/';
	}

	protected function posthandler() {
		$action = $this->getPost('action');

		if ('explode' == $action) {
			if ($file = $this->getPost('file')) {
				$docmgrDao = new dao\docmgr;
				if ($dto = $docmgrDao->getByFileName($file)) {
					$src = $dto->path . $dto->file;
					//~ sys::logger( $src);

					$pdf = new Fpdi;
					$pagecount = $pdf->setSourceFile($src); // How many pages?
					$error = false;

					// Split each page into a new PDF
					for ($i = 1; $i <= $pagecount; $i++) {
						$new_pdf = new Fpdi;
						$new_pdf->AddPage();
						$new_pdf->setSourceFile($src);
						$new_pdf->useTemplate($new_pdf->importPage($i));

						try {
							$new_filename = sprintf('%s-%s.pdf', str_replace('.pdf', '', $dto->file), $i);
							$new_pdf->Output($dto->path . $new_filename, 'F');
							$new_pdf->close();

							chmod($dto->path . $new_filename, 0666);
							$a = [
								'path' => $dto->path,
								'file' => $new_filename,
								'uploaded' => \db::dbTimeStamp(),
								'updated' => \db::dbTimeStamp(),
								'user_id' => currentUser::id()
							];
							$docmgrDao->Insert($a);

							sys::logger(sprintf('docmgr/posthandler("explode") => page $s.%d split into %s', $dto->file, $i, $new_filename));
						} catch (Exception $e) {
							sys::logger(sprintf('docmgr/posthandler("explode") exception: %s',  $e->getMessage()));
							$error = true;
						}
					}

					$pdf->close();
					if (!$error) {
						$docmgrDao->UpdateByID(['filed' => 1], $dto->id);
					}

					json::ack($action);
				} else {
					json::nak($action);
				}
			} else {
				json::nak($action);
			}
		} elseif ('delete' == $action) {
			if ($id = (int)$this->getPost('id')) {
				$dao = new dao\docmgr;
				$dao->delete($id);
				json::ack($action);
			} else {
				json::nak($action);
			}
		} elseif ('file-selected' == $action) {
			if ($_ids = $this->getPost('ids')) {

				$done = 0;
				$ids = explode(',', $_ids);
				$dao = new dao\docmgr;
				foreach ($ids as $id) {
					if ($id = (int)$id) {
						if ($dto = $dao->getByID($id)) {
							$dao->UpdateByID(['filed' => 1], $id);
							$done++;
						}
					}
				}

				if ($done) {
					json::ack($action)
						->add('count', $done);
				} else {
					json::nak($action);
				}
			} else {
				json::nak($action);
			}
		} elseif ('filed' == $action) {
			if ($id = (int)$this->getPost('id')) {
				$dao = new dao\docmgr;
				if ($dto = $dao->getByID($id)) {
					$dao->UpdateByID(['filed' => 1], $id);
					json::ack($action);
				} else {
					json::nak($action);
				}
			} else {
				json::nak($action);
			}
		} elseif ('folder-create' == $action) {
			if ($folder = $this->getPost('folder')) {
				if (!preg_match('/[^0-9a-z]/i', $folder)) {
					if (folders::Create($folder)) {
						json::ack($action);
					} else {
						json::nak($action);
					}
				} else {
					json::nak($action);
				}
			} else {
				json::nak($action);
			}
		} elseif ('folder-get-files' == $action) {
			$folders = new folders;
			if ($folder = $this->getPost('folder')) {
				json::ack($action)
					->add('data', $folders->getFiles($folder));
			} else {
				json::nak($action);
			}
		} elseif ('folders-get' == $action) {
			$folders = new folders;
			json::ack($action)
				->add('data', $folders->get());
		} elseif ('property-set' == $action) {
			if ($id = (int)$this->getPost('id')) {
				if ($pid = (int)$this->getPost('property_id')) {
					$dao = new dao\docmgr;
					if ($dto = $dao->getByID($id)) {
						$dao->UpdateByID(['property_id' => $pid], $id);
						json::ack($action);
					} else {
						json::nak($action);
					}
				} else {
					json::nak($action);
				}
			} else {
				json::nak($action);
			}
		} elseif ('get-queue' == $action) {
			$dao = new dao\docmgr;
			json::ack($action)->add('data', $dao->queue());
		} elseif ('set-folder' == $action) {
			if ($id = (int)$this->getPost('id')) {
				$folder = $this->getPost('folder');
				$dao = new dao\docmgr;

				if ($dto = $dao->getByID($id)) {
					$dao->UpdateByID(['folder' => $folder], $dto->id);
					json::ack($action);
				} else {
					json::nak($action);
				}
			} else {
				json::nak($action);
			}
		} elseif ('tag-add' == $action) {
			if ($id = (int)$this->getPost('id')) {
				if ($tag = $this->getPost('tag')) {
					$dao = new dao\docmgr;
					if ($dto = $dao->getByID($id)) {
						$dao->addTag($dto, $tag);

						json::ack($action);
					} else {
						json::nak($action);
					}
				} else {
					json::nak($action);
				}
			} else {
				json::nak($action);
			}
		} elseif ('tag-delete' == $action) {
			if ($id = (int)$this->getPost('id')) {
				if ($tag = $this->getPost('tag')) {
					$dao = new dao\docmgr;
					if ($dto = $dao->getByID($id)) {
						$dao->deleteTag($dto, $tag);

						json::ack($action);
					} else {
						json::nak($action);
					}
				} else {
					json::nak($action);
				}
			} else {
				json::nak($action);
			}
		} elseif ('upload' == $action) {
			postHandler::user(currentUser::user());
			postHandler::upload();	// generates json response

		} else {
			json::nak($action);
		}
	}

	protected function _index() {
		$dao = new dao\docmgr;
		if ($file = $this->getParam('f')) {
			if ($dto = $dao->getByFileName($file)) {
				if ('serve' == $this->getParam('v')) {
					//~ print $dto->path . $dto->file;
					sys::serve($dto->path . $dto->file);
				} else {
					$this->data = (object)[
						'dto' => $dto

					];

					if ('handler' == $this->getParam('v')) {
						// \sys::logger( sprintf('<%s> %s', $this->route, __METHOD__));
						$this->load('handler');
						if (!$dto->filed) {
							$this->load('filer');
						}
					} elseif ('tags' == $this->getParam('v')) {
						$this->load('tags');
					} else {
						$this->load('viewer');
					}
				}
			}
		} else {
			$this->render([
				'title' => $this->title = $this->label,
				'primary' => 'blank',
				'secondary' => ['index', 'index-handler', 'uploader', 'queue']
			]);
		}
	}

	public function files() {
		if ($folder = $this->getParam('folder')) {
			$folders = new folders;
			$this->data = (object)[
				'title' => $folder,
				'dtoSet' => $folders->getFiles($folder)

			];

			$this->load('report');
		}
	}

	public function folders() {
		$this->data = (object)[
			'folders' => folders::Iterator()

		];

		$this->render([
			'title' => $this->title = $this->label,
			'primary' => 'folders',
			'secondary' => ['index']

		]);
	}

	public function report() {
		$from = $this->getParam('from', date('Y-m-d', strtotime('-1 weeks')));
		$to = $this->getParam('to', date('Y-m-d'));

		$title = sprintf(
			'%s - %s - %s',
			strings::asLocalDate($from),
			strings::asLocalDate($to),
			$this->label

		);

		$dao = new dao\docmgr;
		$this->data = (object)[
			'title' => $title,
			'from' => $from,
			'to' => $to,
			'dtoSet' => $dao->getRange($from, $to)

		];

		$this->render([
			'title' => $this->title = $this->label,
			'primary' => 'report',
			'secondary' => ['index', 'index-handler']
		]);
		//'secondary' => ['index','uploader','queue']]);

	}

	public function reportOfProperty($id) {
		if ($id = (int)$id) {
			$dao = new dao\docmgr;
			if ($dtoSet = $dao->getForProperty($id)) {

				$this->data = (object)[
					'title' => $this->label,
					'dtoSet' => $dtoSet

				];

				$this->load('report');
			}
		}
	}
}
