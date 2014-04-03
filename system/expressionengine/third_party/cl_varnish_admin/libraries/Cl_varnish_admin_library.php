<?php

class Cl_varnish_admin_library {
	
	public $varnish_config = array();
	
	function __construct() 
	{
		if (!class_exists('VarnishAdmin')) die("Varnish PECL extension not installed.");
		
		$this->EE =& get_instance();
		$this->EE->load->model('Cl_varnish_admin_settings_model', 'settings');
	}
	
	public function connectAndAuth() 
	{
		$this->varnish_config[VARNISH_CONFIG_HOST] = $this->EE->settings->get('host');
		$this->varnish_config[VARNISH_CONFIG_PORT] = $this->EE->settings->get('port');
		$this->varnish_config[VARNISH_CONFIG_SECRET] = $this->EE->settings->get('secret');
		$this->varnish_config[VARNISH_CONFIG_TIMEOUT] = 300;

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

		$va->ban($exp);
	}
	
	public function ban_uri($uri)
	{
		error_log("ban: " . $uri);
		$this->banUrl('^' . $uri . '$');
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
	
	public function warm_uri($uri)
	{
		$url = "http://" . parse_url($this->EE->config->item('site_url'), PHP_URL_HOST) . $uri;
		error_log("warm:" . $url);
		return file($url);
	}
	
}