;(function() {
"use strict";

if (typeof(window.SwitchFieldType)) {
	window.SwitchFieldType = {
	};
}

/**
 * Initialize the basic functions of a Switch. This includes setting up the
 * events and doing things to support older browsers.
 */
SwitchFieldType.initialize = function initialize(switchEl) {
	switchEl.addClass(SwitchFieldType.isTransformSupported ? 'csstransforms' : 'no-csstransforms');

	switchEl.find(':checked').attr('data-checked', 'checked');

	switchEl.on('change', 'input', function() {
		var evt = jQuery.Event("switchChange");
		var input = $(this);

		// For IE8, which does not support :checked, we need to set a class or
		// attribute that it CAN select.
		switchEl.find('[data-checked]').removeAttr('data-checked');
		input.attr('data-checked', 'checked');

		evt.switchPosition = input.attr('data-position');
		evt.switchValue = input.val();
		evt.switchText = input.next('label').text();
		evt.switchColor = input.attr('data-color');

		switchEl.trigger(evt);
	});
};

/**
 * Determine if CSS transforms are supported.
 */
SwitchFieldType.isTransformSupported = (function getSupportedTransform() {
    var prefixes = 'transform WebkitTransform MozTransform OTransform msTransform'.split(' ');
    for(var i = 0; i < prefixes.length; i++) {
        if(document.createElement('div').style[prefixes[i]] !== undefined) {
            return true;
        }
    }
    return false;
})();

/**
 * Create a unique ID generator to avoid ID conflicts.
 */
SwitchFieldType.createUniqueIdGenerator = function createUniqueIdGenerator(prefix) {
	var i = 0;
	return function() {
		i++;
		return prefix + i;
	};
};

})();
