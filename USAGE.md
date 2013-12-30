Usage
===============

Requirements
-----

Be sure you have the [PECL Varnish extension](http://www.php.net/manual/en/varnish.installation.php) installed.

Installation
-----

Copy `/third_party/cl_varnish_admin.ee_addon` into `/system/expressionengine/third_party`.

Configuration
-----

Set your Varnish server's settings using `Add-ons -> Varnish Admin -> Server Settings`. 
For more information about configuring your Varnish server's administrative interface 
refer to the [-T and -S options for varnishd](https://www.varnish-cache.org/docs/trunk/reference/varnishd.html).

Tags
-----

### {exp:cl_varnish_admin:expire}

Sets ExpressionEngines's response Expire and Cache-control header for the given template for Varnish to interpret.

#### Parameters

+ on/in (required)

  Either the time in seconds or a string interpretable by [strtotime](http://php.net/strtotime). 
  On/in are synonymous and just so your tag might read better.

  Examples: 
    * `{exp:cl_varnish_admin:expire on="tomorrow at 1:00am"}` expires tomorrow at 1:00am (server timezone).
    * `{exp:cl_varnish_admin:expire in="60"}` expires in 60 seconds