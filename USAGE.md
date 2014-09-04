Usage
===============

Requirements
-----

* [Varnish](https://www.varnish-cache.org/trac/wiki/Installation)

Installation
-----

Copy `/third_party/cl_varnish_admin.ee_addon` into `/system/expressionengine/third_party`.

Upgrading from 1.x
-----

See the configuration section below but also note we will clear out your cache items since the new purge ability requires the full URL to be stored.

Configuration
-----

Starting with version 2 Varnish Admin requires your Varnish server to be configured to include the following to support incoming 
requests to purge, ban, and refresh cached items. You can also choose to create an ACL in the Varnish configuration to protect your varnish server.
Refer to [this article]https://www.varnish-software.com/static/book/Cache_invalidation.html for more information.

  Sample default.vcl:

  ```
  acl cache_admin_ips { "127.0.0.1"; }
  sub vcl_recv {
    if (req.request == "PURGE") {
      if (!client.ip ~ cache_admin_ips) { error 405 "Not allowed"; }
      return (lookup);
    }
    if (req.request == "BAN") {
      if (!client.ip ~ cache_admin_ips) { error 405 "Not allowed"; }
      ban("obj.http.x-host ~ " + req.http.x-ban-host +
          " && obj.http.x-url ~ " + req.http.x-ban-url);
        error 200 "Cache Object Banned";
    }
    if (req.request == "REFRESH") {
      set req.request = "GET";
      set req.hash_always_miss = true;
    }
  }
  ...
  sub vcl_hit {
    if (req.request == "PURGE") {
      if (!client.ip ~ cache_admin_ips) { error 405 "Not allowed"; }
      purge;
      error 200 "Cache Object Purged";
    }
  }
  ...
  sub vcl_miss {
    if (req.request == "PURGE") {
      purge;
      error 404 "Cache Object Not Found";
    }
  }
  ...
  sub vcl_fetch {
    set beresp.http.x-url = req.url;
    set beresp.http.x-host = req.http.host;
  }
  ...
  sub vcl_deliver {
    unset resp.http.x-url;
    unset resp.http.x-host;
  }
  ```

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

Tells varnish to process <esi:include... /> tags for a given template. This just sends a header "X-Parse-Esi: 1" to varnish
so you'll want to add the following to your Varnish configuration.

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

### {exp:cl_varnish_admin:warm_expired_cached_items}

Warms expired cached items. Point your cronjob (or browser) to a template with this tag in it and it will run through 
the warming process for cached items. This is just an alternative to using the module action found in the settings. 

#### Parameters

+ delay (default: `0`)

  The delay between warm requests. Useful to prevent your server from getting hammered.
