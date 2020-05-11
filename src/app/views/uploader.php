<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<div class="row">
    <div class="col" id="<?= $uid = strings::rand() ?>"></div>

</div>
<script>
$(document).ready( () => {
    let c = _brayworth_.fileDragDropContainer({
        fileControl : true,

    }).appendTo('#<?= $uid ?>');

    _brayworth_.fileDragDropHandler.call( c, {
        url : '<?= strings::url( $this->route ) ?>',
        postData : {
            action : 'upload'

        },
        onUpload : ( d) => {
            $(document).trigger( 'queue-refresh');

        }

    });

});
</script>