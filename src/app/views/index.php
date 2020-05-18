<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/	?>

<ul class="nav flex-column">
	<li class="nav-item">
		<a class="nav-link pl-0 h6" href="<?= strings::url( 'docmgr') ?>"><?= $this->title ?></a>

	</li>

	<li class="nav-item">
		<a class="nav-link" href="<?= strings::url( 'docmgr/report') ?>">Report</a>

	</li>

	<li class="nav-item d-flex">
		<a class="nav-link flex-grow-1" href="<?= strings::url( 'docmgr/folders') ?>">Folders</a>
		<a class="nav-link" href="#" id="<?= $_folders = strings::rand() ?>">
			<i class="fa fa-caret-left"></i>

		</a>

	</li>

</ul>
<script>
$(document).ready( () => {
	$('#<?= $_folders ?>').one( 'click', function( e) {
		e.stopPropagation();e.preventDefault();

		let _me = $(this);
		_me.removeAttr( 'href');

		_brayworth_.post({
			url : _brayworth_.url('<?= $this->route ?>'),
			data : {
				action : 'folders-get'

			},

		}).then( function( d) {
			if ( 'ack' == d.response) {
				$('> .fa', _me).removeClass( 'fa-caret-left').addClass( 'fa-caret-down');

				if ( d.data.length > 0) {
					$.each( d.data, (i, fldr) => {
						let li = $('<li class="nav-item"></li>');

						$('<a class="nav-link"></a>')
						.attr( 'href', _brayworth_.url( '<?= $this->route ?>/folders/' + fldr))
						.data( 'folder', fldr)
						.html( fldr)
						.prepend('<i class="fa fa-fw fa-folder-o mr-1"></i>')
						.appendTo( li)
						.on( 'click', function( e) {
							e.stopPropagation();e.preventDefault();

							let _me = $(this);
							let _data = _me.data();
							let url = _brayworth_.url( '<?= $this->route ?>/files/?folder=' + encodeURIComponent( _data.folder));

							let title = $('<div class="border-bottom"></div>').html( '<i class="fa fa-fw fa-folder-open-o"></i>' + _data.folder);
							let div = $('<div style="height: calc( 100% - 2em);"></div>');

							$('[data-role="content-primary"]')
							.html('')
							.append( title)
							.append( div);

							div
							.html( '<div class="w-50 mx-auto my-4 d-flex align-items-center">Loading...<div class="spinner-border ml-auto" role="status" aria-hidden="true" style="height: 1em; width: 1em;"></div></div>')
							.load( url);

						});

						li.appendTo( _me.closest('.nav'));

					});

				}

			}
			else {
				_brayworth_.growl( d);

			}

		});

	});

});
</script>