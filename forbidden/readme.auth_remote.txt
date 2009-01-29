***************************************************************

                    mod_auth_remote v0.1 -
   a single signon module using basic auth ( for Apache 2.0 & 1.3 )

  Saju R Pillai (saju.pillai@gmail.com)

****************************************************************  

README mod_auth_remote  ( Apache 2.0 authentication module )

This module is a very simple, lightweight method of setting up a single signon
system across multiple web-applicaitions hosted on different servers.

The actual authentication & authorization system is deployed on a single server
instead of each individual server. All other servers are built with mod_auth_remote
enabled. When a request comes in, mod_auth_remote obtains the client username &
password from the client via basic authentication scheme.

It then builds a HTTP header with authorization header built from the client's
userid:passwd. mod_auth_remote then makes a HEAD request to the authentication
server. On reciept of a 2XX response, the client is validated; for all other
responses the client is not validated.

Why I wrote mod_auth_remote ?

I have a bunch of web applications running on a bunch of machines ...

1) My authentication code is heavy & I don't want to implement it on all 
   of your servers. (I use mod_perl and require a Database access to 
   authenticate)

2) Most of  my web applications use a single signon

3) Two different applications running under the same server could access 2
   different authentication models without any pain 

**************************************************************************

INSTALLATION

File: mod_auth_remote.c is for Apache 2.0
File: mod_auth_remote_1.3.c is for Apache 1.3

Load as a DSO or build statically.

***************************************************************************

mod_auth_remote keywords/directives

AuthRemoteServer : The remote server against which the authentication has to take place
AuthRemotePort   : The port on which the remote server is runing
AuthRemoteURL    : The (optional) path on the remote server which has to be accessed
( should have been AuthRemotePath :-) )

As you would have noticed these 3 configuration directives are used to build the
complete URL against which mod_auth_remote authenticates.

*****************************************************************************

Sample Configuration for a httpd (my.server.com)

------------------------------------------

<Directory ~ "/application_1/">
 AuthType           Basic
 AuthName           CHICKEN_RUN
 AuthRemoteServer   auth1.saju.com
 AuthRemotePort     80
 AuthRemoteURL      /One/Auth/method
 require            valid-user
</Directory>

<Directory ~ "/application_2/">
 AuthType           Basic
 AuthName           BIG-CHIEF
 AuthRemoteServer   auth1.saju.com
 AuthRemotePort     80
 AuthRemoteURL      /luke/takes/a/walk
 require            valid-user
</Directory>

<Directory ~ "/application_3/">
 AuthType           Basic
 AuthName           ONE_RING
 AuthRemoteServer   www.sauron.com
 AuthRemotePort     1290
 AuthRemoteURL      /auth
 require            valid-user
</Directory>

---------------------------------------------------

When a request is made to http://my.server.com/application_1, mod_auth_remote uses
the basic auth scheme to get the client's username:passwd and then authenticates the
user against http://auth1.saju.com:80/One/Auth/method using basic auth.

Similiarily a request coming to http://my.server.com/application_3 is automatically
authenticated against http://www.sauron.com:1290/auth

So, the biggest advantage here is that 'my.server.com' can host 3 different
applications having 3 different user sets and 'my.server.com' need not host any sort
of authentication infrastructure (like having access to LDAP server or DB etc), it
need not have any authentication code at all !!

Similarily 10 different servers could access 'auth1.saju.com/<url>' for
authenticating users without having to duplicating the authentication infrastructure
10 times !

******************************************************************************

Linux users

This module is now part of the Mandrake Cooker distrib. You can download this module off any Mandrake mirror

FreeBSD users

This module is now part of the FreeBSD www ports collection. Can be downloaded off any FreeBSD-stable mirror.

Win32
www.gknw.net/development/apache/ httpd-2.0/win32/modules/

Netware
Index of /development/apache/httpd-2.0/netware/modules
