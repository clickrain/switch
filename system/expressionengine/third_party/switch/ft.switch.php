<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Switch_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'Switch',
		'version'	=> '1.0.0-alpha'
	);

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
	}

	/**
	 * Allow the Field Type to show up in a Grid.
	 */
	public function accepts_content_type($name)
	{
		return ($name == 'channel' || $name == 'grid');
	}

	protected function _include_theme_js($file) {
		if (! in_array($file, $this->cache['includes']))
		{
			$this->cache['includes'][] = $file;
			$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$this->_theme_url().$file.'?version='.$this->info['version'].'"></script>');
		}
	}

	protected function _include_theme_css($file) {
		if (! in_array($file, $this->cache['includes']))
		{
			$this->cache['includes'][] = $file;
			$this->EE->cp->add_to_head('<link rel="stylesheet" href="'.$this->_theme_url().$file.'?version='.$this->info['version'].'">');
		}
	}

	/**
	 * Theme URL
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

	function _get_setting($data, $setting, $default) {
		if (isset($data[$setting])) {
			return $data[$setting];
		}
		return $default;
	}

	function display_settings($data)
	{
		$colors = array(
			'blue' => 'Blue',
			'green' => 'Green',
			'yellow' => 'Yellow',
			'red' => 'Red'
		);

		$leftlabel = isset($data['leftlabel']) ? $data['leftlabel'] : "ON";
		$leftvalue = isset($data['leftvalue']) ? $data['leftvalue'] : "y";
		$leftcolor = isset($data['leftcolor']) ? $data['leftcolor'] : "blue";
		$rightlabel = isset($data['rightlabel']) ? $data['rightlabel'] : "OFF";
		$rightvalue = isset($data['rightvalue']) ? $data['rightvalue'] : "n";
		$rightcolor = isset($data['rightcolor']) ? $data['rightcolor'] : "blue";
		$default = isset($data['default']) ? $data['default'] : 'left';

		$this->EE->table->add_row('Left Label', form_input(array('name' => 'leftlabel', 'value' => $leftlabel)));
		$this->EE->table->add_row('Left Value', form_input(array('name' => 'leftvalue', 'value' => $leftvalue)));
		$this->EE->table->add_row('Left Color', form_dropdown('leftcolor', $colors, $leftcolor));
		$this->EE->table->add_row('Right Label', form_input(array('name' => 'rightlabel', 'value' => $rightlabel)));
		$this->EE->table->add_row('Right Value', form_input(array('name' => 'rightvalue', 'value' => $rightvalue)));
		$this->EE->table->add_row('Right Color', form_dropdown('rightcolor', $colors, $rightcolor));
		$this->EE->table->add_row('Default', form_dropdown('default', array('left' => 'Left', 'right' => 'Right'), $default));
	}

	function save_settings($data)
	{
		return array(
			'leftlabel'  => ee()->input->post('leftlabel'),
			'leftvalue'  => ee()->input->post('leftvalue'),
			'leftcolor'  => ee()->input->post('leftcolor'),
			'rightlabel' => ee()->input->post('rightlabel'),
			'rightvalue' => ee()->input->post('rightvalue'),
			'rightcolor' => ee()->input->post('rightcolor'),
			'default'    => ee()->input->post('default')
		);
	}

	function grid_display_settings($data) {
		$colors = array(
			'blue' => 'Blue',
			'green' => 'Green',
			'yellow' => 'Yellow',
			'red' => 'Red'
		);

		$leftlabel = isset($data['leftlabel']) ? $data['leftlabel'] : "ON";
		$leftvalue = isset($data['leftvalue']) ? $data['leftvalue'] : "y";
		$leftcolor = isset($data['leftcolor']) ? $data['leftcolor'] : "blue";
		$rightlabel = isset($data['rightlabel']) ? $data['rightlabel'] : "OFF";
		$rightvalue = isset($data['rightvalue']) ? $data['rightvalue'] : "n";
		$rightcolor = isset($data['rightcolor']) ? $data['rightcolor'] : "blue";
		$default = isset($data['default']) ? $data['default'] : 'left';

		return array(
			$this->grid_settings_row('Left Label', form_input(array('name' => 'leftlabel', 'value' => $leftlabel))),
			$this->grid_settings_row('Left Value', form_input(array('name' => 'leftvalue', 'value' => $leftvalue))),
			$this->grid_dropdown_row('Left Color', 'leftcolor', $colors, $leftcolor),
			$this->grid_settings_row('Right Label', form_input(array('name' => 'rightlabel', 'value' => $rightlabel))),
			$this->grid_settings_row('Right Value', form_input(array('name' => 'rightvalue', 'value' => $rightvalue))),
			$this->grid_dropdown_row('Right Color', 'rightcolor', $colors, $rightcolor),
			$this->grid_dropdown_row('Default', 'default', array('left' => 'Left', 'right' => 'Right'), $default)
		);
	}

	function grid_save_settings($data)
	{
		return $data;
	}



	/**
	 * Display Field on Publish
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function display_field($data)
	{
		return $this->_display($data, $this->field_name, $this->field_name, $this->settings);
	}

	function grid_display_field($data) {
		$fieldid = $this->settings['grid_field_id'];
		$rowid = isset($this->settings['grid_row_id']) ? $this->settings['grid_row_id'] : 'new';
		$id = "switch_{$fieldid}_{$rowid}";
		return $this->_display($data, $this->field_name, $id, $this->settings);
	}

	function display_cell($data)
	{
		return $this->_display($data, $this->cell_name, $this->cell_name, $this->settings);
	}

	function _display($data, $name, $id, $settings) {
		$this->_include_theme_js('javascript/switch.js');
		$this->_include_theme_css('css/switch.css');

		$leftlabel = isset($settings['leftlabel']) ? $settings['leftlabel'] : "ON";
		$leftvalue = isset($settings['leftvalue']) ? $settings['leftvalue'] : "y";
		$leftcolor = isset($settings['leftcolor']) ? $settings['leftcolor'] : "blue";
		$rightlabel = isset($settings['rightlabel']) ? $settings['rightlabel'] : "OFF";
		$rightvalue = isset($settings['rightvalue']) ? $settings['rightvalue'] : "n";
		$rightcolor = isset($settings['rightcolor']) ? $settings['rightcolor'] : "blue";

		$leftchecked = FALSE;
		$rightchecked = FALSE;
		if ($data == $leftvalue) {
			$leftchecked = TRUE;
		}
		if ($data == $rightvalue && $leftchecked == FALSE) {
			$rightchecked = TRUE;
		}

		if ($leftchecked == FALSE && $rightchecked == FALSE) {
			$leftchecked = TRUE;
		}

		$options = array();
		$options[] = array(
			'id' => $id . '-1',
			'label' => $leftlabel,
			'value' => $leftvalue,
			'checked' => $leftchecked,
			'color' => $leftcolor
		);
		$options[] = array(
			'id' => $id . '-2',
			'label' => $rightlabel,
			'value' => $rightvalue,
			'checked' => $rightchecked,
			'color' => $rightcolor
		);

		$ret = '<div class="switch" data-options="' . count($options) . '">';

		$i = 0;
		foreach ($options as $option) {
			$i++;
			$ret .= "<input type=\"radio\" name=\"{$name}\" id=\"{$option['id']}\" data-position=\"{$i}\" data-color=\"{$option['color']}\" value=\"{$option['value']}\"";
			if ($option['checked']) {
				$ret .= " checked";
			}
			$ret .= ">";
			$ret .= "<label for=\"{$option['id']}\">{$option['label']}</label>";
		}

		$ret .= '<span class="shuttle"><span></span></span>';
		$ret .= '</div>';

		return $ret;
	}

	/**
	 * Prep data for saving
	 *
	 * @access	public
	 * @param	submitted field data
	 * @return	string to save
	 */
	function save($data)
	{
		return $data;
	}

	function grid_save($data)
	{
		return $data;
	}

	/**
	 * Replace tag
	 *
	 * @access	public
	 * @param	field data
	 * @param	field parameters
	 * @param	data between tag pairs
	 * @return	replacement text
	 *
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		return $data;
	}
}
