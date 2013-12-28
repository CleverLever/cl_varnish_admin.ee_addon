<?php

class Cl_varnish_admin {
	
	public $varnish_config = array();
	
	function __construct() 
	{
		if (!class_exists('VarnishAdmin')) die("Varnish PECL extension not installed.");
		
		$this->EE =& get_instance();
		$this->EE->load->model('Cl_varnish_admin_settings_model');
		
		$this->varnish_config[VARNISH_CONFIG_HOST] = $this->EE->Cl_varnish_admin_settings_model->get('host');
		$this->varnish_config[VARNISH_CONFIG_PORT] = $this->EE->Cl_varnish_admin_settings_model->get('port');
		$this->varnish_config[VARNISH_CONFIG_SECRET] = $this->EE->Cl_varnish_admin_settings_model->get('secret');
		$this->varnish_config[VARNISH_CONFIG_TIMEOUT] = 300;
	}
	
	public function connectAndAuth() 
	{
		$va = new VarnishAdmin($this->varnish_config);

		// connect
		try {
		    if(!$va->connect()) {
		        throw new VarnishException("Connection failed\n");
		    }
		} catch (VarnishException $e) {
		    echo $e->getMessage();
		    exit(3);
		}
		
		// authenticate
		try {
		    if(!$va->auth()) {
		        throw new VarnishException("Auth failed\n");
		    } else {
				return $va;
			} 
		} catch (VarnishException $e) {
		    echo $e->getMessage();
		    exit(3);
		}
	}
	
	public function banUrl($url) 
	{
		$va = $this->connectAndAuth();
		$exp = 'req.http.host == "' . parse_url($this->EE->config->item('site_url'), PHP_URL_HOST) . '" && req.url ~ "' . $url . '"';
		error_log($exp);
		$va->ban($exp);
	}
	
	public function banSite()
	{
		$va = $this->connectAndAuth();
		$exp = 'req.http.host == "' . parse_url($this->EE->config->item('site_url'), PHP_URL_HOST) . '"';

		$va->ban($exp);
	}
	
	public function banAll()
	{
		$va = $this->connectAndAuth();
		$exp = "req.url ~ '^/.+'";

		$va->ban($exp);
	}
	
	public function warmUrl($url)
	{
		$url = "http://" . parse_url($this->EE->config->item('site_url'), PHP_URL_HOST) . $url;
		error_log($url);
		return file($url);
	}
	
}