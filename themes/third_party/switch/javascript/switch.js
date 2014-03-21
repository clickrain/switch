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

		switchEl.on('change', 'input', function() {
			var evt = jQuery.Event("switchChange");
			var input = $(this);

			evt.switchPosition = input.attr('data-position');
			evt.switchValue = input.val();
			evt.switchText = input.next('label').text();
			evt.switchColor = input.attr('data-color');

			switchEl.trigger(evt);
		});
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
