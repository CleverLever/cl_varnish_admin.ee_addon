<?php
class Cl_varnish_admin_ext 
{
	public $name = "Varnish Admin";
	public $version = "1.0.0";
	public $description = "";
	public $settings_exist = "y";
	public $docs_url = "http://cleverlever.co/add-ons/varnish-admin";
	
	public function __construct() 
	{	

		$this->EE =& get_instance();
		
		$this->EE->load->add_package_path(PATH_THIRD . 'cl_varnish_admin/');
		$this->EE->load->helper('module_helper');

		$this->EE->load->model('Cl_varnish_admin_settings_model', 'settings');		
		$this->EE->load->model('Cl_varnish_admin_cache_clear_rules_model', 'cache_clear_rules');
		$this->EE->load->library('cl_varnish_admin');
	}
	
	public function entry_submission_end_hook($entry_id, $entry_metadata, $entry_data)
	{	
		// rule based clearing
		$target_id = $entry_metadata['channel_id'];

		$rules = $this->EE->cache_clear_rules
			->get_rules(array(
				"hook" => cl_rstr_replace("_hook", "", __FUNCTION__), 
				"target" => $target_id)
			)->result_array();

		
		foreach ($rules as $rule) 
		{
			switch ($rule['action']) {
				case "custom":
					foreach($rule['options'] as $option) 
					{
						$this->EE->cl_varnish_admin->banUrl($this->EE->TMPL->parse_variables_row($option['expression'], $entry_metadata));
						if (!empty($option['warm_url'])) $this->EE->cl_varnish_admin->warmUrl($option['warm_url']);
					}
				break;
				case "site":
					$this->EE->cl_varnish_admin->banSite();
				break;
				case "entire":
					$this->EE->cl_varnish_admin->banAll();
				break;
			}
		}
	}
	
	public function update_template_end_hook($template_id)
	{	
		// global template update action
		$action = $this->EE->settings->get('global_template_update_action');

		switch ($action) {
			case "template":
				$template = $this->EE->settings->get_templates(NULL, array('template_id' => $template_id))->row_array();
				$this->EE->cl_varnish_admin->banUrl("/{$template['group_name']}/{$template['template_name']}");
			break;
			case "group":
				$template = $this->EE->settings->get_templates(NULL, array('template_id' => $template_id))->row_array();
				$this->EE->cl_varnish_admin->banUrl("^/{$template['group_name']}.+");
			break;
			case "site":
				$this->EE->cl_varnish_admin->banSite();
			break;
			case "entire":
				$this->EE->cl_varnish_admin->banAll();
			break;
		}
		
		// rule based clearing
		$target_id = $template_id;

		$rules = $this->EE->cache_clear_rules
			->get_rules(array(
				"hook" => cl_rstr_replace("_hook", "", __FUNCTION__), 
				"target" => $template_id)
			)->result_array();
		
		foreach ($rules as $rule) 
		{
			switch ($rule['action']) {
				case "custom":
					foreach($rule['options'] as $option) 
					{
						$this->EE->cl_varnish_admin->banUrl($this->EE->TMPL->parse_variables_row($option['expression'], $entry_metadata));
						if (!empty($option['warm_url'])) $this->EE->cl_varnish_admin->warmUrl($option['warm_url']);
					}
				break;
				case "site":
					$this->EE->cl_varnish_admin->banSite();
				break;
				case "entire":
					$this->EE->cl_varnish_admin->banAll();
				break;
			}
		}
	}

	public function settings() { $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'); }
	
	/**
	 * Activate Extension
	 *
	 * Ran when the extension is installed.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	public function activate_extension() 
	{
		$hooks = $this->_get_hooks();

		foreach ( $hooks as $method => $hook  )
		{
			$data = array('class'		=> __CLASS__,
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> serialize(array()),
				'priority'	=> 10,
				'version'	=> $this->version,
				'enabled'	=> 'y'
			);
			ee()->db->insert('extensions', $data);
		}
	}
	
	/**
	 * Disable Extension
	 *
	 * Ran when the extension is installed.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	public function disable_extension() 
	{
		ee()->db->delete('extensions', array('class' => __CLASS__));
	}
	
	/**
	 * Get Callbacks
	 * 
	 * Returns each callback and their corresponding hook.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	private function _get_hooks() 
	{
		$hooks = array();
		$methods =  get_class_methods(__CLASS__);
		
		foreach ( $methods as $method )
		{
			if (cl_rstrpos($method, '_hook') !== FALSE) $hooks[$method] = cl_rstr_replace("_hook", "", $method);
		}

		return $hooks;
	}
	
}