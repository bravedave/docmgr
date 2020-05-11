<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\docmgr;
use strings;

printf( '<ul class="list-unstyled" id="%s">', $_uid = strings::rand());

$dao = new dao\docmgr;
$tags = $dao->getTags( $this->data->dto);
foreach ($tags as $tag) {
    printf( '<li>%s<i class="fa fa-fw fa-times-circle-o" data-id=%s data-tag=%s delete></i></li>',
        $tag,
        $this->data->dto->id,
        json_encode( $tag)

    );


}
print '</ul>';  ?>
<script>
$(document).ready( () => {
    $('[delete]', '#<?= $_uid ?>').each( (i, el) => {
        $(el)
        .addClass( 'pointer')
        .on( 'click', function( e) {
            e.stopPropagation();e.preventDefault();

            let _me = $(this);
            let _data = _me.data();

            _brayworth_.post({
                url : _brayworth_.url('<?= $this->route ?>'),
                data : {
                    action : 'tag-delete',
                    id : _data.id,
                    tag : _data.tag

                },

            }).then( ( d) => {
                _brayworth_.growl( d);
                if ( 'ack' == d.response) {
                    $(document).trigger('tag-refresh');

                }

            });

        });

    });

});

</script>

