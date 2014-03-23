jQuery(function($) {
	var genid = SwitchFieldType.createUniqueIdGenerator('genid-switch-');

	function initializeElement(switchEl) {
		SwitchFieldType.initialize(switchEl);

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

	if (typeof(window.Grid) !== 'undefined') {
		Grid.bind('switch', 'display', function(cell) {
			// When Grid adds a new switch, initialize it.
			var switchEl = $(cell).find('.switch');
			initializeElement(switchEl);
		});
	}
});
