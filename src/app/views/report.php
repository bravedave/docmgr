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

?>
<div class="accordion h-100" id="<?= $_accordion = strings::rand(); ?>">
    <div id="<?= $_accordion ?>report" class="collapse show" data-parent="#<?= $_accordion ?>">
        <div class="table-responsive">
            <h4 class="d-none d-print-block"><?= $this->data->title ?></h4>
            <table class="table" id="<?= $tblID = strings::rand(); ?>">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>uploaded</td>
                        <td>name</td>
                        <td>tags</td>
                        <td><?= strings::html_tick ?></td>

                    </tr>

                </thead>

                <tbody>
                <?php
                $dao = new dao\docmgr;
                foreach ($this->data->dtoSet as $dto) {
                    $tags = $dao->getTags( $dto);
                    printf( '<tr
                        data-id="%s"
                        data-file=%s>',
                        $dto->id,
                        json_encode( $dto->file, JSON_UNESCAPED_SLASHES) );
                    print '<td class="small" line-number></td>';
                    printf( '<td>%s</td>', strings::asLocalDate( $dto->uploaded));
                    printf( '<td>%s</td>', $dto->name);
                    printf( '<td>%s</td>', implode( ',', $tags));
                    printf( '<td>%s</td>', $dto->filed ? strings::html_tick : '&nbsp;');

                    print '</tr>';

                }   ?>
                </tbody>

            </table>

        </div>

    </div>

    <div id="<?= $_accordion ?>viewer" class="collapse h-100 position-relative" data-parent="#<?= $_accordion ?>"></div>

</div>

<script>
$(document).ready( () => {
    $('#<?= $tblID ?>')
    .on('update-line-numbers', function( e) {
        $('> tbody > tr:not(.d-none) >td[line-number]', this).each( ( i, e) => {
            $(e).data('line', i+1).html( i+1);
        });
    })
    .trigger('update-line-numbers');

    $('tbody > tr', '#<?= $tblID ?>').each( (i, tr) => {
        let _tr = $(tr);

        _tr
        .addClass('pointer')
        .on( 'view', function( e) {
            let _tr = $(tr);
            let _data = _tr.data();

            $('#<?= $_accordion ?>viewer')
            .html('<div class="spinner-border d-block mt-4 mx-auto" role="status"><span class="sr-only">Loading...</span></div>');

            $('#<?= $_accordion ?>viewer').collapse( 'show');

            $('#<?= $_accordion ?>viewer').load( _brayworth_.url('<?= $this->route ?>/?f=' + encodeURIComponent( _data.file)), (data) => {
                setTimeout(() => {
                    $('<i class="fa fa-times-circle-o fa-2x pointer position-absolute"></i>')
                    .css({
                        'top':'-12px',
                        'right':'-12px'

                    })
                    .on( 'click', function( e) {
                        e.stopPropagation();e.preventDefault();

                        $('#<?= $_accordion ?>report').collapse( 'show');
                        $(document).trigger( 'clear-doc-handler');


                    })
                    .appendTo('#<?= $_accordion ?>viewer');

                }, 1000);

            });

        })
        .on( 'click', function( e) {
            e.stopPropagation();e.preventDefault();

            $(this).trigger( 'view');

        });

    });


});
</script>
