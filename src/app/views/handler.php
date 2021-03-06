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
	<input type="hidden" name="id" value="<?= $this->data->dto->id ?>" />
	<input type="hidden" name="property_id" value="<?= $this->data->dto->property_id ?>" />
	<input type="hidden" name="action" />

	<div class="form-group row mt-2">
		<div class="col">
			<input type="text" class="form-control" name="name" id="<?= $_uid = strings::rand() ?>"
				placeholder="<?= $this->data->dto->file ?>"
				value="<?= $this->data->dto->name ?>" />

			<label class="small" for="<?= $_uid ?>">name</label>

		</div>

	</div>

	<div class="form-group row"><!-- file name -->
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

	<div class="form-group row d-none"><!-- folder -->
		<div class="col">
			<select class="form-control"
				data-folder="<?= $this->data->dto->folder ?>"
				id="<?= $_uid = strings::rand() ?>"></select>

			<label class="small" for="<?= $_uid ?>">folder</label>

		</div>

		<script>
		$(document).ready( () => {
			let _select = $('#<?= $_uid ?>');

			_select.on( 'change', function( e) {
				_brayworth_.post({
					url : _brayworth_.url('<?= $this->route ?>'),
					data : {
						action : 'set-folder',
						id : <?= (int)$this->data->dto->id ?>,
						folder : _select.val()

					},

				}).then( function( d) {
					if ( 'ack' == d.response) {
						_select.data('folder', _select.val());

					}

				});


			});

			let _data = _select.data();

			_select.html('');

			_brayworth_.post({
				url : _brayworth_.url('<?= $this->route ?>'),
				data : {
					action : 'folders-get'

				},

			}).then( function( d) {
				if ( 'ack' == d.response) {
					_select.html('<option value=""></option>');
					$.each( d.data, ( i, folder) => {
						let o = $('<option />').val( folder).html( folder);
						if ( folder == _data.folder) {
							o.prop( 'selected', true);

						}

						o.appendTo( _select);

					});

					_select.closest('.form-group').removeClass('d-none');

				}
				else {
					_brayworth_.growl( d);

				}

			});

		});
		</script>

	</div>

	<div class="form-group row"><!-- tag -->
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
									$(document).trigger('tag-refresh');


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

	<!-- property -->
		<div class="form-group row d-none">
			<div class="col">
				<div class="input-group">
					<input type="text" class="form-control" id="<?= $_uid = strings::rand() ?>" />

					<div class="input-group-append" id="<?= $_uid ?>grp"></div>

				</div>

				<label class="small" for="<?= $_uid ?>">Property</label>

			</div>

		</div>
		<script>
		$(document).ready( () => {
			$('input[name="property_id"]', '#<?= $_form ?>')
			.on( 'change', function( e) {
				let _me = $(this);
				let _form = $('#<?= $_form ?>');
				let _data = _form.serializeFormJSON();

				_data.action = 'property-set';

				_brayworth_.post({
					url : _brayworth_.url('<?= $this->route ?>'),
					data : _data,

				}).then( function( d) {
					_brayworth_.growl( d);
					if ( 'ack' == d.response) {
						$('#<?= $_uid ?>').trigger( 'saved');

					}

				});

			});

			$('#<?= $_uid ?>')
			.on( 'saved', function(e) {
				$('#<?= $_uid ?>grp')
				.html('')
				.append( '<div class="input-group-text"><i class="fa fa-check text-success"></i></div>');

			});

			if ( !!window._cms_) {
				$('#<?= $_uid ?>').closest('.row').removeClass('d-none');

				$('#<?= $_uid ?>')
				.autofill({
					autoFocus : true,
					source: _cms_.search.address,
					select: (e, ui) => {
						let o = ui.item;

						//~ console.log( o);
						$('input[name="property_id"]', '#<?= $_form ?>')
						.val( o.id)
						.trigger( 'change');

					},

				});


				((fld) => {
					if ( fld.length > 0) {
						if ( Number( fld.val()) > 0) {
							console.log( fld.val());
							_cms_.getPropertyByID( fld.val())
							.then( (d) => {
								if ( 'ack' == d.response) {
									$('#<?= $_uid ?>').val( d.data.address_street);

								}

							});

						}

					}

				})( $('input[name="property_id"]', '#<?= $_form ?>'));

			}
			else {
				$(document).on( 'trick', (e) => {
					$('input[name="property_id"]','#<?= $_form ?>').val(1175).trigger('change');
					$('#<?= $_uid ?>').closest('.row').removeClass('d-none');
					console.log( 'trick');

				});

				// console.log( 'no cms');

			}

		});
		</script>
	<!-- /property -->

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

		return false;

	});

});
</script>
