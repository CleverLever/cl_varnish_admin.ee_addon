<?php
class Cl_varnish_admin_model extends CI_Model {

	public $data;

	protected $result;
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->dbforge();
	}

	/**
	 * create_table()
	 * 
	 * Create the database table and keys for this model.
	 *
	 * @return boolean	TRUE/FALSE on success/failure
	 * @author Chris LeBlanc
	 */
	public function create_table() 
	{
		if (empty($this->table)) die('No table defined in ' . get_class($this) . '.');
		$this->dbforge->add_field($this->table_fields);
		foreach($this->primary_keys as $key) $this->dbforge->add_key($key, TRUE);

		$this->dbforge->add_key($this->table_keys);
		return $this->dbforge->create_table($this->table, TRUE);
	}

	/**
	 * drop_table()
	 * 
	 * Drops this model's table from the database.
	 *
	 * @return boolean	TRUE/FALSE on success/failure
	 */
	public function drop_table() 
	{
		return $this->dbforge->drop_table($this->table);
	}
	
	public function get_config()
	{
		return $this->config;
	}
	
	public function get_sites()
	{
		$this->load->model('site_model');
		return $this->site_model->get_site();
	}
	
	/**
	 * get_channels()
	 * 
	 * Returns all channels for this site.
	 *
	 * @return object	CI_DB_result
	 * @author Chris LeBlanc
	 */
	public function get_channels($site_id = NULL, $additional_fields = array(), $additional_where = array())
	{
		$this->load->model('channel_model');
		return $this->channel_model->get_channels(
			$site_id,
			$additional_fields, 
			$additional_where
		);
	}

	/**
	 * get_channel_field()
	 * 
	 * Returns channel field data.
	 *
	 * @return object	CI_DB_active_record
	 * @author Chris LeBlanc
	 */
	public function get_channel_field($field_id)
	{
		$this->load->model('field_model');

		return $this->field_model->get_field($field_id);
	}
	
	/**
	 * get_channel_fields()
	 * 
	 * Returns channel fields for a given channel.
	 *
	 * @param string	$channel_id
	 * @return object	CI_DB_active_record
	 * @author Chris LeBlanc
	 */
	public function get_channel_fields(int $channel_id = NULL)
	{
		$this->load->model(array('channel_model', 'field_model'));
		$channel = $this->channel_model->get_channel_info($channel_id)->row_array();

		return $this->field_model->get_fields($channel['field_group'], 
			array('site_id' => $this->config->item('site_id')));	
	}
	
	/**
	 * get_member_fields()
	 * 
	 * Returns all custom member fields.
	 *
	 * @return object	CI_DB_active_record
	 * @author Chris LeBlanc
	 */
	public function get_member_fields() 
	{
		$this->load->model('member_model');
		return $this->member_model->get_custom_member_fields();
	}

	/**
	 * get_act_url()
	 * 
	 * Returns the site URL with the ACT parameter for a given method and
	 * optionally adds additional passed parameters.
	 *
	 * @param string $method	The module method to return ACT id for.
	 * @param array $data	(optional) associate array of query parameters and their values.
	 * @return string
	 * @author Chris LeBlanc
	 */
	public function get_act_url(string $method, array $data = array()) 
	{
		$query = $this->db->from('exp_actions')
			->where('class', substr(__CLASS__, 0, "-6"))
			->where('method', $method);
		$data['ACT'] = $query->get()->row()->action_id;
		
		return $this->config->item('site_url') . '?' . http_build_query(array_reverse($data));
	}

	/**
	 * get_oldest_superadmin()
	 * 
	 * Returns the oldest super admin. Useful for assigning channel entries to
	 * an arbitrary author.
	 *
	 * @return string
	 * @author Chris LeBlanc
	 */
	public function get_oldest_superadmin()
	{
		$query = $this->db->from('members')
			->select('member_id')
			->where('group_id', 1)
			->order_by('member_id', 'asc')
			->limit(1);

		return $query->get()->row('member_id');
	}
	

	/**
	 * save()
	 * 
	 * Either updates or inserts value to this models table stored in 
	 * $this->data depending on whether the optional $where param is specified.
	 *
	 * @param mixed $where	(optional) The where parameter if performing an update.
	 * @return boolean	TRUE/FALSE on success/failure
	 * @author Chris LeBlanc
	 */
	public function save($where = FALSE)
	{
		if ($where && $this->db->from($this->table)->where($where)->count_all_results() > 0) {
			return $this->db->where($where)->update($this->table, $this->data);
		} else {
			return $this->db->insert($this->table, $this->data); 
		}
	}

	/**
	 * delete()
	 * 
	 * Deletes a value from the table given an id or an array with a key and 
	 * value.
	 *
	 * @param mixed $where 	id or array of key and value
	 * @return boolean	TRUE/FALSE on success/failure
	 * @author Chris LeBlanc
	 */
	public function delete($where)
	{	
		$query = $this->db->from($this->table);

		$query->where($where);

		return $query->delete();
	}
	
	/**
	 * get()
	 *
	 * @param string $id 
	 * @return object	CI_DB_active_record
	 * @author Chris LeBlanc
	 */
	public function get($id = '') 
	{	
		$this->query = $this->db->from($this->table);
		if ($id) $this->query->where('id', $id);

		return $this->query->get();
	}

	public function get_templates($additional_fields = array(), $additional_where = array()) 
	{
		$this->load->model('template_model');
		return $this->template_model->get_templates(
			$this->config->item('site_id'), 
			$additional_fields, 
			$additional_where
		);
	}
	
	public function get_template_groups($additional_fields = array()) 
	{
		$this->load->model('template_model');
		return $this->template_model->get_template_groups();
	}
	
	public function parse_tagdata($entry_id, $tagdata)
	{
		require_once PATH_MOD.'channel/mod.channel.php';

		$this->TMPL->tagdata = $tagdata;
		$this->TMPL->tagparams['entry_id'] = $entry_id;
		$this->TMPL->site_ids = array($this->config->item('site_id'));

		$vars = $this->functions->assign_variables($tagdata);
		$this->TMPL->var_single	= $vars['var_single'];
		$this->TMPL->var_pair		= $vars['var_pair'];

		$channel = new Channel;
		$channel->fetch_custom_channel_fields();
		$channel->fetch_custom_member_fields();
		$channel->build_sql_query();
	

		$channel->query = $this->db->query($channel->sql);
		$channel->parse_channel_entries();

		return $channel->return_data;
	}
	
	public function result_array() 
	{
		return $this->result->result_array();
	}
	
	public function row_array() 
	{
		return $this->result->row_array();
	}

	
}