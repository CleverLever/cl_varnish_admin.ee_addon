<?php

class Cl_varnish_admin_request {
	
	public $varnish_config = array();
	
	function __construct() 
	{
		$this->EE =& get_instance();
		$this->EE->load->model('Cl_varnish_admin_settings_model', 'settings');
	}
	
	public function purge($url) 
	{
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => 'PURGE',
			CURLOPT_MAXREDIRS => 0,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HEADER => TRUE,
		);
		
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		
		$response = curl_exec($ch);
		error_log(print_r($response,TRUE));
		
		curl_close($ch);
	}
	
	public function refresh($url) 
	{
		error_log("Refreshing: " . $url);
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => 'REFRESH',
			CURLOPT_MAXREDIRS => 0,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HEADER => TRUE,
		);
		
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		
		$response = curl_exec($ch);
		error_log(print_r($response,TRUE));
		
		curl_close($ch);
	}
	
	public function ban($host, $url)
	{
		$options = array(
			CURLOPT_URL => $this->EE->config->item('site_url'),
			CURLOPT_CUSTOMREQUEST => 'BAN',
			CURLOPT_MAXREDIRS => 0,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HEADER => TRUE,
			CURLOPT_HTTPHEADER => array(
				'X-Ban-Host: ' . $host,
				'X-Ban-Url: ' . $url,
			),
		);
		
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		
		$response = curl_exec($ch);
		error_log(print_r($response,TRUE));
		
		curl_close($ch);
	}
	
	public function ban_path($path) 
	{
		$host = parse_url($this->EE->config->item('site_url'), PHP_URL_HOST);

		$this->ban($host, $path);
	}
	
	public function ban_host($host)
	{
		$path = '.*';

		$this->ban($host, $path);
	}
	
	public function ban_all()
	{
		$host = '.*';
		$path = '.*';

		$this->ban($host, $path);
	}
	
}