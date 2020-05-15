<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<form id="<?= $_form = strings::rand() ?>">
    <input type="hidden" name="action" value="folder-create" />
    <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white py-2">
                    <h5 class="modal-title" id="<?= $_modal ?>Label">Create Folder</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">
                    <input class="form-control" name="folder" placeholder="name" required />
                    <div class="alert alert-warning d-none mt-2">0-9, a-z - no spaces</div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create</button>

                </div>

            </div>

        </div>

    </div>

</form>

<div class="accordion h-100" id="<?= $_accordion = strings::rand() ?>">
    <div id="<?= $_accordion ?>_main" class="collapse fade show" data-parent="#<?= $_accordion ?>">
        <ul class="nav flex-column mb-2 border-bottom" id="<?= $_uid = strings::rand() ?>"></ul>

        <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#<?= $_modal ?>">
            <i class="fa fa-fw fa-plus"></i>new</a>

        </button>

    </div>

    <div id="<?= $_accordion ?>_files" class="collapse fade h-100" data-parent="#<?= $_accordion ?>">
        <ul class="nav">
            <li class="nav-item"><h6 title class="mt-2 mb-0"></h6></li>
            <li class="nav-item ml-auto">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#<?= $_accordion ?>_main">&times;</a>

            </li>

        </ul>

        <div content style="height: calc( 100% - 41px);"></div>

    </div>

</div>
<script>
$(document).ready( () => {
    $('#<?= $_modal ?>').on( 'show.bs.modal', (e) => {
        $('input[name="folder"]', '#<?= $_form ?>').val('');

    });

    let openFolder = function() {
        let _me = $(this);
        let _data = _me.data();
        let url = _brayworth_.url( '<?= $this->route ?>/files/?folder=' + encodeURIComponent( _data.folder));

        // console.log( url);

        $('#<?= $_accordion ?>_files').collapse( 'show');
        $('[title]', '#<?= $_accordion ?>_files').html( '<i class="fa fa-fw fa-folder-open-o"></i>' + _data.folder);

        $('[content]', '#<?= $_accordion ?>_files')
        .html( '<div class="w-50 mx-auto my-4 d-flex align-items-center">Loading...<div class="spinner-border ml-auto" role="status" aria-hidden="true" style="height: 1em; width: 1em;"></div></div>')
        .load( url);

    }

    $('#<?= $_uid ?>')
    .on( 'refresh', function( e) {
        let _me = $(this);

        _me.html('<li class="d-flex align-items-center">Loading...<div class="spinner-border ml-auto" role="status" aria-hidden="true" style="height: 1em; width: 1em;"></div></li>');
        _brayworth_.post({
            url : _brayworth_.url('<?= $this->route ?>'),
            data : {
                action : 'folders-get'

            },

        }).then( function( d) {
            if ( 'ack' == d.response) {
                _me.html('');

                $.each( d.data, ( i, folder) => {
                    let a = $('<a href="#" class="nav-link" />')

                    a
                    .html( folder)
                    .data( 'folder', folder)
                    .prepend( '<i class="fa fa-fw fa-folder-o"></i>');

                    a.on( 'click', function( e) {
                        e.stopPropagation();e.preventDefault();

                        openFolder.call( this);

                    });

                    $('<li class="nav-item" />').append( a).appendTo( _me);

                });

            }
            else {
                _brayworth_.growl( d);

            }

        });

    })
    .trigger('refresh');

    $('#<?= $_form ?>')
    .on( 'submit', function( e) {
        let _form = $(this);
        let _data = _form.serializeFormJSON();
        let _modalBody = $('.modal-body', _form);

        if ( /[^a-z0-9]/i.test( _data.folder)) {
            $('.alert', '#<?= $_form ?>').removeClass('d-none');
            $('input[name="folder"]', '#<?= $_form ?>').focus();

        }
        else {
            $('#<?= $_modal ?>').modal( 'hide');

            _brayworth_.post({
                url : _brayworth_.url('<?= $this->route ?>'),
                data : _data,

            }).then( function( d) {
                $('input[name="folder"]', '#<?= $_form ?>').val('');

                if ( 'ack' == d.response) {
                    $('#<?= $_uid ?>').trigger( 'refresh');

                }
                else {
                    _brayworth_.growl( d);

                }

            });

        }

        return false;

    });

});
</script>