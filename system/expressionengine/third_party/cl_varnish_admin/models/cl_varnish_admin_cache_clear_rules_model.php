<?php
require_once(__DIR__.'/cl_varnish_admin_model.php');

class Cl_varnish_admin_cache_clear_rules_model extends Cl_varnish_admin_model
{
	public $table = "cl_varnish_admin_cache_clear_rules";

	protected $table_fields = array(
		'id'	=> array(
			'type'					=> 'int',
			'constraint'			=> 10,
			'unsigned'				=> TRUE,
			'auto_increment' => TRUE,
			),
		'site_id'	=> array(
			'type'			=> 'int',
			'constraint'	=> 10,
			'unsigned'		=> TRUE,
			),
		'hook'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '30',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'target'	=> array(
			'type'			=> 'int',
			'constraint'	=> 10,
			'unsigned'		=> TRUE,
			),
		'action'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '30',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'options'  => array(
			'type' 			=> 'text',
			'null'			=> FALSE,
			'default'		=> ''
			),
	);
	protected $primary_keys = array("id");
	protected $table_keys = array();
	
	public $data = array();
	
	protected $query;
	protected $result;

	public function __construct() 
	{
		parent::__construct();
	}
	
	public function get($key = false)
	{
		$this->query = $this->db->from($this->table)
			->where('site_id', $this->config->item('site_id'));
		
		if ($key) 
		{
			$this->result  = $this->query->where('key', $key)->get();
			return ($this->result->num_rows() > 0) ? $this->result->row()->value : $this->settings[$key];
		} 
		else 
		{
			$this->result = $this->query->get();
			return $this;
		}
	}
	
	public function get_rules($where = array()) 
	{
		$this->query = $this->db->from($this->table)
			->where('site_id', $this->config->item('site_id'));
		
		if (!empty($where)) $this->query->where($where);

		$this->result = $this->query->get();
		return $this;
	}
	
	public function result_array()
	{
		foreach ($this->result->result_array() as $key => $row) 
		{
			$this->data[$key] = $row;
			$this->data[$key]['options'] = json_decode($this->data[$key]['options'], TRUE);
		}
		return $this->data;
	}
	
	
	public function save($where = array())
	{
		$this->data['site_id'] = $this->config->item('site_id');
		$this->data['options'] = json_encode($this->data['options']);

		parent::save($where);
	}
	
}