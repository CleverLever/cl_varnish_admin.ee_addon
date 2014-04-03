<?php

class Cl_varnish_admin_mcp 
{
	private $addon_name = "Cl_varnish_admin";
	
	public function __construct() 
	{
		$this->EE =& get_instance();
		$this->EE->load->add_package_path(PATH_THIRD . "/" . strtolower($this->addon_name));
		
		$this->EE->load->model('Cl_varnish_admin_settings_model');

		$this->EE->cp->set_right_nav(array(
			'Settings' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=settings',
			'Cached Items' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=cached_items',
			'Cache Clear Rules' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=cache_clear_rules',
			'Utilities' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=utilities',
		));
	}
	public function index() { $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=settings'); }

	public function cached_items() 
	{
		$this->EE->cp->set_variable('cp_page_title', lang('cl_varnish_admin_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")");
		$this->EE->load->model('Cl_varnish_admin_cached_items_model', 'cached_items');
		$this->EE->load->library('cl_varnish_admin_library');
		
		if (!empty($_POST)) 
		{
			foreach ($_POST['items'] as $uri)
			{
				$item = $this->EE->cached_items->get($uri)->row_array();
				
				switch ($_POST['action']) {
					case "clear_and_warm":
						$this->EE->cached_items->delete(array('site_id' => $item['site_id'], 'uri' => $item['uri']));
						$this->EE->cl_varnish_admin_library->ban_uri($item['uri']);
						if ($item['warm']) $this->EE->cl_varnish_admin_library->warm_uri($item['uri']);
					break;
					case "clear_and_force_warm":
						$this->EE->cached_items->delete(array('site_id' => $item['site_id'], 'uri' => $item['uri']));
						$this->EE->cl_varnish_admin_library->ban_uri($item['uri']);
						$this->EE->cl_varnish_admin_library->warm_uri($item['uri']);
					break;
					case "clear":
						$this->EE->cached_items->delete(array('site_id' => $item['site_id'], 'uri' => $item['uri']));
						$this->EE->cl_varnish_admin_library->ban_uri($item['uri']);
					break;
					case "delete":
						$this->EE->cached_items->delete(array('site_id' => $item['site_id'], 'uri' => $item['uri']));
					break;
				}
			}
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->addon_name.AMP.'method='.__FUNCTION__);
		}

		return $this->EE->load->view("mcp/" . __FUNCTION__, array('cached_items' => $this->EE->cached_items), TRUE);
	}

	public function settings() 
	{
		$this->EE->cp->set_variable('cp_page_title', lang('cl_varnish_admin_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")");
		
		$this->EE->load->model('Cl_varnish_admin_settings_model');
		
		if (!empty($_POST)) 
		{
			foreach ($_POST['settings'] as $key => $value) 
			{
				$this->EE->Cl_varnish_admin_settings_model->save($key, $value);
			}
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->addon_name.AMP.'method='.__FUNCTION__);
		}

		return $this->EE->load->view("mcp/" . __FUNCTION__, array('settings' => $this->EE->Cl_varnish_admin_settings_model), TRUE);
	}
	
	public function utilities()
	{
		$this->EE->cp->set_variable('cp_page_title', lang('cl_varnish_admin_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")");
		
		if (!empty($_POST)) 
		{
			$this->EE->load->library('cl_varnish_admin_library');

			switch ($_POST['action']) {
				case "url":
					$this->EE->cl_varnish_admin_library->banUrl($_POST['url']);
				break;
				case "site":
					$this->EE->cl_varnish_admin_library->banSite();
				break;
				case "entire":
					$this->EE->cl_varnish_admin_library->banAll();
				break;
			}
	
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->addon_name.AMP.'method='.__FUNCTION__);
		}

		return $this->EE->load->view("mcp/" . __FUNCTION__, array('settings' => $this->EE->Cl_varnish_admin_settings_model), TRUE);
	}

	
	public function cache_clear_rules()
	{
		$this->EE->cp->set_variable('cp_page_title', lang('cl_varnish_admin_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")");
		
		$this->EE->load->model('Cl_varnish_admin_cache_clear_rules_model', 'cache_clear_rules');
		
		if (!empty($_POST)) 
		{
			if (isset($_POST['delete'])) $this->EE->cache_clear_rules->delete($_POST['delete']);
			else
			{
				foreach ($_POST['settings'] as $key => $value) 
				{
					$this->EE->Cl_varnish_admin_settings_model->save($key, $value);
				}

				foreach ($_POST['cache_clear_rules'] as $cache_clear_rule)
				{
					$this->EE->cache_clear_rules->data = array();

					$this->EE->cache_clear_rules->data = $cache_clear_rule;
					$this->EE->cache_clear_rules->save(array('id' => $cache_clear_rule['id']));
				}
			}

			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->addon_name.AMP.'method='.__FUNCTION__);
		}

		return $this->EE->load->view("mcp/" . __FUNCTION__, array(
			'cache_clear_rules' => $this->EE->cache_clear_rules,
			'settings' => $this->EE->Cl_varnish_admin_settings_model), TRUE);
	}

}