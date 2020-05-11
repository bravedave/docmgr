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

	public function getTags( dto\docmgr $dto) : array {
		return $dto->tags ? (array)json_decode( $dto->tags) : [];

	}

	public function getByFileName( string $filename) {
		if ( $res = $this->Result( sprintf( 'SELECT * FROM docmgr WHERE file = "%s"', $this->escape( $filename)))) {
			return ( self::extendDTO( $res->dto( $this->template)));

		}

		return false;

	}

	public function queue( int $id = 0) : array {
		if ( !( $id = (int)$id)) {
			$id = currentUser::id();

		}

		if ( $res = $this->Result( sprintf( 'SELECT * FROM docmgr WHERE filed = 0 AND user_id = %d ORDER BY id ASC', $id))) {
			return ( $res->dtoSet( function( $dto) {
				return (object)['file' => $dto->file];

			}));

		}

		return [];

	}

}
