Secure XML-RPC
==============

More secure wrapper for the WordPress XML-RPC interface.

Description
-----------

Rather than sending usernames and passwords in plain text with every request, we're going to use a set of public/secret keys to hash data and authenticate instead.

On your WordPress profile, you will see a new "Remote Publishing Permissions" section listing out the applications that have permission to publish, along with their public and secret keys.

New applications can be added whenever you want.  You can also change the names of applications, or revoke publishing permission by deleting them.

Installation
------------

**Manual Installation**

1. Upload the entire `/secure-xml-rpc` directory to the `/wp-content/plugins/` directory.
2. Activate Secure XML-RPC through the 'Plugins' menu in WordPress.

Frequently Asked Questions
--------------------------

**How do I use the new authorization?**

The old username/password paradigm can still be used, but will result in a `X-Deprecated` header being returned by the server.

From now on, you will send an `Authorization` header.  This header will be the publishing application's public key, two pipe (`|`) characters, and a base64-encoded sha256 hash of the application's secret key concatenated with the body of the request.

***Example***

Say your application has the following information:
* Public Key: b730db0864b0d4453ba6a26ad6613cd4
* Secret Key: 7647a19f5bf3e9fd001419900ad48a54

And you want to make the following request (whitespace/indentation added for readability):

```
<?xml version="1.0"?>
<methodCall>
  <methodName>wp.getPosts</methodName>
  <params>
    <param>
      <value><i4>1</i4></value>
    </param>
    <param>
      <value><string></string></value>
    </param>
    <param>
      <value><string></string></value>
    </param>
  </params>
</methodCall>
```

Note that the second and third parameters (traditionally `username` and `password`) are empty.  Usernames and passwords can still be specified, but will result in the server returning an `X-Deprecated` header.

Your Authorization header would thus become:

`b730db0864b0d4453ba6a26ad6613cd4||f0b73fddf91b2358bc28faa745c8c25d3b0d9a36f5456e8181154c54874d81e5`

The second part of the header is generated by calculating:

`base64( sha256( '7647a19f5bf3e9fd001419900ad48a54' + {request_body} ) )`

WordPress will read the header and log you in as usual, but you never need to send your password across the wire.

In this paradigm, application secret keys should _also_ be treated as passwords - they are sensitive information!

Screenshots
-----------

1. The new Remote Publishing Permissions area of the user profile.

Changelog
---------

**0.1.0**

- First release

Upgrade Notice
--------------

**0.1.0**

First Release

Additional Information
----------------------

Contributors:      ericmann
Donate link:       http://wordpress.org/plugins/secure-xmlrpc
Tags:              xmlrpc, security, oauth, authentication
Requires at least: 3.8
Tested up to:      3.8
Stable tag:        0.1.0
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html