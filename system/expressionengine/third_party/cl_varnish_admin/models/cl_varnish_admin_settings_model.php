<?php
require_once(__DIR__.'/cl_varnish_admin_model.php');

class Cl_varnish_admin_settings_model extends Cl_varnish_admin_model
{
	public $table = "cl_varnish_admin_settings";

	protected $table_fields = array(
		'site_id'	=> array(
			'type'			=> 'int',
			'constraint'	=> 10,
			'unsigned'		=> TRUE,
			),
		'key'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '30',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'value'  => array(
			'type' 			=> 'text',
			'null'			=> FALSE,
			'default'		=> ''
			),
	);
	protected $primary_keys = array(array("site_id", "key"));
	protected $table_keys = array();
	
	public $data;
	
	protected $query;
	protected $result;
	
	protected $settings = array();

	public function __construct() 
	{
		parent::__construct();
	}
	
	public function get($key = FALSE)
	{
		$this->query = $this->db->from($this->table)
			->where('site_id', $this->config->item('site_id'));
		
		if ($key) 
		{
			$this->result  = $this->query->where('key', $key)->get();
			return ($this->result->num_rows() > 0) ? $this->result->row()->value : @$this->settings[$key];
		} 
		else 
		{
			$this->result = $this->query->get();
			return $this;
		}
	}
	
	public function result()
	{
		foreach ($this->result->result_array() as $row) 
		{
			$this->data->$row['key'] = $row['value'];
		}
		return $this->data;
	}
	
	public function result_array()
	{
		foreach ($this->result->result_array() as $row) 
		{
			$this->data[$row['key']] = $row['value'];
		}
		return $this->data;
	}
	
	
	public function save($key, $value) 
	{
		$this->data['site_id'] = $this->config->item('site_id');		
		$this->data['key'] = $key;
		$this->data['value'] = $value;

		parent::save(array(
			"site_id" => $this->data['site_id'], 
			"key" => $this->data['key']
		));
	}
	
}