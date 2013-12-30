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
		$expires = $this->EE->TMPL->fetch_param('on', FALSE);
		$expires = $this->EE->TMPL->fetch_param('in', $expires);
		
		$this->EE->TMPL->template_type = 'cp_asset'; // prevent EE from sending it's own headers
		
		if (is_numeric($expires)) 
		{
			if ($expires === 0) 
			{
				$this->EE->output->set_header('Cache-Control: no-cache');
			} 
			else 
			{
				$expires = gmdate("D, d M Y H:i:s", time() + $expires) . " GMT";
				$this->EE->output->set_header('Cache-Control: public, max-age='.$expires);
                $this->EE->output->set_header('Expires: '.$expires);
error_log($expires);
			}
		} 
		else if ($expires)
		{
		    $cache_timestamp = strtotime($expires);
		    $seconds_until_timestamp = $cache_timestamp - time();
		    $expires = gmdate("D, d M Y H:i:s", $cache_timestamp) . " GMT";
			$this->EE->output->set_header('Cache-Control: public, max-age=' . $seconds_until_timestamp);
            $this->EE->output->set_header('Expires: '.$expires);
error_log($expires);
	    }
	}

}