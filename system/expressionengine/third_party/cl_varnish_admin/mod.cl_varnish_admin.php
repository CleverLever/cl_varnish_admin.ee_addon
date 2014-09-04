<?php

class Cl_varnish_admin
{
	public function __construct() 
	{
		$this->EE =& get_instance();
	}
	
	/**
	 * Cache
	 * 
	 * Sets headers for varnish cache to operate.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	public function expire() 
	{
		$this->EE->load->model('Cl_varnish_admin_cached_items_model', 'cached_items');

		$expires = $this->EE->TMPL->fetch_param('when', FALSE);
		$warm = $this->EE->TMPL->fetch_param('warm', FALSE);
		
		$item = array();
		
		// set cache-control header for Varnish
		$this->EE->TMPL->template_type = 'cp_asset'; // prevent EE from sending it's own headers
		if (is_numeric($expires)) 
		{
			$this->EE->output->set_header('Cache-Control: public, max-age=' . $expires);
		} 
		else
		{
			$expires = strtotime($expires) - time();
			$this->EE->output->set_header('Cache-Control: public, max-age=' . $expires);
		}
	
		// log cached item
		$item['hash'] = sha1($this->EE->config->item('site_url') . $this->EE->uri->uri_string());
		$item['uri'] = $this->EE->config->item('site_url') . $this->EE->uri->uri_string();
		$item['created'] = date('Y-m-d G:i:s');
		$item['expires'] = date('Y-m-d G:i:s', time() + $expires);
		$item['warm'] = ($warm) ? 1 : 0;
		$item['site_id'] = $this->EE->config->item('site_id');
		
		$this->EE->cached_items->data = $item;
		$this->EE->cached_items->save(array('site_id' => $this->EE->config->item('site_id'), 'hash' => $item['hash']));
	}
	
	/**
	 * Cache
	 * 
	 * Sets headers for varnish cache to activate esi parsing
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	public function parse_esi() 
	{
		$this->EE->TMPL->template_type = 'cp_asset'; // prevent EE from sending it's own headers
		$this->EE->output->set_header('X-Parse-Esi: 1');
	}
	
	
	/**
	 * Warm Expired Cached Items
	 * 
	 * Warms expired cache items. In order to prevent slower warms this function redirects to each expired item. When finished a JSON
	 * response is returned.
	 *
	 * @return void
	 * @author Chris LeBlanc
	 */
	public function warm_expired_cached_items()
	{		
		$this->EE->load->model('Cl_varnish_admin_cached_items_model', 'cached_items');
		$this->EE->load->library('cl_varnish_admin_request');
		$this->EE->load->library('template');

		$delay = $this->EE->TMPL->fetch_param('delay', 0);
		
		$this->EE->TMPL->template_type = 'cp_asset'; // prevent EE from sending it's own headers
		$this->EE->output->set_header('Cache-Control: public, max-age=0');

		// clear and warm requested item
		$hash = $this->EE->input->get('hash', FALSE);
		if ($hash)
		{
			$item = $this->EE->cached_items->get($hash)->row_array();
			$this->EE->cl_varnish_admin_request->refresh($item['uri']);
		}

		// redirect to next expired item
		$next_item = $this->EE->cached_items->next_expired_item()->row_array();
		if ($this->EE->cached_items->next_expired_item()->num_rows() > 0)
		{
			sleep($delay);
			$query_string = http_build_query(array('hash' => $next_item['hash']) + $_GET); // this order matters for some stupid reason
			$this->EE->output->set_header('Location: /' . $this->EE->uri->uri_string() . "?" . $query_string);
		} 
		else 
		{
			$this->EE->output->send_ajax_response(array("success" => TRUE));
		}

	}

}