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

use currentUser;
use dao\_dao;

class docmgr extends _dao {
	protected $_db_name = 'docmgr';
	protected $template = '\dvc\docmgr\dao\dto\docmgr';

	protected static function extendDTO( $dto) {
		if ( $dto) {
			$file = $dto->path . $dto->file;
			// \sys::logger( sprintf('<%s> %s', $file, __METHOD__));

			if ( $dto->exists = file_exists( $dto->path)) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
				$dto->type = finfo_file($finfo, $file);
				$dto->pages = 1;

				$imgs = [
					'image/gif',
					'image/png',
					'image/jpeg'

				];

				if ( in_array( $dto->type, $imgs)) {
					$dto->viewer = 'image';

				}
				elseif ( in_array( $dto->type, ['image/tiff'])) {
					$dto->viewer = 'tiff';
					/* Create the object */
					$image = new \Imagick( $file);
					$dto->pages = $image->getNumberImages();


				}
				elseif ( in_array( $dto->type, ['application/pdf'])) {
					$dto->viewer = 'pdf';
					try {
						$pdf = new \setasign\Fpdi\Fpdi;
						//~ $dto->pages = self::getNumPagesPdf( $file);
						$dto->pages = $pdf->setSourceFile( $file); // How many pages?
						$pdf->close();

					} catch (\Throwable $th) {
						//throw $th;

					}

				}
				else {
					$dto->viewer = 'download';

				}

			}

		}

		return ( $dto);

	}

	public function addTag( dto\docmgr $dto, string $tag) : bool {
		$tags = $dto->tags ? (array)json_decode( $dto->tags) : [];

		if ( !\in_array( $tag, $tags)) {
			$tags[] = $tag;

		}

		$this->UpdateByID( ['tags' => \json_encode( $tags)], $dto->id);

		return false;

	}

	public function delete( $id) {
		if ( $dto = $this->getByID( $id)) {
			if ( \file_exists( $path = $this->getRealPath( $dto))) {
				\unlink( $path);

			}

			parent::delete( $id);

		}

	}

	public function deleteTag( dto\docmgr $dto, string $tag) : bool {
		if ( $dto->tags) {
			$tags = (array)json_decode( $dto->tags);
			$key = array_search( $tag, $tags);
			if ( false !== $key) {
				unset( $tags[ $key]);
				if ( count( $tags)) {
					$this->UpdateByID( ['tags' => \json_encode( $tags)], $dto->id);

				}
				else {
					$this->UpdateByID( ['tags' => ''], $dto->id);

				}

			}

		}

		return false;

	}

	public function getOfFolder( string $folder) : array {
		$flds = [
			'id',
			'file',
			'name',
			'uploaded',
			'folder',
			'tags',
			'user_id',
			'property_id',
			'filed',

		];

		$sql = sprintf(
			'SELECT `%s` FROM docmgr WHERE folder = "%s"',
			implode( '`,`', $flds),
			$this->escape( $folder));


		if ( $res = $this->Result( $sql)) {
			return ( $res->dtoSet( null, $this->template));

		}

		return [];

	}

	public function getTags( dto\docmgr $dto) : array {
		return $dto->tags ? (array)json_decode( $dto->tags) : [];

	}

	public function getByFileName( string $filename) {
		$sql = sprintf(
			'SELECT * FROM docmgr WHERE file = "%s"',
			$this->escape( $filename));

		if ( $res = $this->Result( $sql)) {
			return ( self::extendDTO( $res->dto( $this->template)));

		}

		return false;

	}

	protected function getFor( array $conditions ) : array {
		$sql = sprintf( 'SELECT
				*
			FROM
				`docmgr`
			WHERE
				%s', implode( ' AND ', $conditions));

		if ( $res = $this->Result( $sql)) {
			return $res->dtoSet( null, $this->template);

		}

		return [];

	}

	public function getForProperty( int $id) : array {
		return $this->getFor([
			sprintf( '`property_id` = %d', $id)

		]);

	}

	public function getRange( string $from, string $to) : array {
		return $this->getFor([
			sprintf( '`uploaded` BETWEEN "%s" AND "%s 23:59"', $from, $to)

		]);

	}

	public function getRealPath( dto\docmgr $dto) : string {
		return realpath( $path = $dto->path . $dto->file);

	}

	public function queue( int $id = 0) : array {
		if ( !( $id = (int)$id)) {
			$id = currentUser::id();

		}

		$_sql = sprintf( 'SELECT
				*
			FROM
				`%s`
			WHERE
				`filed` = 0
				AND `user_id` = %d
			ORDER BY
				`id` DESC', $this->_db_name, $id);

		if ( $res = $this->Result( $_sql)) {
			return ( $res->dtoSet( function( $dto) {
				return (object)['file' => $dto->file];

			}));

		}

		return [];

	}

}

