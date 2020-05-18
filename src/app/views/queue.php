<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/	?>

<div class="row">
	<div class="col">
		<table class="table table-sm"
			id="<?= $queueID = strings::rand() ?>" queue-table></table>

	</div>
</div>
<script>
$(document)
.on( 'queue-refresh', (e) => {
	let content = $('[data-role="content-primary"]');

	$(document).trigger( 'clear-doc-handler')
	$('#<?= $queueID ?>').html('<tr><td><div class="spinner-border d-block mt-4 mx-auto" role="status"><span class="sr-only">Loading...</span></div></td></tr>');

	_brayworth_.post({
		url : _brayworth_.url( '<?= $this->route ?>'),
		data : {
			action : 'get-queue',

		}

	}).then( function( d) {
		if ( 'ack' == d.response) {
			$('#<?= $queueID ?>').html('');

			if ( d.data.length > 0) {
				let th = $( '<thead><tr><td line-number class="text-center pt-2" style="width: 2em;"></td><td class="py-0" ctrl><button type="button" class="btn btn-block btn-sm btn-light d-none">file selected</button></td></tr></thead>');

				$('#<?= $queueID ?>').append( th);

				(( tb) => {
					$('#<?= $queueID ?>').append( tb);
					$.each( d.data, ( i, el) => {
						let tr = $('<tr></tr>').appendTo( tb);
						let lineNo = $('<td line-number class="text-center" style="width: 2em;"></td>').html( el.file).appendTo( tr);
						let td = $('<td></td>').html( el.file).appendTo( tr);

						tr.addClass('pointer').on( 'click', function( e) {
							e.stopPropagation(); e.preventDefault();

							content.html( '<div class="spinner-border d-block mt-4 mx-auto" role="status"><span class="sr-only">Loading...</span></div>');
							content.load( _brayworth_.url('<?= $this->route ?>/?f=' + encodeURIComponent( el.file)));

						});

						lineNo.data( 'id', el.id);
						lineNo.on( 'click', function( e) {
							e.stopPropagation();e.preventDefault();

							let _me = $(this);
							let _data = _me.data();
							if ( _data.line == _me.text()) {
								_me.html( '<i class="fa fa-check"></i>');
								_me.attr( 'data-check', 'yes');

							}
							else {
								_me.html( _data.line);
								_me.attr( 'data-check', 'no');

							}

							$('#<?= $queueID ?>').trigger('update-selected');

						});

					});

					$('#<?= $queueID ?>').trigger('update-line-numbers');

				})( $('<tbody></tbody>'));

				$('button', th).on( 'click', function( e) {
					e.stopPropagation();

					$('#<?= $queueID ?>').trigger( 'file-selected');

				});

				$('> tr >td[line-number]', th).attr('title', 'select all/none').addClass('pointer').on( 'click', function( e) {
					$('#<?= $queueID ?>').trigger('select-toggle');

				});

			}

		}
		else {
			_brayworth_.growl( d);

		}

	});

})
.ready( () => {
	$('#<?= $queueID ?>')
	.on('update-line-numbers', function( e) {
		let t = 0;
		$('> tbody > tr:not(.d-none) >td[line-number]', this).each( ( i, e) => {
			$(e).data('line', i+1).html( i+1);
			t ++;

		});

		$('> thead > tr >td[line-number]', this).data('line', t).html( t);

	})
	.on('update-selected', function( e) {
		let t = 0;
		$('> tbody > tr:not(.d-none) >td[line-number][data-check="yes"]', this).each( ( i, e) => {
			t ++;

		});

		if ( 0 == t) {
			$('> thead > tr >td[line-number]', this).html( $('> thead > tr >td[line-number]', this).data('line'));
			$('> thead button', this).addClass('d-none');

		}
		else {
			$('> thead > tr >td[line-number]', this).html( t);
			$('> thead button', this).removeClass('d-none');

		}


	})
	.on( 'select-all', function( e) {
		$('> tbody > tr:not(.d-none) >td[line-number]', this).each( ( i, e) => {
			$(e)
			.html( '<i class="fa fa-check"></i>')
			.attr( 'data-check', 'yes');

		});

		$('#<?= $queueID ?>').trigger('update-selected');

	})
	.on( 'select-none', function( e) {
		$('> tbody > tr:not(.d-none) >td[line-number]', this).each( ( i, e) => {
			let _e = $(e);
			let _data = _e.data();
			_e.html( _data.line);
			_e.attr( 'data-check', 'no');

		});

		$('#<?= $queueID ?>').trigger('update-selected');

	})
	.on( 'select-toggle', function( e) {
		let _me = $(this);
		let _data = _me.data();

		if ( 'selected' == String( _data.state)) {
			_me.data( 'state', '');
			_me.trigger('select-none');

		}
		else {
			_me.data( 'state', 'selected');
			_me.trigger('select-all');

		}

	})
	.on( 'file-selected', function( e) {

		let ids = [];
		$('> tbody > tr:not(.d-none) >td[line-number][data-check="yes"]', this).each( ( i, el) => {
			let _el = $(el);
			let _data = _el.data();

			ids.push( _data.id);

		});

		if ( ids.length > 0) {
			_brayworth_.post({
				url : _brayworth_.url('<?= $this->route ?>'),
				data : {
					ids : ids.join( ','),
					action : 'file-selected'

				},

			}).then( function( d) {
				_brayworth_.growl( d);
				$(document).trigger( 'queue-refresh');

			});

		}
		else {
			$(document).trigger( 'queue-refresh');

		}

	});

	$(document).trigger( 'queue-refresh');

});
</script>
