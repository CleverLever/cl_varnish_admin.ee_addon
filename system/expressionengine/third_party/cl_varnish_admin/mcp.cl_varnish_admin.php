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
			// 'Settings' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=settings',
			'Cached Items' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=cached_items',
			'Cache Clear Rules' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=cache_clear_rules',
			'Utilities' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=utilities',
		));
	}
	public function index() { $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=cached_items'); }

	public function cached_items() 
	{
		$this->EE->view->cp_page_title =  lang('cl_varnish_admin_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")";
		$this->EE->load->model('Cl_varnish_admin_cached_items_model', 'cached_items');
		$this->EE->load->library('cl_varnish_admin_request');
		
		if (!empty($_POST)) 
		{
			foreach ($_POST['items'] as $hash)
			{
				$item = $this->EE->cached_items->get($hash)->row_array();
				
				switch ($_POST['action']) {
					case "purge_and_warm":
						if ($item['warm']) $this->EE->cl_varnish_admin_request->refresh($item['uri']);
						else $this->EE->cl_varnish_admin_request->purge($item['uri']);
					break;
					case "purge_and_force_warm":
						$this->EE->cl_varnish_admin_request->refresh($item['uri']);
					break;
					case "purge":
						$this->EE->cl_varnish_admin_request->purge($item['uri']);
					break;
					case "delete":
						$this->EE->cached_items->delete(array('site_id' => $item['site_id'], 'hash' => $item['hash']));
					break;
				}
			}
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->addon_name.AMP.'method='.__FUNCTION__);
		}

		return $this->EE->load->view("mcp/" . __FUNCTION__, array('cached_items' => $this->EE->cached_items), TRUE);
	}

	public function settings() 
	{
		$this->EE->view->cp_page_title = lang('cl_varnish_admin_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")";
		
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
		$this->EE->view->cp_page_title =  lang('cl_varnish_admin_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")";
		
		if (!empty($_POST)) 
		{
			$this->EE->load->library('cl_varnish_admin_request');

			switch ($_POST['action']) {
				case "purge":
					$this->EE->cl_varnish_admin_request->purge($_POST['purge_url']);
				break;
				case "ban_path":
					$this->EE->cl_varnish_admin_request->ban_path($_POST['path']);
				break;
				case "ban_site":
					$this->EE->cl_varnish_admin_request->ban_host(parse_url($this->EE->config->item('site_url'), PHP_URL_HOST));
				break;
				case "ban_all":
					$this->EE->cl_varnish_admin_request->ban_all();
				break;
				case "refresh":
					$this->EE->cl_varnish_admin_request->refresh($_POST['refresh_url']);
				break;
			}
	
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->addon_name.AMP.'method='.__FUNCTION__);
		}

		return $this->EE->load->view("mcp/" . __FUNCTION__, array('settings' => $this->EE->Cl_varnish_admin_settings_model), TRUE);
	}

	
	public function cache_clear_rules()
	{
		$this->EE->view->cp_page_title =  lang('cl_varnish_admin_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")";
		
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