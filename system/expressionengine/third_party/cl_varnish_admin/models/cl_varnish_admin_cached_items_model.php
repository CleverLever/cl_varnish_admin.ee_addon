<?php
require_once(__DIR__.'/cl_varnish_admin_model.php');

class Cl_varnish_admin_cached_items_model extends Cl_varnish_admin_model
{
	public $table = "cl_varnish_admin_cached_items";

	protected $table_fields = array(
		'hash' => array(
				'type'			=> 'varchar',
				'constraint'	=> 40,
				'null'			=> FALSE
			),
		'site_id'	=> array(
			'type'			=> 'int',
			'constraint'	=> 10,
			'unsigned'		=> TRUE,
			),
		'uri'	=> array(
			'type' 			=> 'varchar',
			'constraint'	=> '250',
			'null'			=> FALSE,
			'default'		=> ''
			),
		'created' 	=> array(
			'type'			=> 'timestamp',
			'null'			=> FALSE
		),
		'expires' 	=> array(
			'type'			=> 'timestamp',
			'null'			=> FALSE
		),
		'warm'	=> array(
			'type' 			=> 'int',
			'constraint'	=> 1,
			'unsigned'		=> TRUE,
			'null'			=> FALSE,
			'default'		=> 0,
			),
	);
	protected $primary_keys = array("site_id", "uri");
	protected $table_keys = array();
	
	public $data = array();
	
	protected $query;
	protected $result;

	public function __construct() 
	{
		parent::__construct();
	}
	
	public function get($hash)
	{
		return $this->db->from($this->table)
			->where('site_id', $this->config->item('site_id'))
			->where('hash', $hash)->get();
	}
	
	public function collection($where = array(), $limit = FALSE) 
	{
		$this->query = $this->db->from($this->table)
			->where('site_id', $this->config->item('site_id'));
		
		if (!empty($where)) $this->query->where($where);
		if ($limit) $this->query->limit($limit);

		$this->query->order_by('expires', 'asc');

		$this->result = $this->query->get();
		return $this;
	}
	
	public function save($where = FALSE)
	{
		parent::save($where);
	}
	
	public function create_table()
	{
		parent::create_table();
		
		// active record doesn't support CURRENT_TIMESTAMP default values
		$this->db->query('ALTER TABLE ' . $this->db->dbprefix . $this->table . ' CHANGE `created` `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
	}
	
	public function flush_expired()
	{
		return $this->db->where('expires <= NOW()')->delete($this->table);
	}
	
	public function next_expired_item()
	{
		return $this->db->from($this->table)
			->where('site_id', $this->config->item('site_id'))
			->where('expires <= NOW()')
			->order_by('expires', 'asc')
			->limit(1)
			->get();
	}
}