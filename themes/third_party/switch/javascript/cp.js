jQuery(function($) {
	var genid = SwitchFieldType.createUniqueIdGenerator('genid-switch-');

	// ExpressionEngine duplicates our element, so none of the IDs are unique.
	// So, make them unique.
	$('.switch').each(function() {
		var switchEl = $(this);
		SwitchFieldType.initialize(switchEl);

		switchEl.find('input').each(function() {
			var id = genid();
			var input = $(this);
			var label = input.next('label');
			input.attr('id', id);
			label.attr('for', id);
		});
	});

	function rebuildDefault(ctx) {
		var defaultEl = ctx.find('[data-switchdefdefault]');
		var options = parseInt(ctx.find('[data-switchdefoptions] input:checked').val(), 10);
		defaultEl.attr('data-options', options);
		var totalExistingOptions = defaultEl.find('input').length;

		defaultEl.find('label').each(function(i) {
			if (i < options) {
				$(this).show();
			}
			else {
				$(this).hide();
			}
		});

		ctx.find('[data-switchdeflabel]').each(function() {
			var input = $(this);
			var i = input.attr('data-switchdeflabel');
			var val = input.val();
			defaultEl.find('label:nth-of-type(' + i + ')').text(val);
		});
		ctx.find('[data-switchdefvalue]').each(function() {
			var input = $(this);
			var i = input.attr('data-switchdefvalue');
			var val = input.val();
			defaultEl.find('input:nth-of-type(' + i + ')').val(val);
		});
		ctx.find('[data-switchdefcolor]').each(function() {
			var selector = $(this);
			var i = selector.attr('data-switchdefcolor');
			var val = selector.find('input:checked').val();
			defaultEl.find('input:nth-of-type(' + i + ')').attr('data-color', val);
		});
	}

	$('#ft_switch .mainTable').each(function() {
		var table = $(this);

		function showOptions(count) {
			// Three options per item, plus the first "Options" item.
			var show = count * 3 + 1;

			table.find('tbody > tr').show();
			table.find('tbody > tr:nth-child(n + ' + (show + 1) + '):not(:last-child)').hide();
		}

		table.find('[name="options"]').on('change', function(e) {
			var options = $(this).val();
			showOptions(options);
		});

		var options = table.find('[name="options"]:checked').val();
		showOptions(options);
		rebuildDefault(table);

		table.on('change', 'input', function() {
			rebuildDefault(table);
		});
	});

	function initializeGridSettings(settings) {
		function showOptions(count) {
			// Three options per item, plus the first "Options" item.
			var show = count * 3 + 1;

			settings.find('.grid_col_settings_section').show();
			settings.find('.grid_col_settings_section:nth-child(n + ' + (show + 1) + '):not(:last-child)').hide();
		}

		settings.find('[name*="[options]"]').on('change', function(e) {
			var options = $(this).val();
			showOptions(options);
		});

		var options = settings.find('[name*="[options]"]:checked').val();
		showOptions(options);
		rebuildDefault(settings);

		settings.on('change', 'input', function() {
			rebuildDefault(settings);
		});
	}

	$('.grid_col_settings_custom_field_switch').each(function() {
		var settings = $(this);

		initializeGridSettings(settings);
	});

	if (typeof(window.Grid) !== 'undefined') {
		Grid.bind('switch', 'displaySettings', function(cell) {
			// When Grid addes a new switch, initialize it.
			var settings = $(cell);
			initializeGridSettings(settings);
		});
	}
});
