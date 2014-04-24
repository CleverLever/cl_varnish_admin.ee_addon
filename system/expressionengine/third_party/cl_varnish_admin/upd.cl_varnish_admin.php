<?php

class Cl_varnish_admin_upd 
{
	public $version = "1.2.3";
	
	private $addon_name = "Cl_varnish_admin";
	private $has_cp_backend = "y";
	private $has_publish_fields = "n";
	private $settings = array();

	private $mod_actions = array(
		'warm_expired_cached_items',
	);

	public function __construct() 
	{
		$this->EE =& get_instance();
		$this->EE->load->add_package_path(dirname(__FILE__));
	}

	function install()
	{
		$this->_install_module();
		$this->_install_actions();
		$this->_install_models();

		return TRUE;
	}

	function uninstall() 
	{
		$this->_uninstall_module();
		$this->_uninstall_actions();
		$this->_uninstall_models();

		return TRUE;
	}
	
	function update($current = '')
	{
		if (version_compare($current, "1.2.2", "<"))
		{
			$this->EE->load->model('Cl_varnish_admin_cached_items_model');
			$this->EE->Cl_varnish_admin_cached_items_model->create_table();
		}

		if (version_compare($current, "1.2.2", "=="))
		{
			$this->EE->load->model('Cl_varnish_admin_cached_items_model', 'cached_items');
			$this->EE->load->dbforge();
			$this->EE->dbforge->add_column(
				$this->EE->cached_items->table, 
				array(
					'hash' => array(
							'type'			=> 'varchar',
							'constraint'	=> 40,
							'null'			=> FALSE
					)
				)
			);
			
			foreach($this->EE->cached_items->collection()->result_array() as $cached_item)
			{
				$cached_item['hash'] = sha1($cached_item['uri']);

				$this->EE->cached_items->data = $cached_item;
				$this->EE->cached_items->save(array('uri' => $cached_item['uri']));
			}
			$this->EE->db->query("ALTER TABLE `" . $this->EE->db->dbprefix('cl_varnish_admin_cached_items') .  "` DROP PRIMARY KEY");
			$this->EE->db->query("ALTER TABLE `" . $this->EE->db->dbprefix('cl_varnish_admin_cached_items') .  "` ADD PRIMARY KEY( `site_id`, `hash`)");
			$this->EE->db->query("ALTER TABLE `" . $this->EE->db->dbprefix('cl_varnish_admin_cached_items') .  "` CHANGE `uri` `uri` VARCHAR(2000) NOT NULL DEFAULT ''");
		}

		return TRUE;
	}
	
	private function _install_module() 
	{
		$data = array(
			'module_name' => $this->addon_name,
			'module_version' => $this->version,
			'has_cp_backend' => $this->has_cp_backend,
			'has_publish_fields' => $this->has_publish_fields
		);
		$this->EE->db->insert('modules', $data);
	}

	private function _uninstall_module() 
	{
		$this->EE->db->delete('modules', array('module_name' => $this->addon_name));
	}
	
	private function _install_actions() 
	{
		// get existing actions
		$this->EE->db->select('method')
			->from('actions')
			->like('class', $this->addon_name, 'after');
		$existing_methods = array();
		foreach ($this->EE->db->get()->result() as $row) $existing_methods[] = $row->method;

		// insert new actions
		foreach ($this->mod_actions as $method)	{
			if ( ! in_array($method, $existing_methods)) {
				$this->EE->db->insert('actions', array('class' => $this->addon_name, 'method' => $method));
			}
		}
	}
	
	private function _uninstall_actions()
	{
		$this->EE->db->like('class', $this->addon_name, 'after')->delete('actions');
	}
	
	private function _install_models()
	{
		foreach (glob(dirname(__FILE__) . "/models/*.php") as $model)
		{
			$model = ucfirst(pathinfo($model, PATHINFO_FILENAME));

			$this->EE->load->model($model);
			if (isset($this->EE->$model->table)) $this->EE->$model->create_table();
		}
	}
	
	private function _uninstall_models()
	{
		foreach (glob(dirname(__FILE__) . "/models/*.php") as $model)
		{
			$model = ucfirst(pathinfo($model, PATHINFO_FILENAME));

			$this->EE->load->model($model);
			if (isset($this->EE->$model->table)) $this->EE->$model->drop_table();
		}
	}
}
