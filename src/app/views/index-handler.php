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
	<div class="col" id="<?= $_uid = strings::rand() ?>"></div>

</div>

<script>
$(document)
.on( 'clear-doc-handler', (e) => {
	$('#<?= $_uid ?>').html( '');
})
.on( 'load-doc-handler', (e, file) => {
	// console.log( file);
	$('#<?= $_uid ?>').load( _brayworth_.url('<?= $this->route ?>/?v=handler&f=' + encodeURIComponent( file)));

});
</script>
