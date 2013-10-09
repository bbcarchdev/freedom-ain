Freedom-ain
===========

This plugin rewrites Wordpress-emitted URIs to be absolute paths instead of
absolute URLs.

e.g.:

http://example.com/2011/01/

becomes

/2011/01

Currently the plugin applies to both the front-end and the administration
back-end, but if it causes issues with the latter it’s trivial to disable
via the Wordpress is_admin() API call.

