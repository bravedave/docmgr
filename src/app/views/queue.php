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
	<div class="col" id="<?= $queueHandler = strings::rand() ?>"></div>

</div>

<div class="row">
	<div class="col">
		<table class="table table-sm"
			id="<?= $queueID = strings::rand() ?>" queue-table></table>

	</div>
</div>
<script>
$(document)
.on( 'load-doc-handler', (e, file) => {
	// console.log( file);
	$('#<?= $queueHandler ?>').load( _brayworth_.url('<?= $this->route ?>/?v=handler&f=' + encodeURIComponent( file)));

})
.on( 'queue-refresh', (e) => {
	let content = $('[data-role="content-primary"]');
	content.html('');

	$('#<?= $queueHandler ?>').html('');
	$('#<?= $queueID ?>').html('<div class="spinner-border d-block mt-4 mx-auto" role="status"><span class="sr-only">Loading...</span></div>');

	_brayworth_.post({
		url : _brayworth_.url( '<?= $this->route ?>'),
		data : {
			action : 'get-queue',

		}

	}).then( function( d) {
		if ( 'ack' == d.response) {
			(( tb) => {
				$('#<?= $queueID ?>').html('').append( tb);
				$.each( d.data, ( i, el) => {
					let tr = $('<tr />').appendTo( tb);
					$('<td />').html( el.file).appendTo( tr);

					tr.addClass('pointer').on( 'click', function( e) {
						e.stopPropagation(); e.preventDefault();

						content.load( _brayworth_.url('<?= $this->route ?>/?f=' + encodeURIComponent( el.file)));

					});

				});

			})( $('<tbody />'));

		}
		else {
			_brayworth_.growl( d);

		}

	});

})
.ready( () => {

	$(document).trigger( 'queue-refresh');

});
</script>
