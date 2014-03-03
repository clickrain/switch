jQuery(function($) {
	function initializeElement(switchEl) {
	}

	$('.switch').each(function() {
		initializeElement($(this));
	});

	if (typeof(window.Matrix) !== 'undefined') {
		Matrix.bind('switch', 'display', function(cell) {
			// When Matrix addes a new switch, initialize it.
			var switchEl = cell.dom.$inputs.closest('.switch');
			initializeElement(switchEl);
		});
	}

	if (typeof(window.Grid) !== 'undefined') {
		Grid.bind('switch', 'display', function(cell) {
			// When Grid addes a new switch, initialize it.
			var switchEl = $(cell).find('.switch');
			initializeElement(switchEl);
		});
	}
});
