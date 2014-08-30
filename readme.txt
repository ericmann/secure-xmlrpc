=== Secure XML-RPC ===
Contributors:      ericmann
Donate link:       http://wordpress.org/plugins/secure-xmlrpc
Tags:              xmlrpc, security, oauth, authentication
Requires at least: 3.8
Tested up to:      4.0
Stable tag:        1.0.0
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

More secure wrapper for the WordPress XML-RPC interface.

== Description ==

Rather than sending usernames and passwords in plain text with every request, we're going to use a set of public/secret keys to hash data and authenticate instead.

On your WordPress profile, you will see a new "Remote Publishing Permissions" section listing out the applications that have permission to publish, along with their public and secret keys.

New applications can be added whenever you want.  You can also change the names of applications, or revoke publishing permission by deleting them.

== Installation ==

= Manual Installation =

1. Upload the entire `/secure-xml-rpc` directory to the `/wp-content/plugins/` directory.
2. Activate Secure XML-RPC through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= How do I use the new authorization? =

The old username/password paradigm can still be used, but will result in a `X-Deprecated` header being returned by the server.

From now on, you will send an `Authorization` header.  This header will be the publishing application's public key, two pipe (`|`) characters, and a hash of the application's secret key concatenated with the body of the request.

= How do I generate the message hash? =

Say your application has the following information:
* Public Key: b730db0864b0d4453ba6a26ad6613cd4
* Secret Key: 7647a19f5bf3e9fd001419900ad48a54

And you want to make the following request (whitespace/indentation added for readability, but is removed when calculating hashes):

`<?xml version="1.0"?>
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
</methodCall>`

Note that the second and third parameters (traditionally `username` and `password`) are empty.  Usernames and passwords can still be specified, but will result in the server returning an `X-Deprecated` header.

Your Authorization header would thus become:

`b730db0864b0d4453ba6a26ad6613cd4||3fac15f99f7a178f922bcc4942e62dc9001b2a45118fc3a6f3aebd77d25f4d58`

The second part of the header is generated in PHP by calculating:

`hash( 'sha256', '7647a19f5bf3e9fd001419900ad48a54' . hash( 'sha256', '7647a19f5bf3e9fd001419900ad48a54' . {request_body} ) )`

WordPress will read the header and log you in as usual, but you never need to send your password across the wire.

In this paradigm, application secret keys should _also_ be treated as passwords - they are sensitive information!

= Why are we using the secret key twice? =

Some developers raised concerns about [length extension attacks](https://blog.whitehatsec.com/hash-length-extension-attacks/) in previous editions of the plugin. While length extension isn't strictly necessary when dealing with XML-based messaging, a double hash helps end the discussion around potentially-related vulnerabilities.

The double-hash is similar to but simpler than HMAC and is fairly easy to implement in any programming language. Just note, PHP's `hash()` function returns a base64-encoded string, not a raw hash of the data passed in.

= Do I have to copy/paste my application keys into remote systems? =

Not necessarily.

The latest version of the plugin adds a new XML-RPC method to the system that allows for the generation of user-specific application keys remotely. _Please only ever call this method over a secure/trusted network connection_ when setting up an application for the first time.

== Screenshots ==

1. The new Remote Publishing Permissions area of the user profile.

== Changelog ==

= 1.0.0 =
* New: Add a custom RPC method for generating application keys remotely.
* Dev change: Move all functional implementations inside our pseudo-namespace.
* Dev change: Use a constant-time string comparison method for better security and less data leakage during authentication.
* Dev change: Use a double-hash to prevent any potential length-extension attacks.

= 0.1.0 =
* First release

== Upgrade Notice ==

= 1.0.0 =
The hashing mechanism for generating authentication headers has changed slightly. Please refer to the FAWs for an example of how things work with a double-hash in the newest version.

= 0.1.0 =
First Release

== Additional Information ==

Lock graphic designed by Scott Lewis from the thenounproject.com