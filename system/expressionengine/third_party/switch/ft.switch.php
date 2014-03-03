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

	function display_settings($data) {

		$leftlabel = isset($data['leftlabel']) ? $data['leftlabel'] : "ON";
		$leftvalue = isset($data['leftvalue']) ? $data['leftvalue'] : "y";
		$rightlabel = isset($data['rightlabel']) ? $data['rightlabel'] : "OFF";
		$rightvalue = isset($data['rightvalue']) ? $data['rightvalue'] : "n";
		$default = isset($data['default']) ? $data['default'] : 'left';

		$this->EE->table->add_row('Left Label', form_input(array('name' => 'leftlabel', 'value' => $leftlabel)));
		$this->EE->table->add_row('Left Value', form_input(array('name' => 'leftvalue', 'value' => $leftvalue)));
		$this->EE->table->add_row('Right Label', form_input(array('name' => 'rightlabel', 'value' => $rightlabel)));
		$this->EE->table->add_row('Right Value', form_input(array('name' => 'rightvalue', 'value' => $rightvalue)));
		$this->EE->table->add_row('Default', form_dropdown('default', array('left' => 'Left', 'right' => 'Right'), $default));
	}

	function save_settings($data)
	{
		return array(
			'leftlabel'  => ee()->input->post('leftlabel'),
			'leftvalue'  => ee()->input->post('leftvalue'),
			'rightlabel' => ee()->input->post('rightlabel'),
			'rightvalue' => ee()->input->post('rightvalue'),
			'default'    => ee()->input->post('default')
		);
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
		return $this->_display($data, 'field_id_' . $this->field_id, 'field_id_' . $this->field_id, $this->settings);
	}

	function grid_display_field($data) {
		$fieldid = $this->settings['grid_field_id'];
		$rowid = isset($this->settings['grid_row_id']) ? $this->settings['grid_row_id'] : 'new';
		$id = "switch_{$fieldid}_{$rowid}";
		return $this->_display($data, 'field_id_' . $this->field_id, $id, $this->settings);
	}

	function display_cell($data)
	{
		return $this->_display($data, $this->cell_name, $this->cell_name, $this->settings);
	}

	function _display($data, $name, $id, $settings) {
		$this->_include_theme_js('javascript/switch.js');
		$this->_include_theme_css('css/switch.css');

		$leftid = $id . '-on';
		$rightid = $id . '-off';
		$leftlabel = isset($settings['leftlabel']) ? $settings['leftlabel'] : "ON";
		$leftvalue = isset($settings['leftvalue']) ? $settings['leftvalue'] : "y";
		$rightlabel = isset($settings['rightlabel']) ? $settings['rightlabel'] : "OFF";
		$rightvalue = isset($settings['rightvalue']) ? $settings['rightvalue'] : "n";

		$leftchecked = '';
		$rightchecked = '';
		if ($data == $leftvalue) {
			$leftchecked = 'checked';
		}
		if ($data == $rightvalue && $leftchecked == '') {
			$rightchecked = 'checked';
		}

		return <<<EOF
<div class="switch">
	<input type="radio" name="{$name}" id="{$leftid}" class="left" value="{$leftvalue}" {$leftchecked}>
	<label for="{$leftid}" class="left">{$leftlabel}</label>
	<input type="radio" name="{$name}" id="{$rightid}" class="right" value="{$rightvalue}" {$rightchecked}>
	<label for="{$rightid}" class="right">{$rightlabel}</label>
	<span class="shuttle"><span></span></span>
</div>
EOF;
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
		return $this->EE->input->post('field_id_' . $this->field_id);
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
