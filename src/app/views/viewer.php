<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/	?>

<style>
[data-role="main-content-wrapper"] > .row { height : calc(100vh - 56px); }
</style>
<?php
if ( $this->data->dto->viewer == 'image') {
	printf( '<img class="img-fluid" src="%s" />', strings::url( 'docmgr/?v=serve&f=' . urlencode( $this->data->dto->file)));

}
elseif ( $this->data->dto->viewer == 'tiff') {
	printf( '<a class="btn btn-link" href="%s">download</a>', strings::url( 'docmgr/?v=serve&f=' . urlencode( $this->data->dto->file)));

}
elseif ( $this->data->dto->viewer == 'pdf') {
	printf( '<iframe class="w-100 h-100" src="%s"></iframe>', strings::url( 'docmgr/?v=serve&f=' . urlencode( $this->data->dto->file)));

}
else {
	sys::dump( $this->data->dto);

}	?>
<script>
$(document).trigger( 'load-doc-handler', '<?= urlencode( $this->data->dto->file) ?>');
</script>
