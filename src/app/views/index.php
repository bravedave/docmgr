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
.pointer { cursor: pointer; }
.forbidden { cursor: not-allowed; }
a { color: inherit; }

</style>
<div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header bg-secondary text-white py-2">
				<h5 class="modal-title" id="<?= $_modal ?>Label">Tic Tac Toe</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>

				</button>

			</div>

			<div class="modal-body" style="font-size: 3em;">&nbsp;</div>

			<div class="modal-footer">&nbsp;</div>

		</div>

	</div>

</div>


<ul class="nav flex-column text-secondary">
	<li class="nav-item h5"><a href="<?= strings::url( $this->route ) ?>">Index</a></li>

<?php if ( $this->Request->ServerIsLocal()) { ?>
	<li class="nav-item"><a class="nav-link" href="<?php url::write('hello/info') ?>">View phpinfo()</a></li>

<?php } // if ( Request::ServerIsLocal()) ?>

	<li class="nav-item"><a class="nav-link" href="#"
		data-toggle="modal"
		data-target="#<?= $_modal ?>">Tic Tac Toe</a></li>

</ul>
<script>
(function( $) {
	let props = {
		move : 'X',
		moves : 0,
		winner : '',
		history : []

	}

	let turn = function( e) {
		if ( props.winner == '') {
			if ( /(X|O)/.test( $( this).html())) return;

			props.moves ++;
			$( this).html( props.move).removeClass('pointer').addClass('forbidden').trigger( 'change');
			props.move = props.move == 'X' ? 'O' : 'X';

		}

	}

	let square = ( host) => {
		return $('<div class="col border text-center pointer">&nbsp;</div>')
			.on( 'click', turn)
			.on( 'change', () => { host.trigger( 'winner')})
			.appendTo( host);

	};

	(function( host) {
		let squares = [];

		host
		.on('render', function( e) {
			$(this).html('');

			(function( r) { squares.push( square( r), square( r), square( r))})( $('<div class="row" />').appendTo( host));
			(function( r) { squares.push( square( r), square( r), square( r))})( $('<div class="row" />').appendTo( host));
			(function( r) { squares.push( square( r), square( r), square( r))})( $('<div class="row" />').appendTo( host));

		})
		.on('winner', function( e) {
			let win = [
				[0,1,2],
				[0,4,8],
				[0,3,6],
				[1,4,7],
				[2,4,6],
				[2,5,8],
				[3,4,5],
				[6,7,8]
			];

			$.each( win, function( i, arr) {
				let a = squares[ arr[0]].html();
				let b = squares[ arr[1]].html();
				let c = squares[ arr[2]].html();

				if ( a == b && a == c && /(O|X)/.test( a)) {
					props.winner = a;
					return false;	// break

				}

			});

			let h = []
			$.each( squares, function( i, sq) {
				h.push( sq.html());

			});

			props.history.push( h);

			if ( /(O|X)/.test( props.winner)) {
				$('.modal-footer', '#<?= $_modal ?>').html('Winner : ' + props.winner);

			}
			else if ( props.moves == 9 ) {
				$('.modal-footer', '#<?= $_modal ?>').html('draw');

			}
			else {
				let bg = $('<div class="btn-group btn-group-sm" />');
				$.each( props.history, function( index, state) {
					$('<a href="#" class="btn btn-outline-primary" />').appendTo( bg).html( index).on( 'click', function( e) {
						e.stopPropagation(); e.preventDefault();

						props.moves = 0;
						let moves = { x : 0, o : 0 };
						$.each( state, function( i, el) {
							squares[i].html( el);
							if ( /(O|X)/.test( el)) {
								'O' == el ? moves.o++ : moves.x++;
								squares[i].removeClass('pointer').addClass('forbidden');
								props.moves ++;

							}
							else {
								squares[i].removeClass('forbidden').addClass('pointer');
								props.move = props.move == 'X' ? 'O' : 'X';

							}

						});

						props.move = moves.x > moves.o ? 'O' : 'X';
						if ( props.history.length > index) {
							props.history.splice( index, props.history.length - index +1);

						}

						host.trigger( 'winner');

					});

				});

				$('.modal-footer', '#<?= $_modal ?>').html('').append( bg);

			}

		})

		host.trigger( 'render');

	})( $('.modal-body', '#<?= $_modal ?>'));

})( jQuery);
</script>
