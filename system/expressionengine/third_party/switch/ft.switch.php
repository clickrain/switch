<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Switch_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'Switch',
		'version'	=> '1.2.0'
	);

	/**
	 * The maximum number of options that any given Switch may have. This is
	 * essentially an arbitrary number, but we need to be reasonable. A Switch
	 * that has 15 options should probably just be a select box. To change
	 * this to a higher number, it is also necessary to add CSS to support
	 * more options.
	 */
	const MAXIMUM_OPTIONS = 8;

	function __construct()
	{
		parent::__construct();

		if (! isset($this->EE->session->cache['switch']))
		{
			$this->EE->session->cache['switch'] = array();
		}
		$this->cache =& $this->EE->session->cache['switch'];

		if (!isset($this->cache['includes'])) {
			$this->cache['includes'] = array();
		}

		// Define the default list of colors.
		$this->colors = array(
			'default' => 'Default',
			'green' => 'Green',
			'yellow' => 'Yellow',
			'red' => 'Red',
			'blue' => 'Blue'
		);
	}

	/**
	 * Allow the Field Type to show up in a Grid.
	 */
	public function accepts_content_type($name)
	{
		return ($name == 'channel' || $name == 'grid' || $name == 'blocks/1');
	}

	/**
	 * Include a javascript file for this field type if it isn't included yet.
	 */
	protected function _include_theme_js($file) {
		if (! in_array($file, $this->cache['includes']))
		{
			$this->cache['includes'][] = $file;
			$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$this->_theme_url().$file.'?version='.$this->info['version'].'"></script>');
		}
	}

	/**
	 * Include a css file for this field type if it isn't included yet.
	 */
	protected function _include_theme_css($file) {
		if (! in_array($file, $this->cache['includes']))
		{
			$this->cache['includes'][] = $file;
			$this->EE->cp->add_to_head('<link rel="stylesheet" href="'.$this->_theme_url().$file.'?version='.$this->info['version'].'">');
		}
	}

	/**
	 * Determine the base theme URL for the field type.
	 */
	protected function _theme_url()
	{
		if (! isset($this->cache['theme_url']))
		{
			$theme_folder_url = defined('URL_THIRD_THEMES') ? URL_THIRD_THEMES : $this->EE->config->slash_item('theme_folder_url').'third_party/';
			$this->cache['theme_url'] = $theme_folder_url.'switch/';
		}

		return $this->cache['theme_url'];
	}

	/**
	 * Create the settings form for the Control Panel.
	 */
	function display_settings($data)
	{
		// Include our important files.
		$this->_include_theme_css('css/switch.css');
		$this->_include_theme_js('javascript/common.js');
		$this->_include_theme_js('javascript/cp.js');

		// Create a switch so that the user can choose how many options are in
		// his or her Switch. Possible values are from 2 to 8.
		$options = array();
		for ($i = 2; $i <= Switch_ft::MAXIMUM_OPTIONS; $i++) {
			$option = array(
				'id' => "switch-options-$i",
				'label' => $i,
				'color' => 'default',
				'value' => $i
				);
			$options[] = $option;
		}
		$options_count = isset($data['options']) ? $data['options'] : '2';
		$this->EE->table->add_row('Options', $this->build_switch('options', $options_count, $options, array('data-switchdefoptions' => NULL)));

		// At the end, we'll create a Switch where the user can choose the
		// default value for the Switch. As we go through the options, we'll
		// want to add each option to our defaults Switch.
		$defaults = array();

		// Go through all of the possible options and output the controls
		// relevant to that option. We always output all 8 options. The
		// javascript that runs will hide the ones that are not in use.
		for ($i = 1; $i <= Switch_ft::MAXIMUM_OPTIONS; $i++) {
			$label = isset($data["label$i"]) ? $data["label$i"] : "OFF";
			$value = isset($data["value$i"]) ? $data["value$i"] : "";
			$color = isset($data["color$i"]) ? $data["color$i"] : "default";

			// Output options for label, value, and color.
			$this->EE->table->add_row("Label $i", form_input(array('name' => "label$i", 'value' => $label, 'data-switchdeflabel' => $i)));
			$this->EE->table->add_row("Value $i", form_input(array('name' => "value$i", 'value' => $value, 'data-switchdefvalue' => $i)));
			$coloroptions = $this->create_color_switch_options("option-$i-color");
			$this->EE->table->add_row("Color $i", $this->build_switch("color$i", $color, $coloroptions, array('data-switchdefcolor' => $i)));

			// Track the value in the defaults, so the user can set the
			// default value.
			$defaults[] = array(
				'id' => "defaults-$i",
				'label' => $i,
				'color' => 'default',
				'value' => $value
				);
		}

		// Finally, create a Switch where the user can choose the default
		// value for the Switch.
		$default = isset($data['default']) ? $data['default'] : '1';
		$this->EE->table->add_row('Default', $this->build_switch('default', $default, $defaults, array('data-switchdefdefault' => NULL)));
	}

	/**
	 * Save all of the settings that the user choose.
	 */
	function save_settings($data)
	{
		$d = array(
			'options' => ee()->input->post('options'),
			'default' => ee()->input->post('default')
			);

		for ($i = 1; $i <= Switch_ft::MAXIMUM_OPTIONS; $i++) {
			$d["label$i"] = ee()->input->post("label$i");
			$d["value$i"] = ee()->input->post("value$i");
			$d["color$i"] = ee()->input->post("color$i");
		}

		return $d;
	}

	/**
	 * Switch supports Grid, so we need to do the same thing as
	 * display_settings, but for the grid.
	 */
	function grid_display_settings($data) {
		// Include our important files.
		$this->_include_theme_css('css/switch.css');
		$this->_include_theme_js('javascript/common.js');
		$this->_include_theme_js('javascript/cp.js');

		// grid_display_settings must return an array of settings, rather than
		// just adding to the current table. So, this is what we're going to
		// return.
		$settings = array();

		// Create a switch so that the user can choose how many options are in
		// his or her Switch. Possible values are from 2 to 8.
		$options = array();
		for ($i = 2; $i <= Switch_ft::MAXIMUM_OPTIONS; $i++) {
			$option = array(
				'id' => "switch-options-$i",
				'label' => $i,
				'color' => 'default',
				'value' => $i
				);
			$options[] = $option;
		}
		$options_count = isset($data['options']) ? $data['options'] : '2';
		$settings[] = $this->grid_settings_row('Options', $this->build_switch('options', $options_count, $options, array('data-switchdefoptions' => NULL)));

		// At the end, we'll create a Switch where the user can choose the
		// default value for the Switch. As we go through the options, we'll
		// want to add each option to our defaults Switch.
		$defaults = array();

		// Go through all of the possible options and output the controls
		// relevant to that option. We always output all 8 options. The
		// javascript that runs will hide the ones that are not in use.
		for ($i = 1; $i <= Switch_ft::MAXIMUM_OPTIONS; $i++) {
			$label = isset($data["label$i"]) ? $data["label$i"] : "OFF";
			$value = isset($data["value$i"]) ? $data["value$i"] : "";
			$color = isset($data["color$i"]) ? $data["color$i"] : "default";

			// Output options for label, value, and color.
			$settings[] = $this->grid_settings_row("Label $i", form_input(array('name' => "label$i", 'value' => $label, 'data-switchdeflabel' => $i)));
			$settings[] = $this->grid_settings_row("Value $i", form_input(array('name' => "value$i", 'value' => $value, 'data-switchdefvalue' => $i)));
			$coloroptions = $this->create_color_switch_options("option-$i-color");
			$settings[] = $this->grid_settings_row("Color $i", $this->build_switch("color$i", $color, $coloroptions, array('data-switchdefcolor' => $i)));

			// Track the value in the defaults, so the user can set the
			// default value.
			$defaults[] = array(
				'id' => "defaults-$i",
				'label' => $i,
				'color' => 'default',
				'value' => $value
				);
		}

		// Finally, create a Switch where the user can choose the default
		// value for the Switch.
		$default = isset($data['default']) ? $data['default'] : '1';
		$settings[] = $this->grid_settings_row('Default', $this->build_switch('default', $default, $defaults, array('data-switchdefdefault' => NULL)));

		return $settings;
	}

	/**
	 * Save all of the settings that the user choose.
	 */
	function grid_save_settings($data)
	{
		// If no default was specified, use the first value as the default.
		if (!isset($data['default'])) {
			$data['default'] = $data['value1'];
		}
		return $data;
	}



	/**
	 * Display the Switch on the Publish form.
	 */
	function display_field($data)
	{
		// If this is a new entry, use the default.
		if ($this->settings['field_data'] === false) {
			$data = $this->settings['default'];
		}
		return $this->_display($data, $this->field_name, $this->field_name, $this->settings);
	}

	/**
	 * Display the Switch in a grid on the Publish form.
	 */
	function grid_display_field($data) {
		$fieldid = $this->settings['grid_field_id'];
		$rowid = isset($this->settings['grid_row_id']) ? $this->settings['grid_row_id'] : 'new';
		$colid = $this->settings['col_id'];
		$id = "switch_{$fieldid}_{$rowid}_{$colid}";

		// If this is a new entry, use the default.
		if ($rowid === 'new') {
			// Well, that is, if there IS a default.
			if (isset($this->settings['default'])) {
				$data = $this->settings['default'];
			}
			// If there isn't, then use the first value.
			else {
				$data = $this->settings['value1'];
			}
		}

		return $this->_display($data, $this->field_name, $id, $this->settings);
	}

	/**
	 * Display the Switch. This is extracted out because the bulk of
	 * displaying the Switch is common between the various sources.
	 */
	function _display($data, $name, $id, $settings) {
		// Include our important files.
		$this->_include_theme_css('css/switch.css');
		$this->_include_theme_js('javascript/common.js');
		$this->_include_theme_js('javascript/switch.js');

		// How many options do we have? The settings include all the way up to
		// 8 options, so we need to know this to only include the ones that
		// the user wants to show.
		$options_count = isset($settings['options']) ? intval($settings['options']) : 2;

		// Fill up the options for the switch.
		$options = array();
		for ($i = 1; $i <= $options_count; $i++) {
			$label = isset($settings["label$i"]) ? $settings["label$i"] : "OFF";
			$value = isset($settings["value$i"]) ? $settings["value$i"] : "";
			$color = isset($settings["color$i"]) ? $settings["color$i"] : "blue";

			$options[] = array(
				'id' => $id . "-$i",
				'label' => $label,
				'value' => $value,
				'color' => $color
			);
		}

		// And then build build the Switch.
		return $this->build_switch($name, $data, $options);
	}

	/**
	 * Create the HTML for a Switch.
	 */
	function build_switch($name, $value, $options, $additional_attributes = NULL) {
		// The outer <div>.
		$ret = '<div class="switch" data-options="' . count($options) . '"';
		// Add all of the additional attributes to the outer div.
		if (is_array($additional_attributes)) {
			foreach ($additional_attributes as $attrname => $attrval) {
				$ret .= " $attrname";
				if (!is_null($attrval)) {
					$ret .= "=\"$attrval\"";
				}
			}
		}
		$ret .= ">";

		// Used to track if we've already found the option that should be
		// checked. We should only have one item checked, you know.
		$checked = FALSE;

		// Create all of the inner options. Each option has an input and a
		// label.
		$i = 0;
		foreach ($options as $option) {

			// Should this option be checked?
			$ischecked = !$checked && $option['value'] == $value;
			$checked = $checked || $ischecked;

			$i++;
			$ret .= "<input type=\"radio\" name=\"{$name}\" id=\"{$option['id']}\" data-position=\"{$i}\" data-color=\"{$option['color']}\" value=\"{$option['value']}\"";
			if ($ischecked) {
				$ret .= " checked";
			}
			$ret .= ">";
			$ret .= "<label for=\"{$option['id']}\">{$option['label']}</label>";
		}

		// Create the shuttle.
		$ret .= '<span class="shuttle"><span></span></span>';

		// And end the outer div.
		$ret .= '</div>';

		return $ret;
	}

	/**
	 * Create the options for a Switch based on the default colors available,
	 */
	public function create_color_switch_options($id) {
		$coloroptions = array();
		foreach ($this->colors as $color => $colorname) {
			$coloroptions[] = array(
				'id' => "$id-$color",
				'label' => $colorname,
				'color' => $color,
				'value' => $color);
		}
		return $coloroptions;
	}

	/**
	 * Prepare data for saving. Very simple: whatever data we were given is
	 * the data we're going to save.
	 */
	function save($data)
	{
		return $data;
	}

	/**
	 * Prepare data for saving. Very simple: whatever data we were given is
	 * the data we're going to save.
	 */
	function grid_save($data)
	{
		return $data;
	}

	/**
	 * Replace tag. Very simple: whatever data we were given is the data we're
	 * going to output.
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		return $data;
	}
}
