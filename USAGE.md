Usage
===============

Requirements
-----

* [Varnish](https://www.varnish-cache.org/trac/wiki/Installation)
* [PECL Varnish extension*](http://www.php.net/manual/en/varnish.installation.php)

*If you are having trouble installing the PECL Varnish extension you might need to run
`apt-get install libvarnishapi-dev` prior to running the `pecl varnish install` command.

Installation
-----

Copy `/third_party/cl_varnish_admin.ee_addon` into `/system/expressionengine/third_party`.

Configuration
-----

Set your Varnish server's settings using `Add-ons -> Varnish Admin -> Server Settings`. 

For more information about configuring your Varnish server's administrative interface refer to the [-T and -S options for varnishd](https://www.varnish-cache.org/docs/trunk/reference/varnishd.html).

Tags
-----

### {exp:cl_varnish_admin:expire}

Sets ExpressionEngine's response Expire and Cache-control header for the given template for Varnish to interpret.

#### Parameters

+ when (required)

  Either the time in seconds or a string interpretable by [strtotime](http://php.net/strtotime).

  Examples: 
    * `{exp:cl_varnish_admin:expire when="tomorrow at 1:00am"}` expires tomorrow at 1:00am (server timezone).
    * `{exp:cl_varnish_admin:expire when="60"}` expires in 60 seconds

### {exp:cl_varnish_admin:parse_esi}

Tells varnish to processes <esi:include... /> tags for a given template. This send a header "X-Parse-Esi: 1" to varnish.

  Varnish Configuration:

  ```
  sub vcl_fetch {
	...
	if (beresp.http.X-Parse-Esi) {
        set beresp.do_esi = true;
    }
    unset beresp.http.X-Parse-Esi;
    ...
  }
  ```
