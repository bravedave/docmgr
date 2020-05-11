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
	<input type="hidden" name="action" value="filed" />

	<div class="form-group row">
		<div class="col">
			<button type="submit" class="btn btn-outline-primary btn-block">
				file

			</button>

		</div>

	</div>

</form>

<script>
$(document).ready( () => {
	$('#<?= $_form ?>').on( 'submit', function( e) {
		let _form = $(this);
		let _data = _form.serializeFormJSON();

		_brayworth_.post({
			url : _brayworth_.url('<?= $this->route ?>'),
			data : _data,

		}).then( function( d) {
			_brayworth_.growl( d);
			if ( 'ack' == d.response) {
				$(document).trigger( 'clear-doc-handler');
				$(document).trigger( 'queue-refresh');

			}

		});

		console.log( 'filed');

		return false;

	});

});
</script>
