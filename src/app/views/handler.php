<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/	?>
<form id="<?= $_form = strings::rand() ?>">
	<input type="hidden" name="property_id" />
	<div class="form-group row mt-2">
		<div class="col">
			<input type="text" class="form-control" name="name" id="<?= $_uid = strings::rand() ?>"
				placeholder="<?= $this->data->dto->file ?>"
				value="<?= $this->data->dto->name ?>" />

			<label class="small" for="<?= $_uid ?>">name</label>

		</div>

	</div>

	<div class="form-group row">
		<div class="col">
			<div class="input-group">
				<input type="text" class="form-control" readonly value="<?= $this->data->dto->file; ?>" />

				<div class="input-group-append">
					<div class="input-group-text">
						<?= strings::asLocalDate( $this->data->dto->uploaded); ?>

					</div>

				</div>

			</div>

			<label class="small">file name/date uploaded</label>

		</div>

	</div>

	<div class="form-group row">
		<div class="col" id="<?= $_uid = strings::rand(); ?>"></div>

	</div>
	<script>
	$(document).on('tag-refresh', (e) => {
		let url = _brayworth_.url( '<?= $this->route ?>/?v=tags&f=<?= urlencode( $this->data->dto->file) ?>');
		// console.log( url);

		$('#<?= $_uid ?>').load( url);

	})
	.ready( () => {
		$(document).trigger('tag-refresh');

	});
	</script>

	<div class="form-group row">
		<div class="col">
			<div class="input-group">
				<input type="text" class="form-control" id="<?= $_uid = strings::rand() ?>" />

				<div class="input-group-append">
					<button type="button" id="<?= $_uid ?>btn" class="btn btn-outline-primary">
						Add Tag

					</button>

				</div>
				<script>
				$(document).ready( () => {
					$('#<?= $_uid ?>btn').on( 'click', function( e) {
						e.stopPropagation();e.preventDefault();

						let tag = String( $('#<?= $_uid ?>').val());
						if ( '' != tag) {
							_brayworth_.post({
								url : _brayworth_.url('<?= $this->route ?>'),
								data : {
									action : 'tag-add',
									id : <?= (int)$this->data->dto->id ?>,
									tag : tag

								},

							}).then( function( d) {
								if ( 'ack' == d.response) {
									$('#<?= $_uid ?>').val('');

								}
								_brayworth_.growl( d);

							});

						}

					});

				});
				</script>

			</div>

		</div>

	</div>

	<div class="form-group row d-none">
		<div class="col">
			<input type="text" class="form-control" id="<?= $_uid = strings::rand() ?>" />
			<label class="small" for="<?= $_uid ?>">Property</label>

		</div>

	</div>
	<script>
	$(document).ready( () => {
		if ( !!window._cms_) {
			$('#<?= $_uid ?>').closest('.row').removeClass('d-none');

			$('#<?= $_uid ?>').autofill({
				autoFocus : true,
				source: _cms_.search.address,
				select: (e, ui) => {
					let o = ui.item;

					//~ console.log( o);
					$('input[name="property_id"]', '#<?= $_form ?>').val( o.id);

				},

			})

		}
		else {
			console.log( 'no cms');

		}

	});
	</script>

	<div class="form-group row">
		<div class="col">
			<button type="submit" class="btn btn-outline-primary btn-block">
				file

			</button>

		</div>

	</div>

</form>

<?php if ( $this->data->dto->pages > 1) {
	$uidExplode = uniqid( 'cms_');	?>
<ul class="list-unstyled">
	<li><?php
		printf( 'Pages : %d', $this->data->dto->pages);

		if ( $this->data->dto->viewer == 'pdf') {
			printf( '<a href="#" class="btn btn-sm btn-light pull-right" id="%s">explode</a>', $uidExplode);

		}	// if ( $this->data->dto->viewer == 'pdf')
		elseif ( $this->data->dto->viewer == 'tiff') {
			printf( '<a href="#" class="btn btn-sm btn-light pull-right" id="%s">convert to jpegs</a>', $uidExplode);

		}	// if ( $this->data->dto->viewer == 'tiff')

	?></li>

</ul>

<?php }	// if ( $this->data->dto->pages > 1)	?>

<script>
$(document).ready( () => {
<?php if ( $this->data->dto->pages > 1) {	?>
	$('#<?= $uidExplode ?>').on( 'click', function( e) {
		e.stopPropagation(); e.preventDefault();

		_cms_.post({
			url : _cms_.url( 'docmgr'),
			data : {
				action : 'explode',
				file : <?= print json_encode( $this->data->dto->file); ?>
			}

		}).then( function( d) {
			_cms_.growl( d);
			$('[queue-table]').trigger( 'refresh');

		});

	});

<?php }	// if ( $this->data->dto->pages > 1)	?>

	$('#<?= $_form ?>').on( 'submit', function( e) {
		let _form = $(this);
		let _data = _form.serializeFormJSON();

		console.log( _data);

		return false;

	});

});
</script>
