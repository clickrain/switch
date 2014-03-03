function createUniqueIdGenerator(prefix) {
	var i = 0;
	return function() {
		i++;
		return prefix + i;
	};
}

jQuery(function($) {
	var genid = createUniqueIdGenerator('genid-switch-');

	function initializeElement(switchEl) {
		if (switchEl.find('input').eq(0).attr('id').match(/new|genid-switch-/)) {
			switchEl.find('input').each(function() {
				var id = genid();
				var input = $(this);
				var label = input.next('label');
				input.attr('id', id);
				label.attr('for', id);
			});
		}
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
