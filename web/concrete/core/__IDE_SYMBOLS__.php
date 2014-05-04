<?php
namespace {
    die('Intended for use with IDE symbol matching only.');

    /**
     * An object that represents a particular request to the Concrete-powered website. The request object then determines what is being requested, based on the path, and presents itself to the rest of the dispatcher (which loads the page, etc...)
     * @package Core
     * @author Andrew Embler <andrew@concrete5.org>
     * @category Concrete
     * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
     * @license    http://www.concrete5.org/license/     MIT License
     *
     */
    class Request extends \Concrete\Core\Http\Request
    {

        public static function getCurrentPage()
        {
            // Concrete\Core\Http\Request::getCurrentPage();
            Concrete\Core\Http\Request::getCurrentPage();
        }

        public static function setCurrentPage(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Http\Request::setCurrentPage();
            Concrete\Core\Http\Request::setCurrentPage($c);
        }

        public static function getInstance()
        {
            // Concrete\Core\Http\Request::getInstance();
            Concrete\Core\Http\Request::getInstance();
        }

        /**
         * Returns the full path for a request
         */
        public static function getPath()
        {
            // Concrete\Core\Http\Request::getPath();
            Concrete\Core\Http\Request::getPath();
        }

        public static function setCustomRequestUser($ui)
        {
            // Concrete\Core\Http\Request::setCustomRequestUser();
            Concrete\Core\Http\Request::setCustomRequestUser($ui);
        }

        public static function getCustomRequestUser()
        {
            // Concrete\Core\Http\Request::getCustomRequestUser();
            Concrete\Core\Http\Request::getCustomRequestUser();
        }

        public static function hasCustomRequestUser()
        {
            // Concrete\Core\Http\Request::hasCustomRequestUser();
            Concrete\Core\Http\Request::hasCustomRequestUser();
        }

        public static function getCustomRequestDateTime()
        {
            // Concrete\Core\Http\Request::getCustomRequestDateTime();
            Concrete\Core\Http\Request::getCustomRequestDateTime();
        }

        public static function setCustomRequestDateTime($date)
        {
            // Concrete\Core\Http\Request::setCustomRequestDateTime();
            Concrete\Core\Http\Request::setCustomRequestDateTime($date);
        }

        /**
         * Determines whether a request matches a particular pattern
         */
        public static function matches($pattern)
        {
            // Concrete\Core\Http\Request::matches();
            Concrete\Core\Http\Request::matches($pattern);
        }

        /**
         * If no arguments are passed, returns the post array. If a key is passed, it returns the value as it exists in the post array.
         * If a default value is provided and the key does not exist in the POST array, the default value is returned
         * @param string $key
         * @param mixed $defaultValue
         * @return mixed $value
         */
        public static function post($key = null, $defaultValue = null)
        {
            // Concrete\Core\Http\Request::post();
            Concrete\Core\Http\Request::post($key, $defaultValue);
        }

        public static function request($key = null)
        {
            // Concrete\Core\Http\Request::request();
            Concrete\Core\Http\Request::request($key);
        }

        public static function isPost()
        {
            // Concrete\Core\Http\Request::isPost();
            Concrete\Core\Http\Request::isPost();
        }

        /**
         * Constructor.
         *
         * @param array  $query      The GET parameters
         * @param array  $request    The POST parameters
         * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
         * @param array  $cookies    The COOKIE parameters
         * @param array  $files      The FILES parameters
         * @param array  $server     The SERVER parameters
         * @param string $content    The raw body data
         *
         * @api
         */
        public static function __construct(array $query = "", array $request = "", array $attributes = "", array $cookies = "", array $files = "", array $server = "", $content = null)
        {
            // Symfony\Component\HttpFoundation\Request::__construct();
            Symfony\Component\HttpFoundation\Request::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        }

        /**
         * Sets the parameters for this request.
         *
         * This method also re-initializes all properties.
         *
         * @param array  $query      The GET parameters
         * @param array  $request    The POST parameters
         * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
         * @param array  $cookies    The COOKIE parameters
         * @param array  $files      The FILES parameters
         * @param array  $server     The SERVER parameters
         * @param string $content    The raw body data
         *
         * @api
         */
        public static function initialize(array $query = "", array $request = "", array $attributes = "", array $cookies = "", array $files = "", array $server = "", $content = null)
        {
            // Symfony\Component\HttpFoundation\Request::initialize();
            Symfony\Component\HttpFoundation\Request::initialize($query, $request, $attributes, $cookies, $files, $server, $content);
        }

        /**
         * Creates a new request with values from PHP's super globals.
         *
         * @return Request A new request
         *
         * @api
         */
        public static function createFromGlobals()
        {
            // Symfony\Component\HttpFoundation\Request::createFromGlobals();
            Symfony\Component\HttpFoundation\Request::createFromGlobals();
        }

        /**
         * Creates a Request based on a given URI and configuration.
         *
         * @param string $uri        The URI
         * @param string $method     The HTTP method
         * @param array  $parameters The query (GET) or request (POST) parameters
         * @param array  $cookies    The request cookies ($_COOKIE)
         * @param array  $files      The request files ($_FILES)
         * @param array  $server     The server parameters ($_SERVER)
         * @param string $content    The raw body data
         *
         * @return Request A Request instance
         *
         * @api
         */
        public static function create($uri, $method = "GET", $parameters = null, $cookies = null, $files = null, $server = null, $content = null)
        {
            // Symfony\Component\HttpFoundation\Request::create();
            Symfony\Component\HttpFoundation\Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
        }

        /**
         * Clones a request and overrides some of its parameters.
         *
         * @param array $query      The GET parameters
         * @param array $request    The POST parameters
         * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
         * @param array $cookies    The COOKIE parameters
         * @param array $files      The FILES parameters
         * @param array $server     The SERVER parameters
         *
         * @return Request The duplicated request
         *
         * @api
         */
        public static function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
        {
            // Symfony\Component\HttpFoundation\Request::duplicate();
            Symfony\Component\HttpFoundation\Request::duplicate($query, $request, $attributes, $cookies, $files, $server);
        }

        /**
         * Returns the request as a string.
         *
         * @return string The request
         */
        public static function __toString()
        {
            // Symfony\Component\HttpFoundation\Request::__toString();
            Symfony\Component\HttpFoundation\Request::__toString();
        }

        /**
         * Overrides the PHP global variables according to this request instance.
         *
         * It overrides $_GET, $_POST, $_REQUEST, $_SERVER, $_COOKIE.
         * $_FILES is never override, see rfc1867
         *
         * @api
         */
        public static function overrideGlobals()
        {
            // Symfony\Component\HttpFoundation\Request::overrideGlobals();
            Symfony\Component\HttpFoundation\Request::overrideGlobals();
        }

        /**
         * Trusts $_SERVER entries coming from proxies.
         *
         * @deprecated Deprecated since version 2.0, to be removed in 2.3. Use setTrustedProxies instead.
         */
        public static function trustProxyData()
        {
            // Symfony\Component\HttpFoundation\Request::trustProxyData();
            Symfony\Component\HttpFoundation\Request::trustProxyData();
        }

        /**
         * Sets a list of trusted proxies.
         *
         * You should only list the reverse proxies that you manage directly.
         *
         * @param array $proxies A list of trusted proxies
         *
         * @api
         */
        public static function setTrustedProxies(array $proxies)
        {
            // Symfony\Component\HttpFoundation\Request::setTrustedProxies();
            Symfony\Component\HttpFoundation\Request::setTrustedProxies($proxies);
        }

        /**
         * Sets a list of trusted host patterns.
         *
         * You should only list the hosts you manage using regexs.
         *
         * @param array $hostPatterns A list of trusted host patterns
         */
        public static function setTrustedHosts(array $hostPatterns)
        {
            // Symfony\Component\HttpFoundation\Request::setTrustedHosts();
            Symfony\Component\HttpFoundation\Request::setTrustedHosts($hostPatterns);
        }

        /**
         * Gets the list of trusted host patterns.
         *
         * @return array An array of trusted host patterns.
         */
        public static function getTrustedHosts()
        {
            // Symfony\Component\HttpFoundation\Request::getTrustedHosts();
            Symfony\Component\HttpFoundation\Request::getTrustedHosts();
        }

        /**
         * Sets the name for trusted headers.
         *
         * The following header keys are supported:
         *
         *  * Request::HEADER_CLIENT_IP:    defaults to X-Forwarded-For   (see getClientIp())
         *  * Request::HEADER_CLIENT_HOST:  defaults to X-Forwarded-Host  (see getClientHost())
         *  * Request::HEADER_CLIENT_PORT:  defaults to X-Forwarded-Port  (see getClientPort())
         *  * Request::HEADER_CLIENT_PROTO: defaults to X-Forwarded-Proto (see getScheme() and isSecure())
         *
         * Setting an empty value allows to disable the trusted header for the given key.
         *
         * @param string $key   The header key
         * @param string $value The header name
         */
        public static function setTrustedHeaderName($key, $value)
        {
            // Symfony\Component\HttpFoundation\Request::setTrustedHeaderName();
            Symfony\Component\HttpFoundation\Request::setTrustedHeaderName($key, $value);
        }

        /**
         * Returns true if $_SERVER entries coming from proxies are trusted,
         * false otherwise.
         *
         * @return boolean
         */
        public static function isProxyTrusted()
        {
            // Symfony\Component\HttpFoundation\Request::isProxyTrusted();
            Symfony\Component\HttpFoundation\Request::isProxyTrusted();
        }

        /**
         * Normalizes a query string.
         *
         * It builds a normalized query string, where keys/value pairs are alphabetized,
         * have consistent escaping and unneeded delimiters are removed.
         *
         * @param string $qs Query string
         *
         * @return string A normalized query string for the Request
         */
        public static function normalizeQueryString($qs)
        {
            // Symfony\Component\HttpFoundation\Request::normalizeQueryString();
            Symfony\Component\HttpFoundation\Request::normalizeQueryString($qs);
        }

        /**
         * Gets a "parameter" value.
         *
         * This method is mainly useful for libraries that want to provide some flexibility.
         *
         * Order of precedence: GET, PATH, POST
         *
         * Avoid using this method in controllers:
         *
         *  * slow
         *  * prefer to get from a "named" source
         *
         * It is better to explicitly get request parameters from the appropriate
         * public property instead (query, attributes, request).
         *
         * @param string  $key     the key
         * @param mixed   $default the default value
         * @param Boolean $deep    is parameter deep in multidimensional array
         *
         * @return mixed
         */
        public static function get($key, $default = null, $deep = null)
        {
            // Symfony\Component\HttpFoundation\Request::get();
            Symfony\Component\HttpFoundation\Request::get($key, $default, $deep);
        }

        /**
         * Gets the Session.
         *
         * @return SessionInterface|null The session
         *
         * @api
         */
        public static function getSession()
        {
            // Symfony\Component\HttpFoundation\Request::getSession();
            Symfony\Component\HttpFoundation\Request::getSession();
        }

        /**
         * Whether the request contains a Session which was started in one of the
         * previous requests.
         *
         * @return Boolean
         *
         * @api
         */
        public static function hasPreviousSession()
        {
            // Symfony\Component\HttpFoundation\Request::hasPreviousSession();
            Symfony\Component\HttpFoundation\Request::hasPreviousSession();
        }

        /**
         * Whether the request contains a Session object.
         *
         * This method does not give any information about the state of the session object,
         * like whether the session is started or not. It is just a way to check if this Request
         * is associated with a Session instance.
         *
         * @return Boolean true when the Request contains a Session object, false otherwise
         *
         * @api
         */
        public static function hasSession()
        {
            // Symfony\Component\HttpFoundation\Request::hasSession();
            Symfony\Component\HttpFoundation\Request::hasSession();
        }

        /**
         * Sets the Session.
         *
         * @param SessionInterface $session The Session
         *
         * @api
         */
        public static function setSession(Symfony\Component\HttpFoundation\Session\SessionInterface $session)
        {
            // Symfony\Component\HttpFoundation\Request::setSession();
            Symfony\Component\HttpFoundation\Request::setSession($session);
        }

        /**
         * Returns the client IP address.
         *
         * This method can read the client IP address from the "X-Forwarded-For" header
         * when trusted proxies were set via "setTrustedProxies()". The "X-Forwarded-For"
         * header value is a comma+space separated list of IP addresses, the left-most
         * being the original client, and each successive proxy that passed the request
         * adding the IP address where it received the request from.
         *
         * If your reverse proxy uses a different header name than "X-Forwarded-For",
         * ("Client-Ip" for instance), configure it via "setTrustedHeaderName()" with
         * the "client-ip" key.
         *
         * @return string The client IP address
         *
         * @see http://en.wikipedia.org/wiki/X-Forwarded-For
         *
         * @deprecated The proxy argument is deprecated since version 2.0 and will be removed in 2.3. Use setTrustedProxies instead.
         *
         * @api
         */
        public static function getClientIp()
        {
            // Symfony\Component\HttpFoundation\Request::getClientIp();
            Symfony\Component\HttpFoundation\Request::getClientIp();
        }

        /**
         * Returns current script name.
         *
         * @return string
         *
         * @api
         */
        public static function getScriptName()
        {
            // Symfony\Component\HttpFoundation\Request::getScriptName();
            Symfony\Component\HttpFoundation\Request::getScriptName();
        }

        /**
         * Returns the path being requested relative to the executed script.
         *
         * The path info always starts with a /.
         *
         * Suppose this request is instantiated from /mysite on localhost:
         *
         *  * http://localhost/mysite              returns an empty string
         *  * http://localhost/mysite/about        returns '/about'
         *  * htpp://localhost/mysite/enco%20ded   returns '/enco%20ded'
         *  * http://localhost/mysite/about?var=1  returns '/about'
         *
         * @return string The raw path (i.e. not urldecoded)
         *
         * @api
         */
        public static function getPathInfo()
        {
            // Symfony\Component\HttpFoundation\Request::getPathInfo();
            Symfony\Component\HttpFoundation\Request::getPathInfo();
        }

        /**
         * Returns the root path from which this request is executed.
         *
         * Suppose that an index.php file instantiates this request object:
         *
         *  * http://localhost/index.php         returns an empty string
         *  * http://localhost/index.php/page    returns an empty string
         *  * http://localhost/web/index.php     returns '/web'
         *  * http://localhost/we%20b/index.php  returns '/we%20b'
         *
         * @return string The raw path (i.e. not urldecoded)
         *
         * @api
         */
        public static function getBasePath()
        {
            // Symfony\Component\HttpFoundation\Request::getBasePath();
            Symfony\Component\HttpFoundation\Request::getBasePath();
        }

        /**
         * Returns the root url from which this request is executed.
         *
         * The base URL never ends with a /.
         *
         * This is similar to getBasePath(), except that it also includes the
         * script filename (e.g. index.php) if one exists.
         *
         * @return string The raw url (i.e. not urldecoded)
         *
         * @api
         */
        public static function getBaseUrl()
        {
            // Symfony\Component\HttpFoundation\Request::getBaseUrl();
            Symfony\Component\HttpFoundation\Request::getBaseUrl();
        }

        /**
         * Gets the request's scheme.
         *
         * @return string
         *
         * @api
         */
        public static function getScheme()
        {
            // Symfony\Component\HttpFoundation\Request::getScheme();
            Symfony\Component\HttpFoundation\Request::getScheme();
        }

        /**
         * Returns the port on which the request is made.
         *
         * This method can read the client port from the "X-Forwarded-Port" header
         * when trusted proxies were set via "setTrustedProxies()".
         *
         * The "X-Forwarded-Port" header must contain the client port.
         *
         * If your reverse proxy uses a different header name than "X-Forwarded-Port",
         * configure it via "setTrustedHeaderName()" with the "client-port" key.
         *
         * @return string
         *
         * @api
         */
        public static function getPort()
        {
            // Symfony\Component\HttpFoundation\Request::getPort();
            Symfony\Component\HttpFoundation\Request::getPort();
        }

        /**
         * Returns the user.
         *
         * @return string|null
         */
        public static function getUser()
        {
            // Symfony\Component\HttpFoundation\Request::getUser();
            Symfony\Component\HttpFoundation\Request::getUser();
        }

        /**
         * Returns the password.
         *
         * @return string|null
         */
        public static function getPassword()
        {
            // Symfony\Component\HttpFoundation\Request::getPassword();
            Symfony\Component\HttpFoundation\Request::getPassword();
        }

        /**
         * Gets the user info.
         *
         * @return string A user name and, optionally, scheme-specific information about how to gain authorization to access the server
         */
        public static function getUserInfo()
        {
            // Symfony\Component\HttpFoundation\Request::getUserInfo();
            Symfony\Component\HttpFoundation\Request::getUserInfo();
        }

        /**
         * Returns the HTTP host being requested.
         *
         * The port name will be appended to the host if it's non-standard.
         *
         * @return string
         *
         * @api
         */
        public static function getHttpHost()
        {
            // Symfony\Component\HttpFoundation\Request::getHttpHost();
            Symfony\Component\HttpFoundation\Request::getHttpHost();
        }

        /**
         * Returns the requested URI.
         *
         * @return string The raw URI (i.e. not urldecoded)
         *
         * @api
         */
        public static function getRequestUri()
        {
            // Symfony\Component\HttpFoundation\Request::getRequestUri();
            Symfony\Component\HttpFoundation\Request::getRequestUri();
        }

        /**
         * Gets the scheme and HTTP host.
         *
         * If the URL was called with basic authentication, the user
         * and the password are not added to the generated string.
         *
         * @return string The scheme and HTTP host
         */
        public static function getSchemeAndHttpHost()
        {
            // Symfony\Component\HttpFoundation\Request::getSchemeAndHttpHost();
            Symfony\Component\HttpFoundation\Request::getSchemeAndHttpHost();
        }

        /**
         * Generates a normalized URI for the Request.
         *
         * @return string A normalized URI for the Request
         *
         * @see getQueryString()
         *
         * @api
         */
        public static function getUri()
        {
            // Symfony\Component\HttpFoundation\Request::getUri();
            Symfony\Component\HttpFoundation\Request::getUri();
        }

        /**
         * Generates a normalized URI for the given path.
         *
         * @param string $path A path to use instead of the current one
         *
         * @return string The normalized URI for the path
         *
         * @api
         */
        public static function getUriForPath($path)
        {
            // Symfony\Component\HttpFoundation\Request::getUriForPath();
            Symfony\Component\HttpFoundation\Request::getUriForPath($path);
        }

        /**
         * Generates the normalized query string for the Request.
         *
         * It builds a normalized query string, where keys/value pairs are alphabetized
         * and have consistent escaping.
         *
         * @return string|null A normalized query string for the Request
         *
         * @api
         */
        public static function getQueryString()
        {
            // Symfony\Component\HttpFoundation\Request::getQueryString();
            Symfony\Component\HttpFoundation\Request::getQueryString();
        }

        /**
         * Checks whether the request is secure or not.
         *
         * This method can read the client port from the "X-Forwarded-Proto" header
         * when trusted proxies were set via "setTrustedProxies()".
         *
         * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
         *
         * If your reverse proxy uses a different header name than "X-Forwarded-Proto"
         * ("SSL_HTTPS" for instance), configure it via "setTrustedHeaderName()" with
         * the "client-proto" key.
         *
         * @return Boolean
         *
         * @api
         */
        public static function isSecure()
        {
            // Symfony\Component\HttpFoundation\Request::isSecure();
            Symfony\Component\HttpFoundation\Request::isSecure();
        }

        /**
         * Returns the host name.
         *
         * This method can read the client port from the "X-Forwarded-Host" header
         * when trusted proxies were set via "setTrustedProxies()".
         *
         * The "X-Forwarded-Host" header must contain the client host name.
         *
         * If your reverse proxy uses a different header name than "X-Forwarded-Host",
         * configure it via "setTrustedHeaderName()" with the "client-host" key.
         *
         * @return string
         *
         * @throws \UnexpectedValueException when the host name is invalid
         *
         * @api
         */
        public static function getHost()
        {
            // Symfony\Component\HttpFoundation\Request::getHost();
            Symfony\Component\HttpFoundation\Request::getHost();
        }

        /**
         * Sets the request method.
         *
         * @param string $method
         *
         * @api
         */
        public static function setMethod($method)
        {
            // Symfony\Component\HttpFoundation\Request::setMethod();
            Symfony\Component\HttpFoundation\Request::setMethod($method);
        }

        /**
         * Gets the request method.
         *
         * The method is always an uppercased string.
         *
         * @return string The request method
         *
         * @api
         */
        public static function getMethod()
        {
            // Symfony\Component\HttpFoundation\Request::getMethod();
            Symfony\Component\HttpFoundation\Request::getMethod();
        }

        /**
         * Gets the mime type associated with the format.
         *
         * @param string $format The format
         *
         * @return string The associated mime type (null if not found)
         *
         * @api
         */
        public static function getMimeType($format)
        {
            // Symfony\Component\HttpFoundation\Request::getMimeType();
            Symfony\Component\HttpFoundation\Request::getMimeType($format);
        }

        /**
         * Gets the format associated with the mime type.
         *
         * @param string $mimeType The associated mime type
         *
         * @return string|null The format (null if not found)
         *
         * @api
         */
        public static function getFormat($mimeType)
        {
            // Symfony\Component\HttpFoundation\Request::getFormat();
            Symfony\Component\HttpFoundation\Request::getFormat($mimeType);
        }

        /**
         * Associates a format with mime types.
         *
         * @param string       $format    The format
         * @param string|array $mimeTypes The associated mime types (the preferred one must be the first as it will be used as the content type)
         *
         * @api
         */
        public static function setFormat($format, $mimeTypes)
        {
            // Symfony\Component\HttpFoundation\Request::setFormat();
            Symfony\Component\HttpFoundation\Request::setFormat($format, $mimeTypes);
        }

        /**
         * Gets the request format.
         *
         * Here is the process to determine the format:
         *
         *  * format defined by the user (with setRequestFormat())
         *  * _format request parameter
         *  * $default
         *
         * @param string $default The default format
         *
         * @return string The request format
         *
         * @api
         */
        public static function getRequestFormat($default = "html")
        {
            // Symfony\Component\HttpFoundation\Request::getRequestFormat();
            Symfony\Component\HttpFoundation\Request::getRequestFormat($default);
        }

        /**
         * Sets the request format.
         *
         * @param string $format The request format.
         *
         * @api
         */
        public static function setRequestFormat($format)
        {
            // Symfony\Component\HttpFoundation\Request::setRequestFormat();
            Symfony\Component\HttpFoundation\Request::setRequestFormat($format);
        }

        /**
         * Gets the format associated with the request.
         *
         * @return string|null The format (null if no content type is present)
         *
         * @api
         */
        public static function getContentType()
        {
            // Symfony\Component\HttpFoundation\Request::getContentType();
            Symfony\Component\HttpFoundation\Request::getContentType();
        }

        /**
         * Sets the default locale.
         *
         * @param string $locale
         *
         * @api
         */
        public static function setDefaultLocale($locale)
        {
            // Symfony\Component\HttpFoundation\Request::setDefaultLocale();
            Symfony\Component\HttpFoundation\Request::setDefaultLocale($locale);
        }

        /**
         * Sets the locale.
         *
         * @param string $locale
         *
         * @api
         */
        public static function setLocale($locale)
        {
            // Symfony\Component\HttpFoundation\Request::setLocale();
            Symfony\Component\HttpFoundation\Request::setLocale($locale);
        }

        /**
         * Get the locale.
         *
         * @return string
         */
        public static function getLocale()
        {
            // Symfony\Component\HttpFoundation\Request::getLocale();
            Symfony\Component\HttpFoundation\Request::getLocale();
        }

        /**
         * Checks if the request method is of specified type.
         *
         * @param string $method Uppercase request method (GET, POST etc).
         *
         * @return Boolean
         */
        public static function isMethod($method)
        {
            // Symfony\Component\HttpFoundation\Request::isMethod();
            Symfony\Component\HttpFoundation\Request::isMethod($method);
        }

        /**
         * Checks whether the method is safe or not.
         *
         * @return Boolean
         *
         * @api
         */
        public static function isMethodSafe()
        {
            // Symfony\Component\HttpFoundation\Request::isMethodSafe();
            Symfony\Component\HttpFoundation\Request::isMethodSafe();
        }

        /**
         * Returns the request body content.
         *
         * @param Boolean $asResource If true, a resource will be returned
         *
         * @return string|resource The request body content or a resource to read the body stream.
         */
        public static function getContent($asResource = null)
        {
            // Symfony\Component\HttpFoundation\Request::getContent();
            Symfony\Component\HttpFoundation\Request::getContent($asResource);
        }

        /**
         * Gets the Etags.
         *
         * @return array The entity tags
         */
        public static function getETags()
        {
            // Symfony\Component\HttpFoundation\Request::getETags();
            Symfony\Component\HttpFoundation\Request::getETags();
        }

        /**
         * @return Boolean
         */
        public static function isNoCache()
        {
            // Symfony\Component\HttpFoundation\Request::isNoCache();
            Symfony\Component\HttpFoundation\Request::isNoCache();
        }

        /**
         * Returns the preferred language.
         *
         * @param array $locales An array of ordered available locales
         *
         * @return string|null The preferred locale
         *
         * @api
         */
        public static function getPreferredLanguage(array $locales = null)
        {
            // Symfony\Component\HttpFoundation\Request::getPreferredLanguage();
            Symfony\Component\HttpFoundation\Request::getPreferredLanguage($locales);
        }

        /**
         * Gets a list of languages acceptable by the client browser.
         *
         * @return array Languages ordered in the user browser preferences
         *
         * @api
         */
        public static function getLanguages()
        {
            // Symfony\Component\HttpFoundation\Request::getLanguages();
            Symfony\Component\HttpFoundation\Request::getLanguages();
        }

        /**
         * Gets a list of charsets acceptable by the client browser.
         *
         * @return array List of charsets in preferable order
         *
         * @api
         */
        public static function getCharsets()
        {
            // Symfony\Component\HttpFoundation\Request::getCharsets();
            Symfony\Component\HttpFoundation\Request::getCharsets();
        }

        /**
         * Gets a list of content types acceptable by the client browser
         *
         * @return array List of content types in preferable order
         *
         * @api
         */
        public static function getAcceptableContentTypes()
        {
            // Symfony\Component\HttpFoundation\Request::getAcceptableContentTypes();
            Symfony\Component\HttpFoundation\Request::getAcceptableContentTypes();
        }

        /**
         * Returns true if the request is a XMLHttpRequest.
         *
         * It works if your JavaScript library set an X-Requested-With HTTP header.
         * It is known to work with common JavaScript frameworks:
         * @link http://en.wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
         *
         * @return Boolean true if the request is an XMLHttpRequest, false otherwise
         *
         * @api
         */
        public static function isXmlHttpRequest()
        {
            // Symfony\Component\HttpFoundation\Request::isXmlHttpRequest();
            Symfony\Component\HttpFoundation\Request::isXmlHttpRequest();
        }

        /**
         * Splits an Accept-* HTTP header.
         *
         * @param string $header Header to split
         *
         * @return array Array indexed by the values of the Accept-* header in preferred order
         */
        public static function splitHttpAcceptHeader($header)
        {
            // Symfony\Component\HttpFoundation\Request::splitHttpAcceptHeader();
            Symfony\Component\HttpFoundation\Request::splitHttpAcceptHeader($header);
        }

        protected static function prepareRequestUri()
        {
            // Symfony\Component\HttpFoundation\Request::prepareRequestUri();
            Symfony\Component\HttpFoundation\Request::prepareRequestUri();
        }

        /**
         * Prepares the base URL.
         *
         * @return string
         */
        protected static function prepareBaseUrl()
        {
            // Symfony\Component\HttpFoundation\Request::prepareBaseUrl();
            Symfony\Component\HttpFoundation\Request::prepareBaseUrl();
        }

        /**
         * Prepares the base path.
         *
         * @return string base path
         */
        protected static function prepareBasePath()
        {
            // Symfony\Component\HttpFoundation\Request::prepareBasePath();
            Symfony\Component\HttpFoundation\Request::prepareBasePath();
        }

        /**
         * Prepares the path info.
         *
         * @return string path info
         */
        protected static function preparePathInfo()
        {
            // Symfony\Component\HttpFoundation\Request::preparePathInfo();
            Symfony\Component\HttpFoundation\Request::preparePathInfo();
        }

        /**
         * Initializes HTTP request formats.
         */
        protected static function initializeFormats()
        {
            // Symfony\Component\HttpFoundation\Request::initializeFormats();
            Symfony\Component\HttpFoundation\Request::initializeFormats();
        }

    }

    /**
     * Useful functions for getting paths for concrete5 items.
     * @package Core
     * @author Andrew Embler <andrew@concrete5.org>
     * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
     * @license    http://www.concrete5.org/license/     MIT License
     */
    class Environment extends \Concrete\Core\Foundation\Environment
    {

        public static function get()
        {
            // Concrete\Core\Foundation\Environment::get();
            Concrete\Core\Foundation\Environment::get();
        }

        public static function saveCachedEnvironmentObject()
        {
            // Concrete\Core\Foundation\Environment::saveCachedEnvironmentObject();
            Concrete\Core\Foundation\Environment::saveCachedEnvironmentObject();
        }

        public static function clearOverrideCache()
        {
            // Concrete\Core\Foundation\Environment::clearOverrideCache();
            Concrete\Core\Foundation\Environment::clearOverrideCache();
        }

        public static function reset()
        {
            // Concrete\Core\Foundation\Environment::reset();
            Concrete\Core\Foundation\Environment::reset();
        }

        /**
         * Builds a list of all overrides
         */
        protected static function getOverrides()
        {
            // Concrete\Core\Foundation\Environment::getOverrides();
            Concrete\Core\Foundation\Environment::getOverrides();
        }

        public static function getDirectoryContents($dir, $ignoreFilesArray = null, $recursive = null)
        {
            // Concrete\Core\Foundation\Environment::getDirectoryContents();
            Concrete\Core\Foundation\Environment::getDirectoryContents($dir, $ignoreFilesArray, $recursive);
        }

        public static function overrideCoreByPackage($segment, $pkgOrHandle)
        {
            // Concrete\Core\Foundation\Environment::overrideCoreByPackage();
            Concrete\Core\Foundation\Environment::overrideCoreByPackage($segment, $pkgOrHandle);
        }

        public static function getRecord($segment, $pkgHandle = null)
        {
            // Concrete\Core\Foundation\Environment::getRecord();
            Concrete\Core\Foundation\Environment::getRecord($segment, $pkgHandle);
        }

        /**
         * Bypasses overrides cache to get record
         */
        public static function getUncachedRecord($segment, $pkgHandle = null)
        {
            // Concrete\Core\Foundation\Environment::getUncachedRecord();
            Concrete\Core\Foundation\Environment::getUncachedRecord($segment, $pkgHandle);
        }

        /**
         * Returns a full path to the subpath segment. Returns false if not found
         */
        public static function getPath($subpath, $pkgIdentifier = null)
        {
            // Concrete\Core\Foundation\Environment::getPath();
            Concrete\Core\Foundation\Environment::getPath($subpath, $pkgIdentifier);
        }

        /**
         * Returns  a public URL to the subpath item. Returns false if not found
         */
        public static function getURL($subpath, $pkgIdentifier = null)
        {
            // Concrete\Core\Foundation\Environment::getURL();
            Concrete\Core\Foundation\Environment::getURL($subpath, $pkgIdentifier);
        }

    }

    class Localization extends \Concrete\Core\Localization\Localization
    {

        public static function getInstance()
        {
            // Concrete\Core\Localization\Localization::getInstance();
            Concrete\Core\Localization\Localization::getInstance();
        }

        public static function changeLocale($locale)
        {
            // Concrete\Core\Localization\Localization::changeLocale();
            Concrete\Core\Localization\Localization::changeLocale($locale);
        }

        /** Returns the currently active locale
         * @return string
         * @example 'en_US'
         */
        public static function activeLocale()
        {
            // Concrete\Core\Localization\Localization::activeLocale();
            Concrete\Core\Localization\Localization::activeLocale();
        }

        /** Returns the language for the currently active locale
         * @return string
         * @example 'en'
         */
        public static function activeLanguage()
        {
            // Concrete\Core\Localization\Localization::activeLanguage();
            Concrete\Core\Localization\Localization::activeLanguage();
        }

        public static function __construct()
        {
            // Concrete\Core\Localization\Localization::__construct();
            Concrete\Core\Localization\Localization::__construct();
        }

        public static function setLocale($locale)
        {
            // Concrete\Core\Localization\Localization::setLocale();
            Concrete\Core\Localization\Localization::setLocale($locale);
        }

        public static function getLocale()
        {
            // Concrete\Core\Localization\Localization::getLocale();
            Concrete\Core\Localization\Localization::getLocale();
        }

        public static function getActiveTranslateObject()
        {
            // Concrete\Core\Localization\Localization::getActiveTranslateObject();
            Concrete\Core\Localization\Localization::getActiveTranslateObject();
        }

        public static function addSiteInterfaceLanguage($language)
        {
            // Concrete\Core\Localization\Localization::addSiteInterfaceLanguage();
            Concrete\Core\Localization\Localization::addSiteInterfaceLanguage($language);
        }

        public static function getTranslate()
        {
            // Concrete\Core\Localization\Localization::getTranslate();
            Concrete\Core\Localization\Localization::getTranslate();
        }

        public static function getAvailableInterfaceLanguages()
        {
            // Concrete\Core\Localization\Localization::getAvailableInterfaceLanguages();
            Concrete\Core\Localization\Localization::getAvailableInterfaceLanguages();
        }

        /**
         * Generates a list of all available languages and returns an array like
         * [ "de_DE" => "Deutsch (Deutschland)",
         *   "en_US" => "English (United States)",
         *   "fr_FR" => "Francais (France)"]
         * The result will be sorted by the key.
         * If the $displayLocale is set, the language- and region-names will be returned in that language
         * @param string $displayLocale Language of the description
         * @return Array An associative Array with locale as the key and description as content
         */
        public static function getAvailableInterfaceLanguageDescriptions($displayLocale = null)
        {
            // Concrete\Core\Localization\Localization::getAvailableInterfaceLanguageDescriptions();
            Concrete\Core\Localization\Localization::getAvailableInterfaceLanguageDescriptions($displayLocale);
        }

        /**
         * Get the description of a locale consisting of language and region description
         * e.g. "French (France)"
         * @param string $locale Locale that should be described
         * @param string $displayLocale Language of the description
         * @return string Description of a language
         */
        public static function getLanguageDescription($locale, $displayLocale = null)
        {
            // Concrete\Core\Localization\Localization::getLanguageDescription();
            Concrete\Core\Localization\Localization::getLanguageDescription($locale, $displayLocale);
        }

    }

    class Events extends \Concrete\Core\Support\Facade\Events
    {

        public static function getFacadeAccessor()
        {
            // Concrete\Core\Support\Facade\Events::getFacadeAccessor();
            Concrete\Core\Support\Facade\Events::getFacadeAccessor();
        }

        public static function fire($eventName, $event = null)
        {
            // Concrete\Core\Support\Facade\Events::fire();
            Concrete\Core\Support\Facade\Events::fire($eventName, $event);
        }

        /**
         * Get the root object behind the facade.
         *
         * @return mixed
         */
        public static function getFacadeRoot()
        {
            // Concrete\Core\Support\Facade\Facade::getFacadeRoot();
            Concrete\Core\Support\Facade\Facade::getFacadeRoot();
        }

        /**
         * Resolve the facade root instance from the container.
         *
         * @param  string  $name
         * @return mixed
         */
        protected static function resolveFacadeInstance($name)
        {
            // Concrete\Core\Support\Facade\Facade::resolveFacadeInstance();
            Concrete\Core\Support\Facade\Facade::resolveFacadeInstance($name);
        }

        /**
         * Clear a resolved facade instance.
         *
         * @param  string  $name
         * @return void
         */
        public static function clearResolvedInstance($name)
        {
            // Concrete\Core\Support\Facade\Facade::clearResolvedInstance();
            Concrete\Core\Support\Facade\Facade::clearResolvedInstance($name);
        }

        /**
         * Clear all of the resolved instances.
         *
         * @return void
         */
        public static function clearResolvedInstances()
        {
            // Concrete\Core\Support\Facade\Facade::clearResolvedInstances();
            Concrete\Core\Support\Facade\Facade::clearResolvedInstances();
        }

        /**
         * Get the application instance behind the facade.
         *
         * @return \Illuminate\Foundation\Application
         */
        public static function getFacadeApplication()
        {
            // Concrete\Core\Support\Facade\Facade::getFacadeApplication();
            Concrete\Core\Support\Facade\Facade::getFacadeApplication();
        }

        /**
         * Set the application instance.
         *
         * @param  \Illuminate\Foundation\Application  $app
         * @return void
         */
        public static function setFacadeApplication($app)
        {
            // Concrete\Core\Support\Facade\Facade::setFacadeApplication();
            Concrete\Core\Support\Facade\Facade::setFacadeApplication($app);
        }

        /**
         * Handle dynamic, static calls to the object.
         *
         * @param  string  $method
         * @param  array   $args
         * @return mixed
         */
        public static function __callStatic($method, $args)
        {
            // Concrete\Core\Support\Facade\Facade::__callStatic();
            Concrete\Core\Support\Facade\Facade::__callStatic($method, $args);
        }

    }

    class Response extends \Concrete\Core\Http\Response
    {

        public static function send()
        {
            // Concrete\Core\Http\Response::send();
            Concrete\Core\Http\Response::send();
        }

        /**
         * Constructor.
         *
         * @param string  $content The response content
         * @param integer $status  The response status code
         * @param array   $headers An array of response headers
         *
         * @throws \InvalidArgumentException When the HTTP status code is not valid
         *
         * @api
         */
        public static function __construct($content = null, $status = 200, $headers = null)
        {
            // Symfony\Component\HttpFoundation\Response::__construct();
            Symfony\Component\HttpFoundation\Response::__construct($content, $status, $headers);
        }

        /**
         * Factory method for chainability
         *
         * Example:
         *
         *     return Response::create($body, 200)
         *         ->setSharedMaxAge(300);
         *
         * @param string  $content The response content
         * @param integer $status  The response status code
         * @param array   $headers An array of response headers
         *
         * @return Response
         */
        public static function create($content = null, $status = 200, $headers = null)
        {
            // Symfony\Component\HttpFoundation\Response::create();
            Symfony\Component\HttpFoundation\Response::create($content, $status, $headers);
        }

        /**
         * Returns the Response as an HTTP string.
         *
         * The string representation of the Response is the same as the
         * one that will be sent to the client only if the prepare() method
         * has been called before.
         *
         * @return string The Response as an HTTP string
         *
         * @see prepare()
         */
        public static function __toString()
        {
            // Symfony\Component\HttpFoundation\Response::__toString();
            Symfony\Component\HttpFoundation\Response::__toString();
        }

        /**
         * Prepares the Response before it is sent to the client.
         *
         * This method tweaks the Response to ensure that it is
         * compliant with RFC 2616. Most of the changes are based on
         * the Request that is "associated" with this Response.
         *
         * @param Request $request A Request instance
         *
         * @return Response The current response.
         */
        public static function prepare(Symfony\Component\HttpFoundation\Request $request)
        {
            // Symfony\Component\HttpFoundation\Response::prepare();
            Symfony\Component\HttpFoundation\Response::prepare($request);
        }

        /**
         * Sends HTTP headers.
         *
         * @return Response
         */
        public static function sendHeaders()
        {
            // Symfony\Component\HttpFoundation\Response::sendHeaders();
            Symfony\Component\HttpFoundation\Response::sendHeaders();
        }

        /**
         * Sends content for the current web response.
         *
         * @return Response
         */
        public static function sendContent()
        {
            // Symfony\Component\HttpFoundation\Response::sendContent();
            Symfony\Component\HttpFoundation\Response::sendContent();
        }

        /**
         * Sets the response content.
         *
         * Valid types are strings, numbers, and objects that implement a __toString() method.
         *
         * @param mixed $content
         *
         * @return Response
         *
         * @api
         */
        public static function setContent($content)
        {
            // Symfony\Component\HttpFoundation\Response::setContent();
            Symfony\Component\HttpFoundation\Response::setContent($content);
        }

        /**
         * Gets the current response content.
         *
         * @return string Content
         *
         * @api
         */
        public static function getContent()
        {
            // Symfony\Component\HttpFoundation\Response::getContent();
            Symfony\Component\HttpFoundation\Response::getContent();
        }

        /**
         * Sets the HTTP protocol version (1.0 or 1.1).
         *
         * @param string $version The HTTP protocol version
         *
         * @return Response
         *
         * @api
         */
        public static function setProtocolVersion($version)
        {
            // Symfony\Component\HttpFoundation\Response::setProtocolVersion();
            Symfony\Component\HttpFoundation\Response::setProtocolVersion($version);
        }

        /**
         * Gets the HTTP protocol version.
         *
         * @return string The HTTP protocol version
         *
         * @api
         */
        public static function getProtocolVersion()
        {
            // Symfony\Component\HttpFoundation\Response::getProtocolVersion();
            Symfony\Component\HttpFoundation\Response::getProtocolVersion();
        }

        /**
         * Sets the response status code.
         *
         * @param integer $code HTTP status code
         * @param mixed   $text HTTP status text
         *
         * If the status text is null it will be automatically populated for the known
         * status codes and left empty otherwise.
         *
         * @return Response
         *
         * @throws \InvalidArgumentException When the HTTP status code is not valid
         *
         * @api
         */
        public static function setStatusCode($code, $text = null)
        {
            // Symfony\Component\HttpFoundation\Response::setStatusCode();
            Symfony\Component\HttpFoundation\Response::setStatusCode($code, $text);
        }

        /**
         * Retrieves the status code for the current web response.
         *
         * @return integer Status code
         *
         * @api
         */
        public static function getStatusCode()
        {
            // Symfony\Component\HttpFoundation\Response::getStatusCode();
            Symfony\Component\HttpFoundation\Response::getStatusCode();
        }

        /**
         * Sets the response charset.
         *
         * @param string $charset Character set
         *
         * @return Response
         *
         * @api
         */
        public static function setCharset($charset)
        {
            // Symfony\Component\HttpFoundation\Response::setCharset();
            Symfony\Component\HttpFoundation\Response::setCharset($charset);
        }

        /**
         * Retrieves the response charset.
         *
         * @return string Character set
         *
         * @api
         */
        public static function getCharset()
        {
            // Symfony\Component\HttpFoundation\Response::getCharset();
            Symfony\Component\HttpFoundation\Response::getCharset();
        }

        /**
         * Returns true if the response is worth caching under any circumstance.
         *
         * Responses marked "private" with an explicit Cache-Control directive are
         * considered uncacheable.
         *
         * Responses with neither a freshness lifetime (Expires, max-age) nor cache
         * validator (Last-Modified, ETag) are considered uncacheable.
         *
         * @return Boolean true if the response is worth caching, false otherwise
         *
         * @api
         */
        public static function isCacheable()
        {
            // Symfony\Component\HttpFoundation\Response::isCacheable();
            Symfony\Component\HttpFoundation\Response::isCacheable();
        }

        /**
         * Returns true if the response is "fresh".
         *
         * Fresh responses may be served from cache without any interaction with the
         * origin. A response is considered fresh when it includes a Cache-Control/max-age
         * indicator or Expires header and the calculated age is less than the freshness lifetime.
         *
         * @return Boolean true if the response is fresh, false otherwise
         *
         * @api
         */
        public static function isFresh()
        {
            // Symfony\Component\HttpFoundation\Response::isFresh();
            Symfony\Component\HttpFoundation\Response::isFresh();
        }

        /**
         * Returns true if the response includes headers that can be used to validate
         * the response with the origin server using a conditional GET request.
         *
         * @return Boolean true if the response is validateable, false otherwise
         *
         * @api
         */
        public static function isValidateable()
        {
            // Symfony\Component\HttpFoundation\Response::isValidateable();
            Symfony\Component\HttpFoundation\Response::isValidateable();
        }

        /**
         * Marks the response as "private".
         *
         * It makes the response ineligible for serving other clients.
         *
         * @return Response
         *
         * @api
         */
        public static function setPrivate()
        {
            // Symfony\Component\HttpFoundation\Response::setPrivate();
            Symfony\Component\HttpFoundation\Response::setPrivate();
        }

        /**
         * Marks the response as "public".
         *
         * It makes the response eligible for serving other clients.
         *
         * @return Response
         *
         * @api
         */
        public static function setPublic()
        {
            // Symfony\Component\HttpFoundation\Response::setPublic();
            Symfony\Component\HttpFoundation\Response::setPublic();
        }

        /**
         * Returns true if the response must be revalidated by caches.
         *
         * This method indicates that the response must not be served stale by a
         * cache in any circumstance without first revalidating with the origin.
         * When present, the TTL of the response should not be overridden to be
         * greater than the value provided by the origin.
         *
         * @return Boolean true if the response must be revalidated by a cache, false otherwise
         *
         * @api
         */
        public static function mustRevalidate()
        {
            // Symfony\Component\HttpFoundation\Response::mustRevalidate();
            Symfony\Component\HttpFoundation\Response::mustRevalidate();
        }

        /**
         * Returns the Date header as a DateTime instance.
         *
         * @return \DateTime A \DateTime instance
         *
         * @throws \RuntimeException When the header is not parseable
         *
         * @api
         */
        public static function getDate()
        {
            // Symfony\Component\HttpFoundation\Response::getDate();
            Symfony\Component\HttpFoundation\Response::getDate();
        }

        /**
         * Sets the Date header.
         *
         * @param \DateTime $date A \DateTime instance
         *
         * @return Response
         *
         * @api
         */
        public static function setDate(DateTime $date)
        {
            // Symfony\Component\HttpFoundation\Response::setDate();
            Symfony\Component\HttpFoundation\Response::setDate($date);
        }

        /**
         * Returns the age of the response.
         *
         * @return integer The age of the response in seconds
         */
        public static function getAge()
        {
            // Symfony\Component\HttpFoundation\Response::getAge();
            Symfony\Component\HttpFoundation\Response::getAge();
        }

        /**
         * Marks the response stale by setting the Age header to be equal to the maximum age of the response.
         *
         * @return Response
         *
         * @api
         */
        public static function expire()
        {
            // Symfony\Component\HttpFoundation\Response::expire();
            Symfony\Component\HttpFoundation\Response::expire();
        }

        /**
         * Returns the value of the Expires header as a DateTime instance.
         *
         * @return \DateTime|null A DateTime instance or null if the header does not exist
         *
         * @api
         */
        public static function getExpires()
        {
            // Symfony\Component\HttpFoundation\Response::getExpires();
            Symfony\Component\HttpFoundation\Response::getExpires();
        }

        /**
         * Sets the Expires HTTP header with a DateTime instance.
         *
         * Passing null as value will remove the header.
         *
         * @param \DateTime|null $date A \DateTime instance or null to remove the header
         *
         * @return Response
         *
         * @api
         */
        public static function setExpires(DateTime $date = null)
        {
            // Symfony\Component\HttpFoundation\Response::setExpires();
            Symfony\Component\HttpFoundation\Response::setExpires($date);
        }

        /**
         * Returns the number of seconds after the time specified in the response's Date
         * header when the response should no longer be considered fresh.
         *
         * First, it checks for a s-maxage directive, then a max-age directive, and then it falls
         * back on an expires header. It returns null when no maximum age can be established.
         *
         * @return integer|null Number of seconds
         *
         * @api
         */
        public static function getMaxAge()
        {
            // Symfony\Component\HttpFoundation\Response::getMaxAge();
            Symfony\Component\HttpFoundation\Response::getMaxAge();
        }

        /**
         * Sets the number of seconds after which the response should no longer be considered fresh.
         *
         * This methods sets the Cache-Control max-age directive.
         *
         * @param integer $value Number of seconds
         *
         * @return Response
         *
         * @api
         */
        public static function setMaxAge($value)
        {
            // Symfony\Component\HttpFoundation\Response::setMaxAge();
            Symfony\Component\HttpFoundation\Response::setMaxAge($value);
        }

        /**
         * Sets the number of seconds after which the response should no longer be considered fresh by shared caches.
         *
         * This methods sets the Cache-Control s-maxage directive.
         *
         * @param integer $value Number of seconds
         *
         * @return Response
         *
         * @api
         */
        public static function setSharedMaxAge($value)
        {
            // Symfony\Component\HttpFoundation\Response::setSharedMaxAge();
            Symfony\Component\HttpFoundation\Response::setSharedMaxAge($value);
        }

        /**
         * Returns the response's time-to-live in seconds.
         *
         * It returns null when no freshness information is present in the response.
         *
         * When the responses TTL is <= 0, the response may not be served from cache without first
         * revalidating with the origin.
         *
         * @return integer|null The TTL in seconds
         *
         * @api
         */
        public static function getTtl()
        {
            // Symfony\Component\HttpFoundation\Response::getTtl();
            Symfony\Component\HttpFoundation\Response::getTtl();
        }

        /**
         * Sets the response's time-to-live for shared caches.
         *
         * This method adjusts the Cache-Control/s-maxage directive.
         *
         * @param integer $seconds Number of seconds
         *
         * @return Response
         *
         * @api
         */
        public static function setTtl($seconds)
        {
            // Symfony\Component\HttpFoundation\Response::setTtl();
            Symfony\Component\HttpFoundation\Response::setTtl($seconds);
        }

        /**
         * Sets the response's time-to-live for private/client caches.
         *
         * This method adjusts the Cache-Control/max-age directive.
         *
         * @param integer $seconds Number of seconds
         *
         * @return Response
         *
         * @api
         */
        public static function setClientTtl($seconds)
        {
            // Symfony\Component\HttpFoundation\Response::setClientTtl();
            Symfony\Component\HttpFoundation\Response::setClientTtl($seconds);
        }

        /**
         * Returns the Last-Modified HTTP header as a DateTime instance.
         *
         * @return \DateTime|null A DateTime instance or null if the header does not exist
         *
         * @throws \RuntimeException When the HTTP header is not parseable
         *
         * @api
         */
        public static function getLastModified()
        {
            // Symfony\Component\HttpFoundation\Response::getLastModified();
            Symfony\Component\HttpFoundation\Response::getLastModified();
        }

        /**
         * Sets the Last-Modified HTTP header with a DateTime instance.
         *
         * Passing null as value will remove the header.
         *
         * @param \DateTime|null $date A \DateTime instance or null to remove the header
         *
         * @return Response
         *
         * @api
         */
        public static function setLastModified(DateTime $date = null)
        {
            // Symfony\Component\HttpFoundation\Response::setLastModified();
            Symfony\Component\HttpFoundation\Response::setLastModified($date);
        }

        /**
         * Returns the literal value of the ETag HTTP header.
         *
         * @return string|null The ETag HTTP header or null if it does not exist
         *
         * @api
         */
        public static function getEtag()
        {
            // Symfony\Component\HttpFoundation\Response::getEtag();
            Symfony\Component\HttpFoundation\Response::getEtag();
        }

        /**
         * Sets the ETag value.
         *
         * @param string|null $etag The ETag unique identifier or null to remove the header
         * @param Boolean     $weak Whether you want a weak ETag or not
         *
         * @return Response
         *
         * @api
         */
        public static function setEtag($etag = null, $weak = null)
        {
            // Symfony\Component\HttpFoundation\Response::setEtag();
            Symfony\Component\HttpFoundation\Response::setEtag($etag, $weak);
        }

        /**
         * Sets the response's cache headers (validation and/or expiration).
         *
         * Available options are: etag, last_modified, max_age, s_maxage, private, and public.
         *
         * @param array $options An array of cache options
         *
         * @return Response
         *
         * @api
         */
        public static function setCache(array $options)
        {
            // Symfony\Component\HttpFoundation\Response::setCache();
            Symfony\Component\HttpFoundation\Response::setCache($options);
        }

        /**
         * Modifies the response so that it conforms to the rules defined for a 304 status code.
         *
         * This sets the status, removes the body, and discards any headers
         * that MUST NOT be included in 304 responses.
         *
         * @return Response
         *
         * @see http://tools.ietf.org/html/rfc2616#section-10.3.5
         *
         * @api
         */
        public static function setNotModified()
        {
            // Symfony\Component\HttpFoundation\Response::setNotModified();
            Symfony\Component\HttpFoundation\Response::setNotModified();
        }

        /**
         * Returns true if the response includes a Vary header.
         *
         * @return Boolean true if the response includes a Vary header, false otherwise
         *
         * @api
         */
        public static function hasVary()
        {
            // Symfony\Component\HttpFoundation\Response::hasVary();
            Symfony\Component\HttpFoundation\Response::hasVary();
        }

        /**
         * Returns an array of header names given in the Vary header.
         *
         * @return array An array of Vary names
         *
         * @api
         */
        public static function getVary()
        {
            // Symfony\Component\HttpFoundation\Response::getVary();
            Symfony\Component\HttpFoundation\Response::getVary();
        }

        /**
         * Sets the Vary header.
         *
         * @param string|array $headers
         * @param Boolean      $replace Whether to replace the actual value of not (true by default)
         *
         * @return Response
         *
         * @api
         */
        public static function setVary($headers, $replace = "1")
        {
            // Symfony\Component\HttpFoundation\Response::setVary();
            Symfony\Component\HttpFoundation\Response::setVary($headers, $replace);
        }

        /**
         * Determines if the Response validators (ETag, Last-Modified) match
         * a conditional value specified in the Request.
         *
         * If the Response is not modified, it sets the status code to 304 and
         * removes the actual content by calling the setNotModified() method.
         *
         * @param Request $request A Request instance
         *
         * @return Boolean true if the Response validators match the Request, false otherwise
         *
         * @api
         */
        public static function isNotModified(Symfony\Component\HttpFoundation\Request $request)
        {
            // Symfony\Component\HttpFoundation\Response::isNotModified();
            Symfony\Component\HttpFoundation\Response::isNotModified($request);
        }

        /**
         * Is response invalid?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isInvalid()
        {
            // Symfony\Component\HttpFoundation\Response::isInvalid();
            Symfony\Component\HttpFoundation\Response::isInvalid();
        }

        /**
         * Is response informative?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isInformational()
        {
            // Symfony\Component\HttpFoundation\Response::isInformational();
            Symfony\Component\HttpFoundation\Response::isInformational();
        }

        /**
         * Is response successful?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isSuccessful()
        {
            // Symfony\Component\HttpFoundation\Response::isSuccessful();
            Symfony\Component\HttpFoundation\Response::isSuccessful();
        }

        /**
         * Is the response a redirect?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isRedirection()
        {
            // Symfony\Component\HttpFoundation\Response::isRedirection();
            Symfony\Component\HttpFoundation\Response::isRedirection();
        }

        /**
         * Is there a client error?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isClientError()
        {
            // Symfony\Component\HttpFoundation\Response::isClientError();
            Symfony\Component\HttpFoundation\Response::isClientError();
        }

        /**
         * Was there a server side error?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isServerError()
        {
            // Symfony\Component\HttpFoundation\Response::isServerError();
            Symfony\Component\HttpFoundation\Response::isServerError();
        }

        /**
         * Is the response OK?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isOk()
        {
            // Symfony\Component\HttpFoundation\Response::isOk();
            Symfony\Component\HttpFoundation\Response::isOk();
        }

        /**
         * Is the reponse forbidden?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isForbidden()
        {
            // Symfony\Component\HttpFoundation\Response::isForbidden();
            Symfony\Component\HttpFoundation\Response::isForbidden();
        }

        /**
         * Is the response a not found error?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isNotFound()
        {
            // Symfony\Component\HttpFoundation\Response::isNotFound();
            Symfony\Component\HttpFoundation\Response::isNotFound();
        }

        /**
         * Is the response a redirect of some form?
         *
         * @param string $location
         *
         * @return Boolean
         *
         * @api
         */
        public static function isRedirect($location = null)
        {
            // Symfony\Component\HttpFoundation\Response::isRedirect();
            Symfony\Component\HttpFoundation\Response::isRedirect($location);
        }

        /**
         * Is the response empty?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isEmpty()
        {
            // Symfony\Component\HttpFoundation\Response::isEmpty();
            Symfony\Component\HttpFoundation\Response::isEmpty();
        }

    }

    class Redirect extends \Concrete\Core\Routing\Redirect
    {

        /**
         * Actually sends a redirect
         */
        protected static function createRedirectResponse($url, $code, $headers)
        {
            // Concrete\Core\Routing\Redirect::createRedirectResponse();
            Concrete\Core\Routing\Redirect::createRedirectResponse($url, $code, $headers);
        }

        /**
         * Redirects to a concrete5 resource.
         */
        public static function to()
        {
            // Concrete\Core\Routing\Redirect::to();
            Concrete\Core\Routing\Redirect::to();
        }

        /**
         * Redirect to a page
         */
        public static function page(Concrete\Core\Page\Page $c, $code = 302, $headers = null)
        {
            // Concrete\Core\Routing\Redirect::page();
            Concrete\Core\Routing\Redirect::page($c, $code, $headers);
        }

        /**
         * Redirects to a URL.
         */
        public static function url($url, $code = 302, $headers = null)
        {
            // Concrete\Core\Routing\Redirect::url();
            Concrete\Core\Routing\Redirect::url($url, $code, $headers);
        }

    }

    class Log extends \Concrete\Core\Logging\Log
    {

        public static function __construct($log = null, $session = "1", $internal = null)
        {
            // Concrete\Core\Logging\Log::__construct();
            Concrete\Core\Logging\Log::__construct($log, $session, $internal);
        }

        public static function write($message)
        {
            // Concrete\Core\Logging\Log::write();
            Concrete\Core\Logging\Log::write($message);
        }

        public static function addEntry($message, $namespace = null)
        {
            // Concrete\Core\Logging\Log::addEntry();
            Concrete\Core\Logging\Log::addEntry($message, $namespace);
        }

        /**
         * Removes all "custom" log entries - these are entries that an app owner has written and don't have a builtin C5 type
         */
        public static function clearCustom()
        {
            // Concrete\Core\Logging\Log::clearCustom();
            Concrete\Core\Logging\Log::clearCustom();
        }

        /**
         * Removes log entries by type- these are entries that an app owner has written and don't have a builtin C5 type
         * @param string $type Is a lowercase string that uses underscores instead of spaces, e.g. sent_emails
         */
        public static function clearByType($type)
        {
            // Concrete\Core\Logging\Log::clearByType();
            Concrete\Core\Logging\Log::clearByType($type);
        }

        public static function clearInternal()
        {
            // Concrete\Core\Logging\Log::clearInternal();
            Concrete\Core\Logging\Log::clearInternal();
        }

        /**
         * Removes all log entries
         */
        public static function clearAll()
        {
            // Concrete\Core\Logging\Log::clearAll();
            Concrete\Core\Logging\Log::clearAll();
        }

        public static function close()
        {
            // Concrete\Core\Logging\Log::close();
            Concrete\Core\Logging\Log::close();
        }

        /**
         * Renames a log file and moves it to the log archive.
         */
        public static function archive()
        {
            // Concrete\Core\Logging\Log::archive();
            Concrete\Core\Logging\Log::archive();
        }

        /**
         * Returns the total number of entries matching this type
         */
        public static function getTotal($keywords, $type)
        {
            // Concrete\Core\Logging\Log::getTotal();
            Concrete\Core\Logging\Log::getTotal($keywords, $type);
        }

        /**
         * Returns a list of log entries
         */
        public static function getList($keywords, $type, $limit)
        {
            // Concrete\Core\Logging\Log::getList();
            Concrete\Core\Logging\Log::getList($keywords, $type, $limit);
        }

        /**
         * Returns an array of distinct log types
         */
        public static function getTypeList()
        {
            // Concrete\Core\Logging\Log::getTypeList();
            Concrete\Core\Logging\Log::getTypeList();
        }

        public static function getName()
        {
            // Concrete\Core\Logging\Log::getName();
            Concrete\Core\Logging\Log::getName();
        }

        /**
         * Returns all the log files in the directory
         */
        public static function getLogs()
        {
            // Concrete\Core\Logging\Log::getLogs();
            Concrete\Core\Logging\Log::getLogs();
        }

    }

    class URL extends \Concrete\Core\Routing\URL
    {

        public static function isValidURL($path)
        {
            // Concrete\Core\Routing\URL::isValidURL();
            Concrete\Core\Routing\URL::isValidURL($path);
        }

        public static function page(Concrete\Core\Page\Page $c, $action = null)
        {
            // Concrete\Core\Routing\URL::page();
            Concrete\Core\Routing\URL::page($c, $action);
        }

        public static function to($path, $action = null)
        {
            // Concrete\Core\Routing\URL::to();
            Concrete\Core\Routing\URL::to($path, $action);
        }

    }

    class Cookie extends \Concrete\Core\Cookie\Cookie
    {

        public static function getInstance()
        {
            // Concrete\Core\Cookie\Cookie::getInstance();
            Concrete\Core\Cookie\Cookie::getInstance();
        }

        public static function set($name, $value = null, $expire = null, $path = "/", $domain = null, $secure = null, $httpOnly = "1")
        {
            // Concrete\Core\Cookie\Cookie::set();
            Concrete\Core\Cookie\Cookie::set($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        }

        public static function add($cookie)
        {
            // Concrete\Core\Cookie\Cookie::add();
            Concrete\Core\Cookie\Cookie::add($cookie);
        }

        public static function get($name, $fullObject = null)
        {
            // Concrete\Core\Cookie\Cookie::get();
            Concrete\Core\Cookie\Cookie::get($name, $fullObject);
        }

        public static function getCookies()
        {
            // Concrete\Core\Cookie\Cookie::getCookies();
            Concrete\Core\Cookie\Cookie::getCookies();
        }

    }

    class Cache extends \Concrete\Core\Cache\Cache
    {

        public static function key($type, $id)
        {
            // Concrete\Core\Cache\Cache::key();
            Concrete\Core\Cache\Cache::key($type, $id);
        }

        public static function getLibrary()
        {
            // Concrete\Core\Cache\Cache::getLibrary();
            Concrete\Core\Cache\Cache::getLibrary();
        }

        public static function startup()
        {
            // Concrete\Core\Cache\Cache::startup();
            Concrete\Core\Cache\Cache::startup();
        }

        public static function disableCache()
        {
            // Concrete\Core\Cache\Cache::disableCache();
            Concrete\Core\Cache\Cache::disableCache();
        }

        public static function enableCache()
        {
            // Concrete\Core\Cache\Cache::enableCache();
            Concrete\Core\Cache\Cache::enableCache();
        }

        public static function disableLocalCache()
        {
            // Concrete\Core\Cache\Cache::disableLocalCache();
            Concrete\Core\Cache\Cache::disableLocalCache();
        }

        public static function enableLocalCache()
        {
            // Concrete\Core\Cache\Cache::enableLocalCache();
            Concrete\Core\Cache\Cache::enableLocalCache();
        }

        /**
         * Inserts or updates an item to the cache
         * the cache must always be enabled for (getting remote data, etc..)
         */
        public static function set($type, $id, $obj, $expire = null)
        {
            // Concrete\Core\Cache\Cache::set();
            Concrete\Core\Cache\Cache::set($type, $id, $obj, $expire);
        }

        /**
         * Retrieves an item from the cache
         */
        public static function get($type, $id, $mustBeNewerThan = null)
        {
            // Concrete\Core\Cache\Cache::get();
            Concrete\Core\Cache\Cache::get($type, $id, $mustBeNewerThan);
        }

        /**
         * Removes an item from the cache
         */
        public static function delete($type, $id)
        {
            // Concrete\Core\Cache\Cache::delete();
            Concrete\Core\Cache\Cache::delete($type, $id);
        }

        /**
         * Completely flushes the cache
         */
        public static function flush()
        {
            // Concrete\Core\Cache\Cache::flush();
            Concrete\Core\Cache\Cache::flush();
        }

    }

    class CacheLocal extends \Concrete\Core\Cache\CacheLocal
    {

        public static function getEntries()
        {
            // Concrete\Core\Cache\CacheLocal::getEntries();
            Concrete\Core\Cache\CacheLocal::getEntries();
        }

        public static function get()
        {
            // Concrete\Core\Cache\CacheLocal::get();
            Concrete\Core\Cache\CacheLocal::get();
        }

        public static function getEntry($type, $id)
        {
            // Concrete\Core\Cache\CacheLocal::getEntry();
            Concrete\Core\Cache\CacheLocal::getEntry($type, $id);
        }

        public static function flush()
        {
            // Concrete\Core\Cache\CacheLocal::flush();
            Concrete\Core\Cache\CacheLocal::flush();
        }

        public static function delete($type, $id)
        {
            // Concrete\Core\Cache\CacheLocal::delete();
            Concrete\Core\Cache\CacheLocal::delete($type, $id);
        }

        public static function set($type, $id, $object)
        {
            // Concrete\Core\Cache\CacheLocal::set();
            Concrete\Core\Cache\CacheLocal::set($type, $id, $object);
        }

    }

    class CollectionAttributeKey extends \Concrete\Core\Attribute\Key\CollectionKey
    {

        public static function getIndexedSearchTable()
        {
            // Concrete\Core\Attribute\Key\CollectionKey::getIndexedSearchTable();
            Concrete\Core\Attribute\Key\CollectionKey::getIndexedSearchTable();
        }

        /**
         * Returns an attribute value list of attributes and values (duh) which a collection version can store
         * against its object.
         * @return AttributeValueList
         */
        public static function getAttributes($cID, $cvID, $method = "getValue")
        {
            // Concrete\Core\Attribute\Key\CollectionKey::getAttributes();
            Concrete\Core\Attribute\Key\CollectionKey::getAttributes($cID, $cvID, $method);
        }

        public static function getColumnHeaderList()
        {
            // Concrete\Core\Attribute\Key\CollectionKey::getColumnHeaderList();
            Concrete\Core\Attribute\Key\CollectionKey::getColumnHeaderList();
        }

        public static function getSearchableIndexedList()
        {
            // Concrete\Core\Attribute\Key\CollectionKey::getSearchableIndexedList();
            Concrete\Core\Attribute\Key\CollectionKey::getSearchableIndexedList();
        }

        public static function getSearchableList()
        {
            // Concrete\Core\Attribute\Key\CollectionKey::getSearchableList();
            Concrete\Core\Attribute\Key\CollectionKey::getSearchableList();
        }

        public static function getAttributeValue($avID, $method = "getValue")
        {
            // Concrete\Core\Attribute\Key\CollectionKey::getAttributeValue();
            Concrete\Core\Attribute\Key\CollectionKey::getAttributeValue($avID, $method);
        }

        public static function getByID($akID)
        {
            // Concrete\Core\Attribute\Key\CollectionKey::getByID();
            Concrete\Core\Attribute\Key\CollectionKey::getByID($akID);
        }

        public static function getByHandle($akHandle)
        {
            // Concrete\Core\Attribute\Key\CollectionKey::getByHandle();
            Concrete\Core\Attribute\Key\CollectionKey::getByHandle($akHandle);
        }

        public static function getList()
        {
            // Concrete\Core\Attribute\Key\CollectionKey::getList();
            Concrete\Core\Attribute\Key\CollectionKey::getList();
        }

        /**
         * @access private
         */
        public static function get($akID)
        {
            // Concrete\Core\Attribute\Key\CollectionKey::get();
            Concrete\Core\Attribute\Key\CollectionKey::get($akID);
        }

        protected static function saveAttribute($nvc, $value = null)
        {
            // Concrete\Core\Attribute\Key\CollectionKey::saveAttribute();
            Concrete\Core\Attribute\Key\CollectionKey::saveAttribute($nvc, $value);
        }

        public static function add($at, $args, $pkg = null)
        {
            // Concrete\Core\Attribute\Key\CollectionKey::add();
            Concrete\Core\Attribute\Key\CollectionKey::add($at, $args, $pkg);
        }

        public static function delete()
        {
            // Concrete\Core\Attribute\Key\CollectionKey::delete();
            Concrete\Core\Attribute\Key\CollectionKey::delete();
        }

        public static function getSearchIndexFieldDefinition()
        {
            // Concrete\Core\Attribute\Key\Key::getSearchIndexFieldDefinition();
            Concrete\Core\Attribute\Key\Key::getSearchIndexFieldDefinition();
        }

        /**
         * Returns the name for this attribute key
         */
        public static function getAttributeKeyName()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyName();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyName();
        }

        /** Returns the display name for this attribute (localized and escaped accordingly to $format)
         * @param string $format = 'html'
         *	Escape the result in html format (if $format is 'html').
         *	If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getAttributeKeyDisplayName($format = "html")
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayName();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayName($format);
        }

        /**
         * Returns the handle for this attribute key
         */
        public static function getAttributeKeyHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyHandle();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyHandle();
        }

        /**
         * Deprecated. Going to be replaced by front end display name
         */
        public static function getAttributeKeyDisplayHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayHandle();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayHandle();
        }

        /**
         * Returns the ID for this attribute key
         */
        public static function getAttributeKeyID()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyID();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyID();
        }

        public static function getAttributeKeyCategoryID()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyCategoryID();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyCategoryID();
        }

        /**
         * Returns whether the attribute key is searchable
         */
        public static function isAttributeKeySearchable()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeySearchable();
            Concrete\Core\Attribute\Key\Key::isAttributeKeySearchable();
        }

        /**
         * Returns whether the attribute key is internal
         */
        public static function isAttributeKeyInternal()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyInternal();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyInternal();
        }

        /**
         * Returns whether the attribute key is indexed as a "keyword search" field.
         */
        public static function isAttributeKeyContentIndexed()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyContentIndexed();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyContentIndexed();
        }

        /**
         * Returns whether the attribute key is one that was automatically created by a process.
         */
        public static function isAttributeKeyAutoCreated()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyAutoCreated();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyAutoCreated();
        }

        /**
         * Returns whether the attribute key is included in the standard search for this category.
         */
        public static function isAttributeKeyColumnHeader()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyColumnHeader();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyColumnHeader();
        }

        /**
         * Returns whether the attribute key is one that can be edited through the frontend.
         */
        public static function isAttributeKeyEditable()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyEditable();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyEditable();
        }

        /**
         * Loads the required attribute fields for this instantiated attribute
         */
        protected static function load($akIdentifier, $loadBy = "akID")
        {
            // Concrete\Core\Attribute\Key\Key::load();
            Concrete\Core\Attribute\Key\Key::load($akIdentifier, $loadBy);
        }

        public static function getPackageID()
        {
            // Concrete\Core\Attribute\Key\Key::getPackageID();
            Concrete\Core\Attribute\Key\Key::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getPackageHandle();
            Concrete\Core\Attribute\Key\Key::getPackageHandle();
        }

        public static function getInstanceByID($akID)
        {
            // Concrete\Core\Attribute\Key\Key::getInstanceByID();
            Concrete\Core\Attribute\Key\Key::getInstanceByID($akID);
        }

        /**
         * Returns an attribute type object
         */
        public static function getAttributeType()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeType();
            Concrete\Core\Attribute\Key\Key::getAttributeType();
        }

        /**
         * Returns the attribute type handle
         */
        public static function getAttributeTypeHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeTypeHandle();
            Concrete\Core\Attribute\Key\Key::getAttributeTypeHandle();
        }

        public static function getAttributeKeyType()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyType();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyType();
        }

        public static function export($axml, $exporttype = "full")
        {
            // Concrete\Core\Attribute\Key\Key::export();
            Concrete\Core\Attribute\Key\Key::export($axml, $exporttype);
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Attribute\Key\Key::exportList();
            Concrete\Core\Attribute\Key\Key::exportList($xml);
        }

        /**
         * Note, this queries both the pkgID found on the AttributeKeys table AND any attribute keys of a special type
         * installed by that package, and any in categories by that package.
         * That's because a special type, if the package is uninstalled, is going to be unusable
         * by attribute keys that still remain.
         */
        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Attribute\Key\Key::getListByPackage();
            Concrete\Core\Attribute\Key\Key::getListByPackage($pkg);
        }

        public static function import(SimpleXMLElement $ak)
        {
            // Concrete\Core\Attribute\Key\Key::import();
            Concrete\Core\Attribute\Key\Key::import($ak);
        }

        public static function refreshCache()
        {
            // Concrete\Core\Attribute\Key\Key::refreshCache();
            Concrete\Core\Attribute\Key\Key::refreshCache();
        }

        /**
         * Updates an attribute key.
         */
        public static function update($args)
        {
            // Concrete\Core\Attribute\Key\Key::update();
            Concrete\Core\Attribute\Key\Key::update($args);
        }

        /**
         * Duplicates an attribute key
         */
        public static function duplicate($args = null)
        {
            // Concrete\Core\Attribute\Key\Key::duplicate();
            Concrete\Core\Attribute\Key\Key::duplicate($args);
        }

        public static function inAttributeSet($as)
        {
            // Concrete\Core\Attribute\Key\Key::inAttributeSet();
            Concrete\Core\Attribute\Key\Key::inAttributeSet($as);
        }

        public static function setAttributeKeyColumnHeader($r)
        {
            // Concrete\Core\Attribute\Key\Key::setAttributeKeyColumnHeader();
            Concrete\Core\Attribute\Key\Key::setAttributeKeyColumnHeader($r);
        }

        public static function reindex($tbl, $columnHeaders, $attribs, $rs)
        {
            // Concrete\Core\Attribute\Key\Key::reindex();
            Concrete\Core\Attribute\Key\Key::reindex($tbl, $columnHeaders, $attribs, $rs);
        }

        public static function updateSearchIndex($prevHandle = null)
        {
            // Concrete\Core\Attribute\Key\Key::updateSearchIndex();
            Concrete\Core\Attribute\Key\Key::updateSearchIndex($prevHandle);
        }

        public static function getAttributeValueIDList()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeValueIDList();
            Concrete\Core\Attribute\Key\Key::getAttributeValueIDList();
        }

        /**
         * Adds a generic attribute record (with this type) to the AttributeValues table
         */
        public static function addAttributeValue()
        {
            // Concrete\Core\Attribute\Key\Key::addAttributeValue();
            Concrete\Core\Attribute\Key\Key::addAttributeValue();
        }

        public static function getAttributeKeyIconSRC()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyIconSRC();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyIconSRC();
        }

        public static function getController()
        {
            // Concrete\Core\Attribute\Key\Key::getController();
            Concrete\Core\Attribute\Key\Key::getController();
        }

        /**
         * Renders a view for this attribute key. If no view is default we display it's "view"
         * Valid views are "view", "form" or a custom view (if the attribute has one in its directory)
         * Additionally, an attribute does not have to have its own interface. If it doesn't, then whatever
         * is printed in the corresponding $view function in the attribute's controller is printed out.
         */
        public static function render($view = "view", $value = null, $return = null)
        {
            // Concrete\Core\Attribute\Key\Key::render();
            Concrete\Core\Attribute\Key\Key::render($view, $value, $return);
        }

        public static function __destruct()
        {
            // Concrete\Core\Attribute\Key\Key::__destruct();
            Concrete\Core\Attribute\Key\Key::__destruct();
        }

        public static function validateAttributeForm($h = null)
        {
            // Concrete\Core\Attribute\Key\Key::validateAttributeForm();
            Concrete\Core\Attribute\Key\Key::validateAttributeForm($h);
        }

        public static function createIndexedSearchTable()
        {
            // Concrete\Core\Attribute\Key\Key::createIndexedSearchTable();
            Concrete\Core\Attribute\Key\Key::createIndexedSearchTable();
        }

        public static function setAttributeSet($as)
        {
            // Concrete\Core\Attribute\Key\Key::setAttributeSet();
            Concrete\Core\Attribute\Key\Key::setAttributeSet($as);
        }

        public static function clearAttributeSets()
        {
            // Concrete\Core\Attribute\Key\Key::clearAttributeSets();
            Concrete\Core\Attribute\Key\Key::clearAttributeSets();
        }

        public static function getAttributeSets()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeSets();
            Concrete\Core\Attribute\Key\Key::getAttributeSets();
        }

        /**
         * Saves an attribute using its stock form.
         */
        public static function saveAttributeForm($obj)
        {
            // Concrete\Core\Attribute\Key\Key::saveAttributeForm();
            Concrete\Core\Attribute\Key\Key::saveAttributeForm($obj);
        }

        /**
         * Sets an attribute directly with a passed value.
         */
        public static function setAttribute($obj, $value)
        {
            // Concrete\Core\Attribute\Key\Key::setAttribute();
            Concrete\Core\Attribute\Key\Key::setAttribute($obj, $value);
        }

        /**
         * @deprecated */
        public static function outputSearchHTML()
        {
            // Concrete\Core\Attribute\Key\Key::outputSearchHTML();
            Concrete\Core\Attribute\Key\Key::outputSearchHTML();
        }

        public static function getKeyName()
        {
            // Concrete\Core\Attribute\Key\Key::getKeyName();
            Concrete\Core\Attribute\Key\Key::getKeyName();
        }

        /**
         * Returns the handle for this attribute key
         */
        public static function getKeyHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getKeyHandle();
            Concrete\Core\Attribute\Key\Key::getKeyHandle();
        }

        /**
         * Returns the ID for this attribute key
         */
        public static function getKeyID()
        {
            // Concrete\Core\Attribute\Key\Key::getKeyID();
            Concrete\Core\Attribute\Key\Key::getKeyID();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class FileAttributeKey extends \Concrete\Core\Attribute\Key\FileKey
    {

        public static function getIndexedSearchTable()
        {
            // Concrete\Core\Attribute\Key\FileKey::getIndexedSearchTable();
            Concrete\Core\Attribute\Key\FileKey::getIndexedSearchTable();
        }

        /**
         * Returns an attribute value list of attributes and values (duh) which a collection version can store
         * against its object.
         * @return AttributeValueList
         */
        public static function getAttributes($fID, $fvID, $method = "getValue")
        {
            // Concrete\Core\Attribute\Key\FileKey::getAttributes();
            Concrete\Core\Attribute\Key\FileKey::getAttributes($fID, $fvID, $method);
        }

        public static function getAttributeValue($avID, $method = "getValue")
        {
            // Concrete\Core\Attribute\Key\FileKey::getAttributeValue();
            Concrete\Core\Attribute\Key\FileKey::getAttributeValue($avID, $method);
        }

        public static function getByHandle($akHandle)
        {
            // Concrete\Core\Attribute\Key\FileKey::getByHandle();
            Concrete\Core\Attribute\Key\FileKey::getByHandle($akHandle);
        }

        public static function getByID($akID)
        {
            // Concrete\Core\Attribute\Key\FileKey::getByID();
            Concrete\Core\Attribute\Key\FileKey::getByID($akID);
        }

        public static function getList()
        {
            // Concrete\Core\Attribute\Key\FileKey::getList();
            Concrete\Core\Attribute\Key\FileKey::getList();
        }

        public static function getSearchableList()
        {
            // Concrete\Core\Attribute\Key\FileKey::getSearchableList();
            Concrete\Core\Attribute\Key\FileKey::getSearchableList();
        }

        public static function getSearchableIndexedList()
        {
            // Concrete\Core\Attribute\Key\FileKey::getSearchableIndexedList();
            Concrete\Core\Attribute\Key\FileKey::getSearchableIndexedList();
        }

        public static function getImporterList($fv = null)
        {
            // Concrete\Core\Attribute\Key\FileKey::getImporterList();
            Concrete\Core\Attribute\Key\FileKey::getImporterList($fv);
        }

        public static function getUserAddedList()
        {
            // Concrete\Core\Attribute\Key\FileKey::getUserAddedList();
            Concrete\Core\Attribute\Key\FileKey::getUserAddedList();
        }

        /**
         * @access private
         */
        public static function get($akID)
        {
            // Concrete\Core\Attribute\Key\FileKey::get();
            Concrete\Core\Attribute\Key\FileKey::get($akID);
        }

        protected static function saveAttribute($f, $value = null)
        {
            // Concrete\Core\Attribute\Key\FileKey::saveAttribute();
            Concrete\Core\Attribute\Key\FileKey::saveAttribute($f, $value);
        }

        public static function add($at, $args, $pkg = null)
        {
            // Concrete\Core\Attribute\Key\FileKey::add();
            Concrete\Core\Attribute\Key\FileKey::add($at, $args, $pkg);
        }

        public static function getColumnHeaderList()
        {
            // Concrete\Core\Attribute\Key\FileKey::getColumnHeaderList();
            Concrete\Core\Attribute\Key\FileKey::getColumnHeaderList();
        }

        public static function delete()
        {
            // Concrete\Core\Attribute\Key\FileKey::delete();
            Concrete\Core\Attribute\Key\FileKey::delete();
        }

        public static function getSearchIndexFieldDefinition()
        {
            // Concrete\Core\Attribute\Key\Key::getSearchIndexFieldDefinition();
            Concrete\Core\Attribute\Key\Key::getSearchIndexFieldDefinition();
        }

        /**
         * Returns the name for this attribute key
         */
        public static function getAttributeKeyName()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyName();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyName();
        }

        /** Returns the display name for this attribute (localized and escaped accordingly to $format)
         * @param string $format = 'html'
         *	Escape the result in html format (if $format is 'html').
         *	If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getAttributeKeyDisplayName($format = "html")
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayName();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayName($format);
        }

        /**
         * Returns the handle for this attribute key
         */
        public static function getAttributeKeyHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyHandle();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyHandle();
        }

        /**
         * Deprecated. Going to be replaced by front end display name
         */
        public static function getAttributeKeyDisplayHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayHandle();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayHandle();
        }

        /**
         * Returns the ID for this attribute key
         */
        public static function getAttributeKeyID()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyID();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyID();
        }

        public static function getAttributeKeyCategoryID()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyCategoryID();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyCategoryID();
        }

        /**
         * Returns whether the attribute key is searchable
         */
        public static function isAttributeKeySearchable()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeySearchable();
            Concrete\Core\Attribute\Key\Key::isAttributeKeySearchable();
        }

        /**
         * Returns whether the attribute key is internal
         */
        public static function isAttributeKeyInternal()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyInternal();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyInternal();
        }

        /**
         * Returns whether the attribute key is indexed as a "keyword search" field.
         */
        public static function isAttributeKeyContentIndexed()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyContentIndexed();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyContentIndexed();
        }

        /**
         * Returns whether the attribute key is one that was automatically created by a process.
         */
        public static function isAttributeKeyAutoCreated()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyAutoCreated();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyAutoCreated();
        }

        /**
         * Returns whether the attribute key is included in the standard search for this category.
         */
        public static function isAttributeKeyColumnHeader()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyColumnHeader();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyColumnHeader();
        }

        /**
         * Returns whether the attribute key is one that can be edited through the frontend.
         */
        public static function isAttributeKeyEditable()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyEditable();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyEditable();
        }

        /**
         * Loads the required attribute fields for this instantiated attribute
         */
        protected static function load($akIdentifier, $loadBy = "akID")
        {
            // Concrete\Core\Attribute\Key\Key::load();
            Concrete\Core\Attribute\Key\Key::load($akIdentifier, $loadBy);
        }

        public static function getPackageID()
        {
            // Concrete\Core\Attribute\Key\Key::getPackageID();
            Concrete\Core\Attribute\Key\Key::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getPackageHandle();
            Concrete\Core\Attribute\Key\Key::getPackageHandle();
        }

        public static function getInstanceByID($akID)
        {
            // Concrete\Core\Attribute\Key\Key::getInstanceByID();
            Concrete\Core\Attribute\Key\Key::getInstanceByID($akID);
        }

        /**
         * Returns an attribute type object
         */
        public static function getAttributeType()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeType();
            Concrete\Core\Attribute\Key\Key::getAttributeType();
        }

        /**
         * Returns the attribute type handle
         */
        public static function getAttributeTypeHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeTypeHandle();
            Concrete\Core\Attribute\Key\Key::getAttributeTypeHandle();
        }

        public static function getAttributeKeyType()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyType();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyType();
        }

        public static function export($axml, $exporttype = "full")
        {
            // Concrete\Core\Attribute\Key\Key::export();
            Concrete\Core\Attribute\Key\Key::export($axml, $exporttype);
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Attribute\Key\Key::exportList();
            Concrete\Core\Attribute\Key\Key::exportList($xml);
        }

        /**
         * Note, this queries both the pkgID found on the AttributeKeys table AND any attribute keys of a special type
         * installed by that package, and any in categories by that package.
         * That's because a special type, if the package is uninstalled, is going to be unusable
         * by attribute keys that still remain.
         */
        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Attribute\Key\Key::getListByPackage();
            Concrete\Core\Attribute\Key\Key::getListByPackage($pkg);
        }

        public static function import(SimpleXMLElement $ak)
        {
            // Concrete\Core\Attribute\Key\Key::import();
            Concrete\Core\Attribute\Key\Key::import($ak);
        }

        public static function refreshCache()
        {
            // Concrete\Core\Attribute\Key\Key::refreshCache();
            Concrete\Core\Attribute\Key\Key::refreshCache();
        }

        /**
         * Updates an attribute key.
         */
        public static function update($args)
        {
            // Concrete\Core\Attribute\Key\Key::update();
            Concrete\Core\Attribute\Key\Key::update($args);
        }

        /**
         * Duplicates an attribute key
         */
        public static function duplicate($args = null)
        {
            // Concrete\Core\Attribute\Key\Key::duplicate();
            Concrete\Core\Attribute\Key\Key::duplicate($args);
        }

        public static function inAttributeSet($as)
        {
            // Concrete\Core\Attribute\Key\Key::inAttributeSet();
            Concrete\Core\Attribute\Key\Key::inAttributeSet($as);
        }

        public static function setAttributeKeyColumnHeader($r)
        {
            // Concrete\Core\Attribute\Key\Key::setAttributeKeyColumnHeader();
            Concrete\Core\Attribute\Key\Key::setAttributeKeyColumnHeader($r);
        }

        public static function reindex($tbl, $columnHeaders, $attribs, $rs)
        {
            // Concrete\Core\Attribute\Key\Key::reindex();
            Concrete\Core\Attribute\Key\Key::reindex($tbl, $columnHeaders, $attribs, $rs);
        }

        public static function updateSearchIndex($prevHandle = null)
        {
            // Concrete\Core\Attribute\Key\Key::updateSearchIndex();
            Concrete\Core\Attribute\Key\Key::updateSearchIndex($prevHandle);
        }

        public static function getAttributeValueIDList()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeValueIDList();
            Concrete\Core\Attribute\Key\Key::getAttributeValueIDList();
        }

        /**
         * Adds a generic attribute record (with this type) to the AttributeValues table
         */
        public static function addAttributeValue()
        {
            // Concrete\Core\Attribute\Key\Key::addAttributeValue();
            Concrete\Core\Attribute\Key\Key::addAttributeValue();
        }

        public static function getAttributeKeyIconSRC()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyIconSRC();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyIconSRC();
        }

        public static function getController()
        {
            // Concrete\Core\Attribute\Key\Key::getController();
            Concrete\Core\Attribute\Key\Key::getController();
        }

        /**
         * Renders a view for this attribute key. If no view is default we display it's "view"
         * Valid views are "view", "form" or a custom view (if the attribute has one in its directory)
         * Additionally, an attribute does not have to have its own interface. If it doesn't, then whatever
         * is printed in the corresponding $view function in the attribute's controller is printed out.
         */
        public static function render($view = "view", $value = null, $return = null)
        {
            // Concrete\Core\Attribute\Key\Key::render();
            Concrete\Core\Attribute\Key\Key::render($view, $value, $return);
        }

        public static function __destruct()
        {
            // Concrete\Core\Attribute\Key\Key::__destruct();
            Concrete\Core\Attribute\Key\Key::__destruct();
        }

        public static function validateAttributeForm($h = null)
        {
            // Concrete\Core\Attribute\Key\Key::validateAttributeForm();
            Concrete\Core\Attribute\Key\Key::validateAttributeForm($h);
        }

        public static function createIndexedSearchTable()
        {
            // Concrete\Core\Attribute\Key\Key::createIndexedSearchTable();
            Concrete\Core\Attribute\Key\Key::createIndexedSearchTable();
        }

        public static function setAttributeSet($as)
        {
            // Concrete\Core\Attribute\Key\Key::setAttributeSet();
            Concrete\Core\Attribute\Key\Key::setAttributeSet($as);
        }

        public static function clearAttributeSets()
        {
            // Concrete\Core\Attribute\Key\Key::clearAttributeSets();
            Concrete\Core\Attribute\Key\Key::clearAttributeSets();
        }

        public static function getAttributeSets()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeSets();
            Concrete\Core\Attribute\Key\Key::getAttributeSets();
        }

        /**
         * Saves an attribute using its stock form.
         */
        public static function saveAttributeForm($obj)
        {
            // Concrete\Core\Attribute\Key\Key::saveAttributeForm();
            Concrete\Core\Attribute\Key\Key::saveAttributeForm($obj);
        }

        /**
         * Sets an attribute directly with a passed value.
         */
        public static function setAttribute($obj, $value)
        {
            // Concrete\Core\Attribute\Key\Key::setAttribute();
            Concrete\Core\Attribute\Key\Key::setAttribute($obj, $value);
        }

        /**
         * @deprecated */
        public static function outputSearchHTML()
        {
            // Concrete\Core\Attribute\Key\Key::outputSearchHTML();
            Concrete\Core\Attribute\Key\Key::outputSearchHTML();
        }

        public static function getKeyName()
        {
            // Concrete\Core\Attribute\Key\Key::getKeyName();
            Concrete\Core\Attribute\Key\Key::getKeyName();
        }

        /**
         * Returns the handle for this attribute key
         */
        public static function getKeyHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getKeyHandle();
            Concrete\Core\Attribute\Key\Key::getKeyHandle();
        }

        /**
         * Returns the ID for this attribute key
         */
        public static function getKeyID()
        {
            // Concrete\Core\Attribute\Key\Key::getKeyID();
            Concrete\Core\Attribute\Key\Key::getKeyID();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class UserAttributeKey extends \Concrete\Core\Attribute\Key\UserKey
    {

        public static function getIndexedSearchTable()
        {
            // Concrete\Core\Attribute\Key\UserKey::getIndexedSearchTable();
            Concrete\Core\Attribute\Key\UserKey::getIndexedSearchTable();
        }

        public static function getAttributes($uID, $method = "getValue")
        {
            // Concrete\Core\Attribute\Key\UserKey::getAttributes();
            Concrete\Core\Attribute\Key\UserKey::getAttributes($uID, $method);
        }

        public static function getAttributeKeyDisplayOrder()
        {
            // Concrete\Core\Attribute\Key\UserKey::getAttributeKeyDisplayOrder();
            Concrete\Core\Attribute\Key\UserKey::getAttributeKeyDisplayOrder();
        }

        public static function load($akID)
        {
            // Concrete\Core\Attribute\Key\UserKey::load();
            Concrete\Core\Attribute\Key\UserKey::load($akID);
        }

        public static function getAttributeValue($avID, $method = "getValue")
        {
            // Concrete\Core\Attribute\Key\UserKey::getAttributeValue();
            Concrete\Core\Attribute\Key\UserKey::getAttributeValue($avID, $method);
        }

        public static function getByID($akID)
        {
            // Concrete\Core\Attribute\Key\UserKey::getByID();
            Concrete\Core\Attribute\Key\UserKey::getByID($akID);
        }

        public static function getByHandle($akHandle)
        {
            // Concrete\Core\Attribute\Key\UserKey::getByHandle();
            Concrete\Core\Attribute\Key\UserKey::getByHandle($akHandle);
        }

        public static function export($axml)
        {
            // Concrete\Core\Attribute\Key\UserKey::export();
            Concrete\Core\Attribute\Key\UserKey::export($axml);
        }

        public static function import(SimpleXMLElement $ak)
        {
            // Concrete\Core\Attribute\Key\UserKey::import();
            Concrete\Core\Attribute\Key\UserKey::import($ak);
        }

        public static function isAttributeKeyDisplayedOnProfile()
        {
            // Concrete\Core\Attribute\Key\UserKey::isAttributeKeyDisplayedOnProfile();
            Concrete\Core\Attribute\Key\UserKey::isAttributeKeyDisplayedOnProfile();
        }

        public static function isAttributeKeyEditableOnProfile()
        {
            // Concrete\Core\Attribute\Key\UserKey::isAttributeKeyEditableOnProfile();
            Concrete\Core\Attribute\Key\UserKey::isAttributeKeyEditableOnProfile();
        }

        public static function isAttributeKeyRequiredOnProfile()
        {
            // Concrete\Core\Attribute\Key\UserKey::isAttributeKeyRequiredOnProfile();
            Concrete\Core\Attribute\Key\UserKey::isAttributeKeyRequiredOnProfile();
        }

        public static function isAttributeKeyEditableOnRegister()
        {
            // Concrete\Core\Attribute\Key\UserKey::isAttributeKeyEditableOnRegister();
            Concrete\Core\Attribute\Key\UserKey::isAttributeKeyEditableOnRegister();
        }

        public static function isAttributeKeyRequiredOnRegister()
        {
            // Concrete\Core\Attribute\Key\UserKey::isAttributeKeyRequiredOnRegister();
            Concrete\Core\Attribute\Key\UserKey::isAttributeKeyRequiredOnRegister();
        }

        public static function isAttributeKeyDisplayedOnMemberList()
        {
            // Concrete\Core\Attribute\Key\UserKey::isAttributeKeyDisplayedOnMemberList();
            Concrete\Core\Attribute\Key\UserKey::isAttributeKeyDisplayedOnMemberList();
        }

        public static function isAttributeKeyActive()
        {
            // Concrete\Core\Attribute\Key\UserKey::isAttributeKeyActive();
            Concrete\Core\Attribute\Key\UserKey::isAttributeKeyActive();
        }

        public static function activate()
        {
            // Concrete\Core\Attribute\Key\UserKey::activate();
            Concrete\Core\Attribute\Key\UserKey::activate();
        }

        public static function deactivate()
        {
            // Concrete\Core\Attribute\Key\UserKey::deactivate();
            Concrete\Core\Attribute\Key\UserKey::deactivate();
        }

        public static function getList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getList();
            Concrete\Core\Attribute\Key\UserKey::getList();
        }

        /**
         * @access private
         */
        public static function get($akID)
        {
            // Concrete\Core\Attribute\Key\UserKey::get();
            Concrete\Core\Attribute\Key\UserKey::get($akID);
        }

        protected static function saveAttribute($uo, $value = null)
        {
            // Concrete\Core\Attribute\Key\UserKey::saveAttribute();
            Concrete\Core\Attribute\Key\UserKey::saveAttribute($uo, $value);
        }

        public static function add($type, $args, $pkg = null)
        {
            // Concrete\Core\Attribute\Key\UserKey::add();
            Concrete\Core\Attribute\Key\UserKey::add($type, $args, $pkg);
        }

        public static function update($args)
        {
            // Concrete\Core\Attribute\Key\UserKey::update();
            Concrete\Core\Attribute\Key\UserKey::update($args);
        }

        public static function delete()
        {
            // Concrete\Core\Attribute\Key\UserKey::delete();
            Concrete\Core\Attribute\Key\UserKey::delete();
        }

        public static function getColumnHeaderList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getColumnHeaderList();
            Concrete\Core\Attribute\Key\UserKey::getColumnHeaderList();
        }

        public static function getEditableList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getEditableList();
            Concrete\Core\Attribute\Key\UserKey::getEditableList();
        }

        public static function getSearchableList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getSearchableList();
            Concrete\Core\Attribute\Key\UserKey::getSearchableList();
        }

        public static function getSearchableIndexedList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getSearchableIndexedList();
            Concrete\Core\Attribute\Key\UserKey::getSearchableIndexedList();
        }

        public static function getImporterList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getImporterList();
            Concrete\Core\Attribute\Key\UserKey::getImporterList();
        }

        public static function getPublicProfileList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getPublicProfileList();
            Concrete\Core\Attribute\Key\UserKey::getPublicProfileList();
        }

        public static function getRegistrationList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getRegistrationList();
            Concrete\Core\Attribute\Key\UserKey::getRegistrationList();
        }

        public static function getMemberListList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getMemberListList();
            Concrete\Core\Attribute\Key\UserKey::getMemberListList();
        }

        public static function getEditableInProfileList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getEditableInProfileList();
            Concrete\Core\Attribute\Key\UserKey::getEditableInProfileList();
        }

        public static function getUserAddedList()
        {
            // Concrete\Core\Attribute\Key\UserKey::getUserAddedList();
            Concrete\Core\Attribute\Key\UserKey::getUserAddedList();
        }

        public static function updateAttributesDisplayOrder($uats)
        {
            // Concrete\Core\Attribute\Key\UserKey::updateAttributesDisplayOrder();
            Concrete\Core\Attribute\Key\UserKey::updateAttributesDisplayOrder($uats);
        }

        public static function getSearchIndexFieldDefinition()
        {
            // Concrete\Core\Attribute\Key\Key::getSearchIndexFieldDefinition();
            Concrete\Core\Attribute\Key\Key::getSearchIndexFieldDefinition();
        }

        /**
         * Returns the name for this attribute key
         */
        public static function getAttributeKeyName()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyName();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyName();
        }

        /** Returns the display name for this attribute (localized and escaped accordingly to $format)
         * @param string $format = 'html'
         *	Escape the result in html format (if $format is 'html').
         *	If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getAttributeKeyDisplayName($format = "html")
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayName();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayName($format);
        }

        /**
         * Returns the handle for this attribute key
         */
        public static function getAttributeKeyHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyHandle();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyHandle();
        }

        /**
         * Deprecated. Going to be replaced by front end display name
         */
        public static function getAttributeKeyDisplayHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayHandle();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyDisplayHandle();
        }

        /**
         * Returns the ID for this attribute key
         */
        public static function getAttributeKeyID()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyID();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyID();
        }

        public static function getAttributeKeyCategoryID()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyCategoryID();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyCategoryID();
        }

        /**
         * Returns whether the attribute key is searchable
         */
        public static function isAttributeKeySearchable()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeySearchable();
            Concrete\Core\Attribute\Key\Key::isAttributeKeySearchable();
        }

        /**
         * Returns whether the attribute key is internal
         */
        public static function isAttributeKeyInternal()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyInternal();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyInternal();
        }

        /**
         * Returns whether the attribute key is indexed as a "keyword search" field.
         */
        public static function isAttributeKeyContentIndexed()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyContentIndexed();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyContentIndexed();
        }

        /**
         * Returns whether the attribute key is one that was automatically created by a process.
         */
        public static function isAttributeKeyAutoCreated()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyAutoCreated();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyAutoCreated();
        }

        /**
         * Returns whether the attribute key is included in the standard search for this category.
         */
        public static function isAttributeKeyColumnHeader()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyColumnHeader();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyColumnHeader();
        }

        /**
         * Returns whether the attribute key is one that can be edited through the frontend.
         */
        public static function isAttributeKeyEditable()
        {
            // Concrete\Core\Attribute\Key\Key::isAttributeKeyEditable();
            Concrete\Core\Attribute\Key\Key::isAttributeKeyEditable();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Attribute\Key\Key::getPackageID();
            Concrete\Core\Attribute\Key\Key::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getPackageHandle();
            Concrete\Core\Attribute\Key\Key::getPackageHandle();
        }

        public static function getInstanceByID($akID)
        {
            // Concrete\Core\Attribute\Key\Key::getInstanceByID();
            Concrete\Core\Attribute\Key\Key::getInstanceByID($akID);
        }

        /**
         * Returns an attribute type object
         */
        public static function getAttributeType()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeType();
            Concrete\Core\Attribute\Key\Key::getAttributeType();
        }

        /**
         * Returns the attribute type handle
         */
        public static function getAttributeTypeHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeTypeHandle();
            Concrete\Core\Attribute\Key\Key::getAttributeTypeHandle();
        }

        public static function getAttributeKeyType()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyType();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyType();
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Attribute\Key\Key::exportList();
            Concrete\Core\Attribute\Key\Key::exportList($xml);
        }

        /**
         * Note, this queries both the pkgID found on the AttributeKeys table AND any attribute keys of a special type
         * installed by that package, and any in categories by that package.
         * That's because a special type, if the package is uninstalled, is going to be unusable
         * by attribute keys that still remain.
         */
        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Attribute\Key\Key::getListByPackage();
            Concrete\Core\Attribute\Key\Key::getListByPackage($pkg);
        }

        public static function refreshCache()
        {
            // Concrete\Core\Attribute\Key\Key::refreshCache();
            Concrete\Core\Attribute\Key\Key::refreshCache();
        }

        /**
         * Duplicates an attribute key
         */
        public static function duplicate($args = null)
        {
            // Concrete\Core\Attribute\Key\Key::duplicate();
            Concrete\Core\Attribute\Key\Key::duplicate($args);
        }

        public static function inAttributeSet($as)
        {
            // Concrete\Core\Attribute\Key\Key::inAttributeSet();
            Concrete\Core\Attribute\Key\Key::inAttributeSet($as);
        }

        public static function setAttributeKeyColumnHeader($r)
        {
            // Concrete\Core\Attribute\Key\Key::setAttributeKeyColumnHeader();
            Concrete\Core\Attribute\Key\Key::setAttributeKeyColumnHeader($r);
        }

        public static function reindex($tbl, $columnHeaders, $attribs, $rs)
        {
            // Concrete\Core\Attribute\Key\Key::reindex();
            Concrete\Core\Attribute\Key\Key::reindex($tbl, $columnHeaders, $attribs, $rs);
        }

        public static function updateSearchIndex($prevHandle = null)
        {
            // Concrete\Core\Attribute\Key\Key::updateSearchIndex();
            Concrete\Core\Attribute\Key\Key::updateSearchIndex($prevHandle);
        }

        public static function getAttributeValueIDList()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeValueIDList();
            Concrete\Core\Attribute\Key\Key::getAttributeValueIDList();
        }

        /**
         * Adds a generic attribute record (with this type) to the AttributeValues table
         */
        public static function addAttributeValue()
        {
            // Concrete\Core\Attribute\Key\Key::addAttributeValue();
            Concrete\Core\Attribute\Key\Key::addAttributeValue();
        }

        public static function getAttributeKeyIconSRC()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeKeyIconSRC();
            Concrete\Core\Attribute\Key\Key::getAttributeKeyIconSRC();
        }

        public static function getController()
        {
            // Concrete\Core\Attribute\Key\Key::getController();
            Concrete\Core\Attribute\Key\Key::getController();
        }

        /**
         * Renders a view for this attribute key. If no view is default we display it's "view"
         * Valid views are "view", "form" or a custom view (if the attribute has one in its directory)
         * Additionally, an attribute does not have to have its own interface. If it doesn't, then whatever
         * is printed in the corresponding $view function in the attribute's controller is printed out.
         */
        public static function render($view = "view", $value = null, $return = null)
        {
            // Concrete\Core\Attribute\Key\Key::render();
            Concrete\Core\Attribute\Key\Key::render($view, $value, $return);
        }

        public static function __destruct()
        {
            // Concrete\Core\Attribute\Key\Key::__destruct();
            Concrete\Core\Attribute\Key\Key::__destruct();
        }

        public static function validateAttributeForm($h = null)
        {
            // Concrete\Core\Attribute\Key\Key::validateAttributeForm();
            Concrete\Core\Attribute\Key\Key::validateAttributeForm($h);
        }

        public static function createIndexedSearchTable()
        {
            // Concrete\Core\Attribute\Key\Key::createIndexedSearchTable();
            Concrete\Core\Attribute\Key\Key::createIndexedSearchTable();
        }

        public static function setAttributeSet($as)
        {
            // Concrete\Core\Attribute\Key\Key::setAttributeSet();
            Concrete\Core\Attribute\Key\Key::setAttributeSet($as);
        }

        public static function clearAttributeSets()
        {
            // Concrete\Core\Attribute\Key\Key::clearAttributeSets();
            Concrete\Core\Attribute\Key\Key::clearAttributeSets();
        }

        public static function getAttributeSets()
        {
            // Concrete\Core\Attribute\Key\Key::getAttributeSets();
            Concrete\Core\Attribute\Key\Key::getAttributeSets();
        }

        /**
         * Saves an attribute using its stock form.
         */
        public static function saveAttributeForm($obj)
        {
            // Concrete\Core\Attribute\Key\Key::saveAttributeForm();
            Concrete\Core\Attribute\Key\Key::saveAttributeForm($obj);
        }

        /**
         * Sets an attribute directly with a passed value.
         */
        public static function setAttribute($obj, $value)
        {
            // Concrete\Core\Attribute\Key\Key::setAttribute();
            Concrete\Core\Attribute\Key\Key::setAttribute($obj, $value);
        }

        /**
         * @deprecated */
        public static function outputSearchHTML()
        {
            // Concrete\Core\Attribute\Key\Key::outputSearchHTML();
            Concrete\Core\Attribute\Key\Key::outputSearchHTML();
        }

        public static function getKeyName()
        {
            // Concrete\Core\Attribute\Key\Key::getKeyName();
            Concrete\Core\Attribute\Key\Key::getKeyName();
        }

        /**
         * Returns the handle for this attribute key
         */
        public static function getKeyHandle()
        {
            // Concrete\Core\Attribute\Key\Key::getKeyHandle();
            Concrete\Core\Attribute\Key\Key::getKeyHandle();
        }

        /**
         * Returns the ID for this attribute key
         */
        public static function getKeyID()
        {
            // Concrete\Core\Attribute\Key\Key::getKeyID();
            Concrete\Core\Attribute\Key\Key::getKeyID();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class AttributeSet extends \Concrete\Core\Attribute\Set
    {

        public static function getByID($asID)
        {
            // Concrete\Core\Attribute\Set::getByID();
            Concrete\Core\Attribute\Set::getByID($asID);
        }

        public static function getByHandle($asHandle)
        {
            // Concrete\Core\Attribute\Set::getByHandle();
            Concrete\Core\Attribute\Set::getByHandle($asHandle);
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Attribute\Set::getListByPackage();
            Concrete\Core\Attribute\Set::getListByPackage($pkg);
        }

        public static function getAttributeSetID()
        {
            // Concrete\Core\Attribute\Set::getAttributeSetID();
            Concrete\Core\Attribute\Set::getAttributeSetID();
        }

        public static function getAttributeSetHandle()
        {
            // Concrete\Core\Attribute\Set::getAttributeSetHandle();
            Concrete\Core\Attribute\Set::getAttributeSetHandle();
        }

        public static function getAttributeSetName()
        {
            // Concrete\Core\Attribute\Set::getAttributeSetName();
            Concrete\Core\Attribute\Set::getAttributeSetName();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Attribute\Set::getPackageID();
            Concrete\Core\Attribute\Set::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Attribute\Set::getPackageHandle();
            Concrete\Core\Attribute\Set::getPackageHandle();
        }

        public static function getAttributeSetKeyCategoryID()
        {
            // Concrete\Core\Attribute\Set::getAttributeSetKeyCategoryID();
            Concrete\Core\Attribute\Set::getAttributeSetKeyCategoryID();
        }

        public static function isAttributeSetLocked()
        {
            // Concrete\Core\Attribute\Set::isAttributeSetLocked();
            Concrete\Core\Attribute\Set::isAttributeSetLocked();
        }

        /** Returns the display name for this attribute set (localized and escaped accordingly to $format)
         * @param string $format = 'html'
         *	Escape the result in html format (if $format is 'html').
         *	If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getAttributeSetDisplayName($format = "html")
        {
            // Concrete\Core\Attribute\Set::getAttributeSetDisplayName();
            Concrete\Core\Attribute\Set::getAttributeSetDisplayName($format);
        }

        public static function updateAttributeSetName($asName)
        {
            // Concrete\Core\Attribute\Set::updateAttributeSetName();
            Concrete\Core\Attribute\Set::updateAttributeSetName($asName);
        }

        public static function updateAttributeSetHandle($asHandle)
        {
            // Concrete\Core\Attribute\Set::updateAttributeSetHandle();
            Concrete\Core\Attribute\Set::updateAttributeSetHandle($asHandle);
        }

        public static function addKey($ak)
        {
            // Concrete\Core\Attribute\Set::addKey();
            Concrete\Core\Attribute\Set::addKey($ak);
        }

        public static function clearAttributeKeys()
        {
            // Concrete\Core\Attribute\Set::clearAttributeKeys();
            Concrete\Core\Attribute\Set::clearAttributeKeys();
        }

        public static function export($axml)
        {
            // Concrete\Core\Attribute\Set::export();
            Concrete\Core\Attribute\Set::export($axml);
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Attribute\Set::exportList();
            Concrete\Core\Attribute\Set::exportList($xml);
        }

        public static function getAttributeKeys()
        {
            // Concrete\Core\Attribute\Set::getAttributeKeys();
            Concrete\Core\Attribute\Set::getAttributeKeys();
        }

        public static function contains($ak)
        {
            // Concrete\Core\Attribute\Set::contains();
            Concrete\Core\Attribute\Set::contains($ak);
        }

        /**
         * Removes an attribute set and sets all keys within to have a set ID of 0.
         */
        public static function delete()
        {
            // Concrete\Core\Attribute\Set::delete();
            Concrete\Core\Attribute\Set::delete();
        }

        public static function deleteKey($ak)
        {
            // Concrete\Core\Attribute\Set::deleteKey();
            Concrete\Core\Attribute\Set::deleteKey($ak);
        }

        protected static function rescanDisplayOrder()
        {
            // Concrete\Core\Attribute\Set::rescanDisplayOrder();
            Concrete\Core\Attribute\Set::rescanDisplayOrder();
        }

        public static function updateAttributesDisplayOrder($uats)
        {
            // Concrete\Core\Attribute\Set::updateAttributesDisplayOrder();
            Concrete\Core\Attribute\Set::updateAttributesDisplayOrder($uats);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class AssetList extends \Concrete\Core\Asset\AssetList
    {

        public static function getRegisteredAssets()
        {
            // Concrete\Core\Asset\AssetList::getRegisteredAssets();
            Concrete\Core\Asset\AssetList::getRegisteredAssets();
        }

        public static function getInstance()
        {
            // Concrete\Core\Asset\AssetList::getInstance();
            Concrete\Core\Asset\AssetList::getInstance();
        }

        public static function register($assetType, $assetHandle, $filename, $args = null, $pkg = null)
        {
            // Concrete\Core\Asset\AssetList::register();
            Concrete\Core\Asset\AssetList::register($assetType, $assetHandle, $filename, $args, $pkg);
        }

        public static function registerAsset(Concrete\Core\Asset\Asset $asset)
        {
            // Concrete\Core\Asset\AssetList::registerAsset();
            Concrete\Core\Asset\AssetList::registerAsset($asset);
        }

        public static function registerGroup($assetGroupHandle, $assetHandles, $customClass = null)
        {
            // Concrete\Core\Asset\AssetList::registerGroup();
            Concrete\Core\Asset\AssetList::registerGroup($assetGroupHandle, $assetHandles, $customClass);
        }

        public static function getAsset($assetType, $assetHandle)
        {
            // Concrete\Core\Asset\AssetList::getAsset();
            Concrete\Core\Asset\AssetList::getAsset($assetType, $assetHandle);
        }

        public static function getAssetGroup($assetGroupHandle)
        {
            // Concrete\Core\Asset\AssetList::getAssetGroup();
            Concrete\Core\Asset\AssetList::getAssetGroup($assetGroupHandle);
        }

    }

    class Router extends \Concrete\Core\Routing\Router
    {

        public static function __construct()
        {
            // Concrete\Core\Routing\Router::__construct();
            Concrete\Core\Routing\Router::__construct();
        }

        public static function getList()
        {
            // Concrete\Core\Routing\Router::getList();
            Concrete\Core\Routing\Router::getList();
        }

        public static function setRequest(Concrete\Core\Http\Request $req)
        {
            // Concrete\Core\Routing\Router::setRequest();
            Concrete\Core\Routing\Router::setRequest($req);
        }

        public static function register($rtPath, $callback, $rtHandle = null, $additionalAttributes = null)
        {
            // Concrete\Core\Routing\Router::register();
            Concrete\Core\Routing\Router::register($rtPath, $callback, $rtHandle, $additionalAttributes);
        }

        public static function execute(Concrete\Core\Routing\Route $route, $parameters)
        {
            // Concrete\Core\Routing\Router::execute();
            Concrete\Core\Routing\Router::execute($route, $parameters);
        }

        /**
         * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes
         * @access public
         * @param $path string
         * @param $theme object, if null site theme is default
         * @return void
         */
        public static function setThemeByRoute($path, $theme = null, $wrapper = "view.php")
        {
            // Concrete\Core\Routing\Router::setThemeByRoute();
            Concrete\Core\Routing\Router::setThemeByRoute($path, $theme, $wrapper);
        }

        /**
         * This grabs the theme for a particular path, if one exists in the themePaths array
         * @param string $path
         * @return string|boolean
         */
        public static function getThemeByRoute($path)
        {
            // Concrete\Core\Routing\Router::getThemeByRoute();
            Concrete\Core\Routing\Router::getThemeByRoute($path);
        }

    }

    class RedirectResponse extends \Concrete\Core\Routing\RedirectResponse
    {

        public static function setRequest(Concrete\Core\Http\Request $r)
        {
            // Concrete\Core\Routing\RedirectResponse::setRequest();
            Concrete\Core\Routing\RedirectResponse::setRequest($r);
        }

        /**
         * Creates a redirect response so that it conforms to the rules defined for a redirect status code.
         *
         * @param string  $url     The URL to redirect to
         * @param integer $status  The status code (302 by default)
         * @param array   $headers The headers (Location is always set to the given url)
         *
         * @see http://tools.ietf.org/html/rfc2616#section-10.3
         *
         * @api
         */
        public static function __construct($url, $status = 302, $headers = null)
        {
            // Symfony\Component\HttpFoundation\RedirectResponse::__construct();
            Symfony\Component\HttpFoundation\RedirectResponse::__construct($url, $status, $headers);
        }

        /**
         * {@inheritDoc}
         */
        public static function create($url = null, $status = 302, $headers = null)
        {
            // Symfony\Component\HttpFoundation\RedirectResponse::create();
            Symfony\Component\HttpFoundation\RedirectResponse::create($url, $status, $headers);
        }

        /**
         * Returns the target URL.
         *
         * @return string target URL
         */
        public static function getTargetUrl()
        {
            // Symfony\Component\HttpFoundation\RedirectResponse::getTargetUrl();
            Symfony\Component\HttpFoundation\RedirectResponse::getTargetUrl();
        }

        /**
         * Sets the redirect target of this response.
         *
         * @param string  $url     The URL to redirect to
         *
         * @return RedirectResponse The current response.
         */
        public static function setTargetUrl($url)
        {
            // Symfony\Component\HttpFoundation\RedirectResponse::setTargetUrl();
            Symfony\Component\HttpFoundation\RedirectResponse::setTargetUrl($url);
        }

        /**
         * Returns the Response as an HTTP string.
         *
         * The string representation of the Response is the same as the
         * one that will be sent to the client only if the prepare() method
         * has been called before.
         *
         * @return string The Response as an HTTP string
         *
         * @see prepare()
         */
        public static function __toString()
        {
            // Symfony\Component\HttpFoundation\Response::__toString();
            Symfony\Component\HttpFoundation\Response::__toString();
        }

        /**
         * Prepares the Response before it is sent to the client.
         *
         * This method tweaks the Response to ensure that it is
         * compliant with RFC 2616. Most of the changes are based on
         * the Request that is "associated" with this Response.
         *
         * @param Request $request A Request instance
         *
         * @return Response The current response.
         */
        public static function prepare(Symfony\Component\HttpFoundation\Request $request)
        {
            // Symfony\Component\HttpFoundation\Response::prepare();
            Symfony\Component\HttpFoundation\Response::prepare($request);
        }

        /**
         * Sends HTTP headers.
         *
         * @return Response
         */
        public static function sendHeaders()
        {
            // Symfony\Component\HttpFoundation\Response::sendHeaders();
            Symfony\Component\HttpFoundation\Response::sendHeaders();
        }

        /**
         * Sends content for the current web response.
         *
         * @return Response
         */
        public static function sendContent()
        {
            // Symfony\Component\HttpFoundation\Response::sendContent();
            Symfony\Component\HttpFoundation\Response::sendContent();
        }

        /**
         * Sends HTTP headers and content.
         *
         * @return Response
         *
         * @api
         */
        public static function send()
        {
            // Symfony\Component\HttpFoundation\Response::send();
            Symfony\Component\HttpFoundation\Response::send();
        }

        /**
         * Sets the response content.
         *
         * Valid types are strings, numbers, and objects that implement a __toString() method.
         *
         * @param mixed $content
         *
         * @return Response
         *
         * @api
         */
        public static function setContent($content)
        {
            // Symfony\Component\HttpFoundation\Response::setContent();
            Symfony\Component\HttpFoundation\Response::setContent($content);
        }

        /**
         * Gets the current response content.
         *
         * @return string Content
         *
         * @api
         */
        public static function getContent()
        {
            // Symfony\Component\HttpFoundation\Response::getContent();
            Symfony\Component\HttpFoundation\Response::getContent();
        }

        /**
         * Sets the HTTP protocol version (1.0 or 1.1).
         *
         * @param string $version The HTTP protocol version
         *
         * @return Response
         *
         * @api
         */
        public static function setProtocolVersion($version)
        {
            // Symfony\Component\HttpFoundation\Response::setProtocolVersion();
            Symfony\Component\HttpFoundation\Response::setProtocolVersion($version);
        }

        /**
         * Gets the HTTP protocol version.
         *
         * @return string The HTTP protocol version
         *
         * @api
         */
        public static function getProtocolVersion()
        {
            // Symfony\Component\HttpFoundation\Response::getProtocolVersion();
            Symfony\Component\HttpFoundation\Response::getProtocolVersion();
        }

        /**
         * Sets the response status code.
         *
         * @param integer $code HTTP status code
         * @param mixed   $text HTTP status text
         *
         * If the status text is null it will be automatically populated for the known
         * status codes and left empty otherwise.
         *
         * @return Response
         *
         * @throws \InvalidArgumentException When the HTTP status code is not valid
         *
         * @api
         */
        public static function setStatusCode($code, $text = null)
        {
            // Symfony\Component\HttpFoundation\Response::setStatusCode();
            Symfony\Component\HttpFoundation\Response::setStatusCode($code, $text);
        }

        /**
         * Retrieves the status code for the current web response.
         *
         * @return integer Status code
         *
         * @api
         */
        public static function getStatusCode()
        {
            // Symfony\Component\HttpFoundation\Response::getStatusCode();
            Symfony\Component\HttpFoundation\Response::getStatusCode();
        }

        /**
         * Sets the response charset.
         *
         * @param string $charset Character set
         *
         * @return Response
         *
         * @api
         */
        public static function setCharset($charset)
        {
            // Symfony\Component\HttpFoundation\Response::setCharset();
            Symfony\Component\HttpFoundation\Response::setCharset($charset);
        }

        /**
         * Retrieves the response charset.
         *
         * @return string Character set
         *
         * @api
         */
        public static function getCharset()
        {
            // Symfony\Component\HttpFoundation\Response::getCharset();
            Symfony\Component\HttpFoundation\Response::getCharset();
        }

        /**
         * Returns true if the response is worth caching under any circumstance.
         *
         * Responses marked "private" with an explicit Cache-Control directive are
         * considered uncacheable.
         *
         * Responses with neither a freshness lifetime (Expires, max-age) nor cache
         * validator (Last-Modified, ETag) are considered uncacheable.
         *
         * @return Boolean true if the response is worth caching, false otherwise
         *
         * @api
         */
        public static function isCacheable()
        {
            // Symfony\Component\HttpFoundation\Response::isCacheable();
            Symfony\Component\HttpFoundation\Response::isCacheable();
        }

        /**
         * Returns true if the response is "fresh".
         *
         * Fresh responses may be served from cache without any interaction with the
         * origin. A response is considered fresh when it includes a Cache-Control/max-age
         * indicator or Expires header and the calculated age is less than the freshness lifetime.
         *
         * @return Boolean true if the response is fresh, false otherwise
         *
         * @api
         */
        public static function isFresh()
        {
            // Symfony\Component\HttpFoundation\Response::isFresh();
            Symfony\Component\HttpFoundation\Response::isFresh();
        }

        /**
         * Returns true if the response includes headers that can be used to validate
         * the response with the origin server using a conditional GET request.
         *
         * @return Boolean true if the response is validateable, false otherwise
         *
         * @api
         */
        public static function isValidateable()
        {
            // Symfony\Component\HttpFoundation\Response::isValidateable();
            Symfony\Component\HttpFoundation\Response::isValidateable();
        }

        /**
         * Marks the response as "private".
         *
         * It makes the response ineligible for serving other clients.
         *
         * @return Response
         *
         * @api
         */
        public static function setPrivate()
        {
            // Symfony\Component\HttpFoundation\Response::setPrivate();
            Symfony\Component\HttpFoundation\Response::setPrivate();
        }

        /**
         * Marks the response as "public".
         *
         * It makes the response eligible for serving other clients.
         *
         * @return Response
         *
         * @api
         */
        public static function setPublic()
        {
            // Symfony\Component\HttpFoundation\Response::setPublic();
            Symfony\Component\HttpFoundation\Response::setPublic();
        }

        /**
         * Returns true if the response must be revalidated by caches.
         *
         * This method indicates that the response must not be served stale by a
         * cache in any circumstance without first revalidating with the origin.
         * When present, the TTL of the response should not be overridden to be
         * greater than the value provided by the origin.
         *
         * @return Boolean true if the response must be revalidated by a cache, false otherwise
         *
         * @api
         */
        public static function mustRevalidate()
        {
            // Symfony\Component\HttpFoundation\Response::mustRevalidate();
            Symfony\Component\HttpFoundation\Response::mustRevalidate();
        }

        /**
         * Returns the Date header as a DateTime instance.
         *
         * @return \DateTime A \DateTime instance
         *
         * @throws \RuntimeException When the header is not parseable
         *
         * @api
         */
        public static function getDate()
        {
            // Symfony\Component\HttpFoundation\Response::getDate();
            Symfony\Component\HttpFoundation\Response::getDate();
        }

        /**
         * Sets the Date header.
         *
         * @param \DateTime $date A \DateTime instance
         *
         * @return Response
         *
         * @api
         */
        public static function setDate(DateTime $date)
        {
            // Symfony\Component\HttpFoundation\Response::setDate();
            Symfony\Component\HttpFoundation\Response::setDate($date);
        }

        /**
         * Returns the age of the response.
         *
         * @return integer The age of the response in seconds
         */
        public static function getAge()
        {
            // Symfony\Component\HttpFoundation\Response::getAge();
            Symfony\Component\HttpFoundation\Response::getAge();
        }

        /**
         * Marks the response stale by setting the Age header to be equal to the maximum age of the response.
         *
         * @return Response
         *
         * @api
         */
        public static function expire()
        {
            // Symfony\Component\HttpFoundation\Response::expire();
            Symfony\Component\HttpFoundation\Response::expire();
        }

        /**
         * Returns the value of the Expires header as a DateTime instance.
         *
         * @return \DateTime|null A DateTime instance or null if the header does not exist
         *
         * @api
         */
        public static function getExpires()
        {
            // Symfony\Component\HttpFoundation\Response::getExpires();
            Symfony\Component\HttpFoundation\Response::getExpires();
        }

        /**
         * Sets the Expires HTTP header with a DateTime instance.
         *
         * Passing null as value will remove the header.
         *
         * @param \DateTime|null $date A \DateTime instance or null to remove the header
         *
         * @return Response
         *
         * @api
         */
        public static function setExpires(DateTime $date = null)
        {
            // Symfony\Component\HttpFoundation\Response::setExpires();
            Symfony\Component\HttpFoundation\Response::setExpires($date);
        }

        /**
         * Returns the number of seconds after the time specified in the response's Date
         * header when the response should no longer be considered fresh.
         *
         * First, it checks for a s-maxage directive, then a max-age directive, and then it falls
         * back on an expires header. It returns null when no maximum age can be established.
         *
         * @return integer|null Number of seconds
         *
         * @api
         */
        public static function getMaxAge()
        {
            // Symfony\Component\HttpFoundation\Response::getMaxAge();
            Symfony\Component\HttpFoundation\Response::getMaxAge();
        }

        /**
         * Sets the number of seconds after which the response should no longer be considered fresh.
         *
         * This methods sets the Cache-Control max-age directive.
         *
         * @param integer $value Number of seconds
         *
         * @return Response
         *
         * @api
         */
        public static function setMaxAge($value)
        {
            // Symfony\Component\HttpFoundation\Response::setMaxAge();
            Symfony\Component\HttpFoundation\Response::setMaxAge($value);
        }

        /**
         * Sets the number of seconds after which the response should no longer be considered fresh by shared caches.
         *
         * This methods sets the Cache-Control s-maxage directive.
         *
         * @param integer $value Number of seconds
         *
         * @return Response
         *
         * @api
         */
        public static function setSharedMaxAge($value)
        {
            // Symfony\Component\HttpFoundation\Response::setSharedMaxAge();
            Symfony\Component\HttpFoundation\Response::setSharedMaxAge($value);
        }

        /**
         * Returns the response's time-to-live in seconds.
         *
         * It returns null when no freshness information is present in the response.
         *
         * When the responses TTL is <= 0, the response may not be served from cache without first
         * revalidating with the origin.
         *
         * @return integer|null The TTL in seconds
         *
         * @api
         */
        public static function getTtl()
        {
            // Symfony\Component\HttpFoundation\Response::getTtl();
            Symfony\Component\HttpFoundation\Response::getTtl();
        }

        /**
         * Sets the response's time-to-live for shared caches.
         *
         * This method adjusts the Cache-Control/s-maxage directive.
         *
         * @param integer $seconds Number of seconds
         *
         * @return Response
         *
         * @api
         */
        public static function setTtl($seconds)
        {
            // Symfony\Component\HttpFoundation\Response::setTtl();
            Symfony\Component\HttpFoundation\Response::setTtl($seconds);
        }

        /**
         * Sets the response's time-to-live for private/client caches.
         *
         * This method adjusts the Cache-Control/max-age directive.
         *
         * @param integer $seconds Number of seconds
         *
         * @return Response
         *
         * @api
         */
        public static function setClientTtl($seconds)
        {
            // Symfony\Component\HttpFoundation\Response::setClientTtl();
            Symfony\Component\HttpFoundation\Response::setClientTtl($seconds);
        }

        /**
         * Returns the Last-Modified HTTP header as a DateTime instance.
         *
         * @return \DateTime|null A DateTime instance or null if the header does not exist
         *
         * @throws \RuntimeException When the HTTP header is not parseable
         *
         * @api
         */
        public static function getLastModified()
        {
            // Symfony\Component\HttpFoundation\Response::getLastModified();
            Symfony\Component\HttpFoundation\Response::getLastModified();
        }

        /**
         * Sets the Last-Modified HTTP header with a DateTime instance.
         *
         * Passing null as value will remove the header.
         *
         * @param \DateTime|null $date A \DateTime instance or null to remove the header
         *
         * @return Response
         *
         * @api
         */
        public static function setLastModified(DateTime $date = null)
        {
            // Symfony\Component\HttpFoundation\Response::setLastModified();
            Symfony\Component\HttpFoundation\Response::setLastModified($date);
        }

        /**
         * Returns the literal value of the ETag HTTP header.
         *
         * @return string|null The ETag HTTP header or null if it does not exist
         *
         * @api
         */
        public static function getEtag()
        {
            // Symfony\Component\HttpFoundation\Response::getEtag();
            Symfony\Component\HttpFoundation\Response::getEtag();
        }

        /**
         * Sets the ETag value.
         *
         * @param string|null $etag The ETag unique identifier or null to remove the header
         * @param Boolean     $weak Whether you want a weak ETag or not
         *
         * @return Response
         *
         * @api
         */
        public static function setEtag($etag = null, $weak = null)
        {
            // Symfony\Component\HttpFoundation\Response::setEtag();
            Symfony\Component\HttpFoundation\Response::setEtag($etag, $weak);
        }

        /**
         * Sets the response's cache headers (validation and/or expiration).
         *
         * Available options are: etag, last_modified, max_age, s_maxage, private, and public.
         *
         * @param array $options An array of cache options
         *
         * @return Response
         *
         * @api
         */
        public static function setCache(array $options)
        {
            // Symfony\Component\HttpFoundation\Response::setCache();
            Symfony\Component\HttpFoundation\Response::setCache($options);
        }

        /**
         * Modifies the response so that it conforms to the rules defined for a 304 status code.
         *
         * This sets the status, removes the body, and discards any headers
         * that MUST NOT be included in 304 responses.
         *
         * @return Response
         *
         * @see http://tools.ietf.org/html/rfc2616#section-10.3.5
         *
         * @api
         */
        public static function setNotModified()
        {
            // Symfony\Component\HttpFoundation\Response::setNotModified();
            Symfony\Component\HttpFoundation\Response::setNotModified();
        }

        /**
         * Returns true if the response includes a Vary header.
         *
         * @return Boolean true if the response includes a Vary header, false otherwise
         *
         * @api
         */
        public static function hasVary()
        {
            // Symfony\Component\HttpFoundation\Response::hasVary();
            Symfony\Component\HttpFoundation\Response::hasVary();
        }

        /**
         * Returns an array of header names given in the Vary header.
         *
         * @return array An array of Vary names
         *
         * @api
         */
        public static function getVary()
        {
            // Symfony\Component\HttpFoundation\Response::getVary();
            Symfony\Component\HttpFoundation\Response::getVary();
        }

        /**
         * Sets the Vary header.
         *
         * @param string|array $headers
         * @param Boolean      $replace Whether to replace the actual value of not (true by default)
         *
         * @return Response
         *
         * @api
         */
        public static function setVary($headers, $replace = "1")
        {
            // Symfony\Component\HttpFoundation\Response::setVary();
            Symfony\Component\HttpFoundation\Response::setVary($headers, $replace);
        }

        /**
         * Determines if the Response validators (ETag, Last-Modified) match
         * a conditional value specified in the Request.
         *
         * If the Response is not modified, it sets the status code to 304 and
         * removes the actual content by calling the setNotModified() method.
         *
         * @param Request $request A Request instance
         *
         * @return Boolean true if the Response validators match the Request, false otherwise
         *
         * @api
         */
        public static function isNotModified(Symfony\Component\HttpFoundation\Request $request)
        {
            // Symfony\Component\HttpFoundation\Response::isNotModified();
            Symfony\Component\HttpFoundation\Response::isNotModified($request);
        }

        /**
         * Is response invalid?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isInvalid()
        {
            // Symfony\Component\HttpFoundation\Response::isInvalid();
            Symfony\Component\HttpFoundation\Response::isInvalid();
        }

        /**
         * Is response informative?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isInformational()
        {
            // Symfony\Component\HttpFoundation\Response::isInformational();
            Symfony\Component\HttpFoundation\Response::isInformational();
        }

        /**
         * Is response successful?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isSuccessful()
        {
            // Symfony\Component\HttpFoundation\Response::isSuccessful();
            Symfony\Component\HttpFoundation\Response::isSuccessful();
        }

        /**
         * Is the response a redirect?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isRedirection()
        {
            // Symfony\Component\HttpFoundation\Response::isRedirection();
            Symfony\Component\HttpFoundation\Response::isRedirection();
        }

        /**
         * Is there a client error?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isClientError()
        {
            // Symfony\Component\HttpFoundation\Response::isClientError();
            Symfony\Component\HttpFoundation\Response::isClientError();
        }

        /**
         * Was there a server side error?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isServerError()
        {
            // Symfony\Component\HttpFoundation\Response::isServerError();
            Symfony\Component\HttpFoundation\Response::isServerError();
        }

        /**
         * Is the response OK?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isOk()
        {
            // Symfony\Component\HttpFoundation\Response::isOk();
            Symfony\Component\HttpFoundation\Response::isOk();
        }

        /**
         * Is the reponse forbidden?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isForbidden()
        {
            // Symfony\Component\HttpFoundation\Response::isForbidden();
            Symfony\Component\HttpFoundation\Response::isForbidden();
        }

        /**
         * Is the response a not found error?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isNotFound()
        {
            // Symfony\Component\HttpFoundation\Response::isNotFound();
            Symfony\Component\HttpFoundation\Response::isNotFound();
        }

        /**
         * Is the response a redirect of some form?
         *
         * @param string $location
         *
         * @return Boolean
         *
         * @api
         */
        public static function isRedirect($location = null)
        {
            // Symfony\Component\HttpFoundation\Response::isRedirect();
            Symfony\Component\HttpFoundation\Response::isRedirect($location);
        }

        /**
         * Is the response empty?
         *
         * @return Boolean
         *
         * @api
         */
        public static function isEmpty()
        {
            // Symfony\Component\HttpFoundation\Response::isEmpty();
            Symfony\Component\HttpFoundation\Response::isEmpty();
        }

    }

    /**
     *
     * The page object in Concrete encapsulates all the functionality used by a typical page and their contents
     * including blocks, page metadata, page permissions.
     * @package Pages
     *
     */
    class Page extends \Concrete\Core\Page\Page
    {

        /**
         * @param string $path /path/to/page
         * @param string $version ACTIVE or RECENT
         * @return Page
         */
        public static function getByPath($path, $version = "RECENT")
        {
            // Concrete\Core\Page\Page::getByPath();
            Concrete\Core\Page\Page::getByPath($path, $version);
        }

        /**
         * @param int $cID Collection ID of a page
         * @param string $versionOrig ACTIVE or RECENT
         * @param string $class
         * @return Page
         */
        public static function getByID($cID, $version = "RECENT", $class = "Page")
        {
            // Concrete\Core\Page\Page::getByID();
            Concrete\Core\Page\Page::getByID($cID, $version, $class);
        }

        public static function __construct()
        {
            // Concrete\Core\Page\Page::__construct();
            Concrete\Core\Page\Page::__construct();
        }

        /**
         * @access private
         */
        protected static function populatePage($cInfo, $where, $cvID)
        {
            // Concrete\Core\Page\Page::populatePage();
            Concrete\Core\Page\Page::populatePage($cInfo, $where, $cvID);
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\Page\Page::getPermissionResponseClassName();
            Concrete\Core\Page\Page::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\Page\Page::getPermissionAssignmentClassName();
            Concrete\Core\Page\Page::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\Page\Page::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\Page\Page::getPermissionObjectKeyCategoryHandle();
        }

        public static function getPageController()
        {
            // Concrete\Core\Page\Page::getPageController();
            Concrete\Core\Page\Page::getPageController();
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\Page\Page::getPermissionObjectIdentifier();
            Concrete\Core\Page\Page::getPermissionObjectIdentifier();
        }

        /**
         * Returns 1 if the page is in edit mode
         * @return bool
         */
        public static function isEditMode()
        {
            // Concrete\Core\Page\Page::isEditMode();
            Concrete\Core\Page\Page::isEditMode();
        }

        /**
         * Get the package ID for a page (page thats added by a package) (returns 0 if its not in a package)
         * @return int
         */
        public static function getPackageID()
        {
            // Concrete\Core\Page\Page::getPackageID();
            Concrete\Core\Page\Page::getPackageID();
        }

        /**
         * Get the package handle for a page (page thats added by a package)
         * @return string
         */
        public static function getPackageHandle()
        {
            // Concrete\Core\Page\Page::getPackageHandle();
            Concrete\Core\Page\Page::getPackageHandle();
        }

        /**
         * Returns 1 if the page is in arrange mode
         * @return bool
         */
        public static function isArrangeMode()
        {
            // Concrete\Core\Page\Page::isArrangeMode();
            Concrete\Core\Page\Page::isArrangeMode();
        }

        /**
         * Forces the page to be checked in if its checked out
         */
        public static function forceCheckIn()
        {
            // Concrete\Core\Page\Page::forceCheckIn();
            Concrete\Core\Page\Page::forceCheckIn();
        }

        /**
         * Checks if the page is a dashboard page, returns true if it is
         * @return bool
         */
        public static function isAdminArea()
        {
            // Concrete\Core\Page\Page::isAdminArea();
            Concrete\Core\Page\Page::isAdminArea();
        }

        /**
         * Uses a Request object to determine which page to load. queries by path and then
         * by cID
         */
        public static function getFromRequest(Concrete\Core\Http\Request $request)
        {
            // Concrete\Core\Page\Page::getFromRequest();
            Concrete\Core\Page\Page::getFromRequest($request);
        }

        public static function processArrangement($area_id, $moved_block_id, $block_order)
        {
            // Concrete\Core\Page\Page::processArrangement();
            Concrete\Core\Page\Page::processArrangement($area_id, $moved_block_id, $block_order);
        }

        /**
         * checks if the page is checked out, if it is return true
         * @return bool
         */
        public static function isCheckedOut()
        {
            // Concrete\Core\Page\Page::isCheckedOut();
            Concrete\Core\Page\Page::isCheckedOut();
        }

        /**
         * Gets the user that is editing the current page.
         * $return string $name
         */
        public static function getCollectionCheckedOutUserName()
        {
            // Concrete\Core\Page\Page::getCollectionCheckedOutUserName();
            Concrete\Core\Page\Page::getCollectionCheckedOutUserName();
        }

        /**
         * Checks if the page is checked out by the current user
         * @return bool
         */
        public static function isCheckedOutByMe()
        {
            // Concrete\Core\Page\Page::isCheckedOutByMe();
            Concrete\Core\Page\Page::isCheckedOutByMe();
        }

        /**
         * Checks if the page is a single page
         * @return bool
         */
        public static function isGeneratedCollection()
        {
            // Concrete\Core\Page\Page::isGeneratedCollection();
            Concrete\Core\Page\Page::isGeneratedCollection();
        }

        public static function assignPermissions($userOrGroup, $permissions = null, $accessType = 10)
        {
            // Concrete\Core\Page\Page::assignPermissions();
            Concrete\Core\Page\Page::assignPermissions($userOrGroup, $permissions, $accessType);
        }

        public static function getDrafts()
        {
            // Concrete\Core\Page\Page::getDrafts();
            Concrete\Core\Page\Page::getDrafts();
        }

        public static function isPageDraft()
        {
            // Concrete\Core\Page\Page::isPageDraft();
            Concrete\Core\Page\Page::isPageDraft();
        }

        public static function setController($controller)
        {
            // Concrete\Core\Page\Page::setController();
            Concrete\Core\Page\Page::setController($controller);
        }

        /**
         * @deprecated
         */
        public static function getController()
        {
            // Concrete\Core\Page\Page::getController();
            Concrete\Core\Page\Page::getController();
        }

        /**
         * @private
         */
        public static function assignPermissionSet($px)
        {
            // Concrete\Core\Page\Page::assignPermissionSet();
            Concrete\Core\Page\Page::assignPermissionSet($px);
        }

        /**
         * Make an alias to a page
         * @param Collection $c
         * @return int $newCID
         */
        public static function addCollectionAlias($c)
        {
            // Concrete\Core\Page\Page::addCollectionAlias();
            Concrete\Core\Page\Page::addCollectionAlias($c);
        }

        /**
         * Update the name, link, and to open in a new window for an external link
         * @param string $cName
         * @param string $cLink
         * @param bool $newWindow
         */
        public static function updateCollectionAliasExternal($cName, $cLink, $newWindow = null)
        {
            // Concrete\Core\Page\Page::updateCollectionAliasExternal();
            Concrete\Core\Page\Page::updateCollectionAliasExternal($cName, $cLink, $newWindow);
        }

        /**
         * Add a new external link
         * @param string $cName
         * @param string $cLink
         * @param bool $newWindow
         * @return int $newCID
         */
        public static function addCollectionAliasExternal($cName, $cLink, $newWindow = null)
        {
            // Concrete\Core\Page\Page::addCollectionAliasExternal();
            Concrete\Core\Page\Page::addCollectionAliasExternal($cName, $cLink, $newWindow);
        }

        /**
         * Check if a page is a single page that is in the core (/concrete directory)
         * @return bool
         */
        public static function isSystemPage()
        {
            // Concrete\Core\Page\Page::isSystemPage();
            Concrete\Core\Page\Page::isSystemPage();
        }

        /**
         * Gets the icon for a page (also fires the on_page_get_icon event)
         * @return string $icon Path to the icon
         */
        public static function getCollectionIcon()
        {
            // Concrete\Core\Page\Page::getCollectionIcon();
            Concrete\Core\Page\Page::getCollectionIcon();
        }

        /**
         * Remove an external link/alias
         * @return int $cIDRedir cID for the original page if the page was an alias
         */
        public static function removeThisAlias()
        {
            // Concrete\Core\Page\Page::removeThisAlias();
            Concrete\Core\Page\Page::removeThisAlias();
        }

        public static function populateRecursivePages($pages, $pageRow, $cParentID, $level, $includeThisPage = "1")
        {
            // Concrete\Core\Page\Page::populateRecursivePages();
            Concrete\Core\Page\Page::populateRecursivePages($pages, $pageRow, $cParentID, $level, $includeThisPage);
        }

        public static function queueForDeletionSort($a, $b)
        {
            // Concrete\Core\Page\Page::queueForDeletionSort();
            Concrete\Core\Page\Page::queueForDeletionSort($a, $b);
        }

        public static function queueForDuplicationSort($a, $b)
        {
            // Concrete\Core\Page\Page::queueForDuplicationSort();
            Concrete\Core\Page\Page::queueForDuplicationSort($a, $b);
        }

        public static function queueForDeletion()
        {
            // Concrete\Core\Page\Page::queueForDeletion();
            Concrete\Core\Page\Page::queueForDeletion();
        }

        public static function queueForDeletionRequest()
        {
            // Concrete\Core\Page\Page::queueForDeletionRequest();
            Concrete\Core\Page\Page::queueForDeletionRequest();
        }

        public static function queueForDuplication($destination, $includeParent = "1")
        {
            // Concrete\Core\Page\Page::queueForDuplication();
            Concrete\Core\Page\Page::queueForDuplication($destination, $includeParent);
        }

        public static function export($pageNode, $includePublicDate = null)
        {
            // Concrete\Core\Page\Page::export();
            Concrete\Core\Page\Page::export($pageNode, $includePublicDate);
        }

        /**
         * Returns the uID for a page that is checked out
         * @return int
         */
        public static function getCollectionCheckedOutUserID()
        {
            // Concrete\Core\Page\Page::getCollectionCheckedOutUserID();
            Concrete\Core\Page\Page::getCollectionCheckedOutUserID();
        }

        /**
         * Returns the path for the current page
         * @return string
         */
        public static function getCollectionPath()
        {
            // Concrete\Core\Page\Page::getCollectionPath();
            Concrete\Core\Page\Page::getCollectionPath();
        }

        /**
         * Returns full url for the current page
         * @return string
         */
        public static function getCollectionLink($appendBaseURL = null, $ignoreUrlRewriting = null)
        {
            // Concrete\Core\Page\Page::getCollectionLink();
            Concrete\Core\Page\Page::getCollectionLink($appendBaseURL, $ignoreUrlRewriting);
        }

        /**
         * Returns the path for a page from its cID
         * @param int cID
         * @return string $path
         */
        public static function getCollectionPathFromID($cID)
        {
            // Concrete\Core\Page\Page::getCollectionPathFromID();
            Concrete\Core\Page\Page::getCollectionPathFromID($cID);
        }

        /**
         * Returns the uID for a page ownder
         * @return int
         */
        public static function getCollectionUserID()
        {
            // Concrete\Core\Page\Page::getCollectionUserID();
            Concrete\Core\Page\Page::getCollectionUserID();
        }

        /**
         * Returns the page's handle
         * @return string
         */
        public static function getCollectionHandle()
        {
            // Concrete\Core\Page\Page::getCollectionHandle();
            Concrete\Core\Page\Page::getCollectionHandle();
        }

        /**
         * @deprecated
         */
        public static function getCollectionTypeName()
        {
            // Concrete\Core\Page\Page::getCollectionTypeName();
            Concrete\Core\Page\Page::getCollectionTypeName();
        }

        public static function getPageTypeName()
        {
            // Concrete\Core\Page\Page::getPageTypeName();
            Concrete\Core\Page\Page::getPageTypeName();
        }

        /**
         * @deprecated
         */
        public static function getCollectionTypeID()
        {
            // Concrete\Core\Page\Page::getCollectionTypeID();
            Concrete\Core\Page\Page::getCollectionTypeID();
        }

        /**
         * Returns the Collection Type ID
         * @return int
         */
        public static function getPageTypeID()
        {
            // Concrete\Core\Page\Page::getPageTypeID();
            Concrete\Core\Page\Page::getPageTypeID();
        }

        public static function getPageTypeObject()
        {
            // Concrete\Core\Page\Page::getPageTypeObject();
            Concrete\Core\Page\Page::getPageTypeObject();
        }

        /**
         * Returns the Page Template ID
         * @return int
         */
        public static function getPageTemplateID()
        {
            // Concrete\Core\Page\Page::getPageTemplateID();
            Concrete\Core\Page\Page::getPageTemplateID();
        }

        /**
         * Returns the Collection Type handle
         * @return string
         */
        public static function getPageTypeHandle()
        {
            // Concrete\Core\Page\Page::getPageTypeHandle();
            Concrete\Core\Page\Page::getPageTypeHandle();
        }

        public static function getCollectionTypeHandle()
        {
            // Concrete\Core\Page\Page::getCollectionTypeHandle();
            Concrete\Core\Page\Page::getCollectionTypeHandle();
        }

        /**
         * Returns theme id for the collection
         * @return int
         */
        public static function getCollectionThemeID()
        {
            // Concrete\Core\Page\Page::getCollectionThemeID();
            Concrete\Core\Page\Page::getCollectionThemeID();
        }

        /**
         * Check if a block is an alias from a page default
         * @param Block $b
         * @return bool
         */
        public static function isBlockAliasedFromMasterCollection($b)
        {
            // Concrete\Core\Page\Page::isBlockAliasedFromMasterCollection();
            Concrete\Core\Page\Page::isBlockAliasedFromMasterCollection($b);
        }

        /**
         * Returns Collection's theme object
         * @return PageTheme
         */
        public static function getCollectionThemeObject()
        {
            // Concrete\Core\Page\Page::getCollectionThemeObject();
            Concrete\Core\Page\Page::getCollectionThemeObject();
        }

        /**
         * Returns the page's name
         * @return string
         */
        public static function getCollectionName()
        {
            // Concrete\Core\Page\Page::getCollectionName();
            Concrete\Core\Page\Page::getCollectionName();
        }

        /**
         * Returns the collection ID for the aliased page (returns 0 unless used on an actual alias)
         * @return int
         */
        public static function getCollectionPointerID()
        {
            // Concrete\Core\Page\Page::getCollectionPointerID();
            Concrete\Core\Page\Page::getCollectionPointerID();
        }

        /**
         * Returns link for the aliased page
         * @return string
         */
        public static function getCollectionPointerExternalLink()
        {
            // Concrete\Core\Page\Page::getCollectionPointerExternalLink();
            Concrete\Core\Page\Page::getCollectionPointerExternalLink();
        }

        /**
         * Returns if the alias opens in a new window
         * @return bool
         */
        public static function openCollectionPointerExternalLinkInNewWindow()
        {
            // Concrete\Core\Page\Page::openCollectionPointerExternalLinkInNewWindow();
            Concrete\Core\Page\Page::openCollectionPointerExternalLinkInNewWindow();
        }

        /**
         * Checks to see if the page is an alias
         * @return bool
         */
        public static function isAlias()
        {
            // Concrete\Core\Page\Page::isAlias();
            Concrete\Core\Page\Page::isAlias();
        }

        /**
         * Checks if a page is an external link
         * @return bool
         */
        public static function isExternalLink()
        {
            // Concrete\Core\Page\Page::isExternalLink();
            Concrete\Core\Page\Page::isExternalLink();
        }

        /**
         * Get the original cID of a page
         * @return int
         */
        public static function getCollectionPointerOriginalID()
        {
            // Concrete\Core\Page\Page::getCollectionPointerOriginalID();
            Concrete\Core\Page\Page::getCollectionPointerOriginalID();
        }

        /**
         * Get the file name of a page (single pages)
         * @return string
         */
        public static function getCollectionFilename()
        {
            // Concrete\Core\Page\Page::getCollectionFilename();
            Concrete\Core\Page\Page::getCollectionFilename();
        }

        /**
         * Gets the date a the current version was made public,
         * if user is specified, returns in the current user's timezone
         * @param string $mask
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getCollectionDatePublic($mask = null, $type = "system")
        {
            // Concrete\Core\Page\Page::getCollectionDatePublic();
            Concrete\Core\Page\Page::getCollectionDatePublic($mask, $type);
        }

        /**
         * Get the description of a page
         * @return string
         */
        public static function getCollectionDescription()
        {
            // Concrete\Core\Page\Page::getCollectionDescription();
            Concrete\Core\Page\Page::getCollectionDescription();
        }

        /**
         * Gets the cID of the page's parent
         * @return int
         */
        public static function getCollectionParentID()
        {
            // Concrete\Core\Page\Page::getCollectionParentID();
            Concrete\Core\Page\Page::getCollectionParentID();
        }

        /**
         * Get the Parent cID from a page by using a cID
         * @param int $cID
         * @return int
         */
        public static function getCollectionParentIDFromChildID($cID)
        {
            // Concrete\Core\Page\Page::getCollectionParentIDFromChildID();
            Concrete\Core\Page\Page::getCollectionParentIDFromChildID($cID);
        }

        /**
         * Returns an array of this cParentID and aliased parentIDs
         * @return array $cID
         */
        public static function getCollectionParentIDs()
        {
            // Concrete\Core\Page\Page::getCollectionParentIDs();
            Concrete\Core\Page\Page::getCollectionParentIDs();
        }

        /**
         * Checks if a page is a page default
         * @return bool
         */
        public static function isMasterCollection()
        {
            // Concrete\Core\Page\Page::isMasterCollection();
            Concrete\Core\Page\Page::isMasterCollection();
        }

        /**
         * Gets the template permissions
         * @return string
         */
        public static function overrideTemplatePermissions()
        {
            // Concrete\Core\Page\Page::overrideTemplatePermissions();
            Concrete\Core\Page\Page::overrideTemplatePermissions();
        }

        /**
         * Gets the position of the page in the sitemap
         * @return int
         */
        public static function getCollectionDisplayOrder()
        {
            // Concrete\Core\Page\Page::getCollectionDisplayOrder();
            Concrete\Core\Page\Page::getCollectionDisplayOrder();
        }

        /**
         * Set the theme for a page using the page object
         * @param PageTheme $pl
         */
        public static function setTheme($pl)
        {
            // Concrete\Core\Page\Page::setTheme();
            Concrete\Core\Page\Page::setTheme($pl);
        }

        /**
         * Set the permissions of sub-collections added beneath this permissions to inherit from the template
         */
        public static function setPermissionsInheritanceToTemplate()
        {
            // Concrete\Core\Page\Page::setPermissionsInheritanceToTemplate();
            Concrete\Core\Page\Page::setPermissionsInheritanceToTemplate();
        }

        /**
         * Set the permissions of sub-collections added beneath this permissions to inherit from the parent
         */
        public static function setPermissionsInheritanceToOverride()
        {
            // Concrete\Core\Page\Page::setPermissionsInheritanceToOverride();
            Concrete\Core\Page\Page::setPermissionsInheritanceToOverride();
        }

        public static function getPermissionsCollectionID()
        {
            // Concrete\Core\Page\Page::getPermissionsCollectionID();
            Concrete\Core\Page\Page::getPermissionsCollectionID();
        }

        public static function getCollectionInheritance()
        {
            // Concrete\Core\Page\Page::getCollectionInheritance();
            Concrete\Core\Page\Page::getCollectionInheritance();
        }

        public static function getParentPermissionsCollectionID()
        {
            // Concrete\Core\Page\Page::getParentPermissionsCollectionID();
            Concrete\Core\Page\Page::getParentPermissionsCollectionID();
        }

        public static function getPermissionsCollectionObject()
        {
            // Concrete\Core\Page\Page::getPermissionsCollectionObject();
            Concrete\Core\Page\Page::getPermissionsCollectionObject();
        }

        /**
         * Given the current page's template and page type, we return the master page
         */
        public static function getMasterCollectionID()
        {
            // Concrete\Core\Page\Page::getMasterCollectionID();
            Concrete\Core\Page\Page::getMasterCollectionID();
        }

        public static function getOriginalCollectionID()
        {
            // Concrete\Core\Page\Page::getOriginalCollectionID();
            Concrete\Core\Page\Page::getOriginalCollectionID();
        }

        public static function getNumChildren()
        {
            // Concrete\Core\Page\Page::getNumChildren();
            Concrete\Core\Page\Page::getNumChildren();
        }

        public static function getNumChildrenDirect()
        {
            // Concrete\Core\Page\Page::getNumChildrenDirect();
            Concrete\Core\Page\Page::getNumChildrenDirect();
        }

        /**
         * Returns the first child of the current page, or null if there is no child
         * @param string $sortColumn
         * @return Page
         */
        public static function getFirstChild($sortColumn = "cDisplayOrder asc", $excludeSystemPages = null)
        {
            // Concrete\Core\Page\Page::getFirstChild();
            Concrete\Core\Page\Page::getFirstChild($sortColumn, $excludeSystemPages);
        }

        public static function getCollectionChildrenArray($oneLevelOnly = null)
        {
            // Concrete\Core\Page\Page::getCollectionChildrenArray();
            Concrete\Core\Page\Page::getCollectionChildrenArray($oneLevelOnly);
        }

        public static function _getNumChildren($cID, $oneLevelOnly = null, $sortColumn = "cDisplayOrder asc")
        {
            // Concrete\Core\Page\Page::_getNumChildren();
            Concrete\Core\Page\Page::_getNumChildren($cID, $oneLevelOnly, $sortColumn);
        }

        public static function canMoveCopyTo($cobj)
        {
            // Concrete\Core\Page\Page::canMoveCopyTo();
            Concrete\Core\Page\Page::canMoveCopyTo($cobj);
        }

        public static function updateCollectionName($name)
        {
            // Concrete\Core\Page\Page::updateCollectionName();
            Concrete\Core\Page\Page::updateCollectionName($name);
        }

        public static function hasPageThemeCustomizations()
        {
            // Concrete\Core\Page\Page::hasPageThemeCustomizations();
            Concrete\Core\Page\Page::hasPageThemeCustomizations();
        }

        public static function resetCustomThemeStyles()
        {
            // Concrete\Core\Page\Page::resetCustomThemeStyles();
            Concrete\Core\Page\Page::resetCustomThemeStyles();
        }

        public static function getCustomThemeStyles()
        {
            // Concrete\Core\Page\Page::getCustomThemeStyles();
            Concrete\Core\Page\Page::getCustomThemeStyles();
        }

        public static function updateCustomThemeStyles($styles)
        {
            // Concrete\Core\Page\Page::updateCustomThemeStyles();
            Concrete\Core\Page\Page::updateCustomThemeStyles($styles);
        }

        public static function writePageThemeCustomizations()
        {
            // Concrete\Core\Page\Page::writePageThemeCustomizations();
            Concrete\Core\Page\Page::writePageThemeCustomizations();
        }

        public static function update($data)
        {
            // Concrete\Core\Page\Page::update();
            Concrete\Core\Page\Page::update($data);
        }

        public static function uniquifyPagePath($origPath)
        {
            // Concrete\Core\Page\Page::uniquifyPagePath();
            Concrete\Core\Page\Page::uniquifyPagePath($origPath);
        }

        public static function rescanPagePaths($newPaths)
        {
            // Concrete\Core\Page\Page::rescanPagePaths();
            Concrete\Core\Page\Page::rescanPagePaths($newPaths);
        }

        public static function clearPagePermissions()
        {
            // Concrete\Core\Page\Page::clearPagePermissions();
            Concrete\Core\Page\Page::clearPagePermissions();
        }

        public static function inheritPermissionsFromParent()
        {
            // Concrete\Core\Page\Page::inheritPermissionsFromParent();
            Concrete\Core\Page\Page::inheritPermissionsFromParent();
        }

        public static function inheritPermissionsFromDefaults()
        {
            // Concrete\Core\Page\Page::inheritPermissionsFromDefaults();
            Concrete\Core\Page\Page::inheritPermissionsFromDefaults();
        }

        public static function setPermissionsToManualOverride()
        {
            // Concrete\Core\Page\Page::setPermissionsToManualOverride();
            Concrete\Core\Page\Page::setPermissionsToManualOverride();
        }

        public static function rescanAreaPermissions()
        {
            // Concrete\Core\Page\Page::rescanAreaPermissions();
            Concrete\Core\Page\Page::rescanAreaPermissions();
        }

        public static function setOverrideTemplatePermissions($cOverrideTemplatePermissions)
        {
            // Concrete\Core\Page\Page::setOverrideTemplatePermissions();
            Concrete\Core\Page\Page::setOverrideTemplatePermissions($cOverrideTemplatePermissions);
        }

        public static function updatePermissionsCollectionID($cParentIDString, $npID)
        {
            // Concrete\Core\Page\Page::updatePermissionsCollectionID();
            Concrete\Core\Page\Page::updatePermissionsCollectionID($cParentIDString, $npID);
        }

        public static function acquireAreaPermissions($permissionsCollectionID)
        {
            // Concrete\Core\Page\Page::acquireAreaPermissions();
            Concrete\Core\Page\Page::acquireAreaPermissions($permissionsCollectionID);
        }

        public static function acquirePagePermissions($permissionsCollectionID)
        {
            // Concrete\Core\Page\Page::acquirePagePermissions();
            Concrete\Core\Page\Page::acquirePagePermissions($permissionsCollectionID);
        }

        public static function __destruct()
        {
            // Concrete\Core\Page\Page::__destruct();
            Concrete\Core\Page\Page::__destruct();
        }

        public static function updateGroupsSubCollection($cParentIDString)
        {
            // Concrete\Core\Page\Page::updateGroupsSubCollection();
            Concrete\Core\Page\Page::updateGroupsSubCollection($cParentIDString);
        }

        public static function move($nc, $retainOldPagePath = null)
        {
            // Concrete\Core\Page\Page::move();
            Concrete\Core\Page\Page::move($nc, $retainOldPagePath);
        }

        public static function duplicateAll($nc, $preserveUserID = null)
        {
            // Concrete\Core\Page\Page::duplicateAll();
            Concrete\Core\Page\Page::duplicateAll($nc, $preserveUserID);
        }

        /**
         * @access private
         **/
        public static function _duplicateAll($cParent, $cNewParent, $preserveUserID = null)
        {
            // Concrete\Core\Page\Page::_duplicateAll();
            Concrete\Core\Page\Page::_duplicateAll($cParent, $cNewParent, $preserveUserID);
        }

        public static function duplicate($nc, $preserveUserID = null)
        {
            // Concrete\Core\Page\Page::duplicate();
            Concrete\Core\Page\Page::duplicate($nc, $preserveUserID);
        }

        public static function delete()
        {
            // Concrete\Core\Page\Page::delete();
            Concrete\Core\Page\Page::delete();
        }

        public static function moveToTrash()
        {
            // Concrete\Core\Page\Page::moveToTrash();
            Concrete\Core\Page\Page::moveToTrash();
        }

        public static function rescanChildrenDisplayOrder()
        {
            // Concrete\Core\Page\Page::rescanChildrenDisplayOrder();
            Concrete\Core\Page\Page::rescanChildrenDisplayOrder();
        }

        public static function getNextSubPageDisplayOrder()
        {
            // Concrete\Core\Page\Page::getNextSubPageDisplayOrder();
            Concrete\Core\Page\Page::getNextSubPageDisplayOrder();
        }

        public static function rescanCollectionPath($retainOldPagePath = null)
        {
            // Concrete\Core\Page\Page::rescanCollectionPath();
            Concrete\Core\Page\Page::rescanCollectionPath($retainOldPagePath);
        }

        public static function updateDisplayOrder($do, $cID = null)
        {
            // Concrete\Core\Page\Page::updateDisplayOrder();
            Concrete\Core\Page\Page::updateDisplayOrder($do, $cID);
        }

        public static function movePageDisplayOrderToTop()
        {
            // Concrete\Core\Page\Page::movePageDisplayOrderToTop();
            Concrete\Core\Page\Page::movePageDisplayOrderToTop();
        }

        public static function movePageDisplayOrderToBottom()
        {
            // Concrete\Core\Page\Page::movePageDisplayOrderToBottom();
            Concrete\Core\Page\Page::movePageDisplayOrderToBottom();
        }

        public static function movePageDisplayOrderToSibling(Concrete\Core\Page\Page $c, $position = "before")
        {
            // Concrete\Core\Page\Page::movePageDisplayOrderToSibling();
            Concrete\Core\Page\Page::movePageDisplayOrderToSibling($c, $position);
        }

        public static function rescanCollectionPathIndividual($cID, $cPath, $retainOldPagePath = null)
        {
            // Concrete\Core\Page\Page::rescanCollectionPathIndividual();
            Concrete\Core\Page\Page::rescanCollectionPathIndividual($cID, $cPath, $retainOldPagePath);
        }

        public static function rescanSystemPageStatus()
        {
            // Concrete\Core\Page\Page::rescanSystemPageStatus();
            Concrete\Core\Page\Page::rescanSystemPageStatus();
        }

        public static function isInTrash()
        {
            // Concrete\Core\Page\Page::isInTrash();
            Concrete\Core\Page\Page::isInTrash();
        }

        public static function moveToRoot()
        {
            // Concrete\Core\Page\Page::moveToRoot();
            Concrete\Core\Page\Page::moveToRoot();
        }

        public static function rescanSystemPages()
        {
            // Concrete\Core\Page\Page::rescanSystemPages();
            Concrete\Core\Page\Page::rescanSystemPages();
        }

        public static function deactivate()
        {
            // Concrete\Core\Page\Page::deactivate();
            Concrete\Core\Page\Page::deactivate();
        }

        public static function activate()
        {
            // Concrete\Core\Page\Page::activate();
            Concrete\Core\Page\Page::activate();
        }

        public static function isActive()
        {
            // Concrete\Core\Page\Page::isActive();
            Concrete\Core\Page\Page::isActive();
        }

        public static function setPageIndexScore($score)
        {
            // Concrete\Core\Page\Page::setPageIndexScore();
            Concrete\Core\Page\Page::setPageIndexScore($score);
        }

        public static function getPageIndexScore()
        {
            // Concrete\Core\Page\Page::getPageIndexScore();
            Concrete\Core\Page\Page::getPageIndexScore();
        }

        public static function getPageIndexContent()
        {
            // Concrete\Core\Page\Page::getPageIndexContent();
            Concrete\Core\Page\Page::getPageIndexContent();
        }

        public static function rescanCollectionPathChildren($cID, $cPath)
        {
            // Concrete\Core\Page\Page::rescanCollectionPathChildren();
            Concrete\Core\Page\Page::rescanCollectionPathChildren($cID, $cPath);
        }

        public static function getCollectionAction()
        {
            // Concrete\Core\Page\Page::getCollectionAction();
            Concrete\Core\Page\Page::getCollectionAction();
        }

        public static function _associateMasterCollectionBlocks($newCID, $masterCID)
        {
            // Concrete\Core\Page\Page::_associateMasterCollectionBlocks();
            Concrete\Core\Page\Page::_associateMasterCollectionBlocks($newCID, $masterCID);
        }

        public static function _associateMasterCollectionAttributes($newCID, $masterCID)
        {
            // Concrete\Core\Page\Page::_associateMasterCollectionAttributes();
            Concrete\Core\Page\Page::_associateMasterCollectionAttributes($newCID, $masterCID);
        }

        /**
         * Adds the home page to the system. Typically used only by the installation program.
         * @return page
         **/
        public static function addHomePage()
        {
            // Concrete\Core\Page\Page::addHomePage();
            Concrete\Core\Page\Page::addHomePage();
        }

        /**
         * Adds a new page of a certain type, using a passed associate array to setup value. $data may contain any or all of the following:
         * "uID": User ID of the page's owner
         * "pkgID": Package ID the page belongs to
         * "cName": The name of the page
         * "cHandle": The handle of the page as used in the path
         * "cDatePublic": The date assigned to the page
         * @param collectiontype $ct
         * @param array $data
         * @return page
         **/
        public static function add($pt, $data, $template = null)
        {
            // Concrete\Core\Page\Page::add();
            Concrete\Core\Page\Page::add($pt, $data, $template);
        }

        public static function getCollectionFullPageCaching()
        {
            // Concrete\Core\Page\Page::getCollectionFullPageCaching();
            Concrete\Core\Page\Page::getCollectionFullPageCaching();
        }

        public static function getCollectionFullPageCachingLifetime()
        {
            // Concrete\Core\Page\Page::getCollectionFullPageCachingLifetime();
            Concrete\Core\Page\Page::getCollectionFullPageCachingLifetime();
        }

        public static function getCollectionFullPageCachingLifetimeCustomValue()
        {
            // Concrete\Core\Page\Page::getCollectionFullPageCachingLifetimeCustomValue();
            Concrete\Core\Page\Page::getCollectionFullPageCachingLifetimeCustomValue();
        }

        public static function getCollectionFullPageCachingLifetimeValue()
        {
            // Concrete\Core\Page\Page::getCollectionFullPageCachingLifetimeValue();
            Concrete\Core\Page\Page::getCollectionFullPageCachingLifetimeValue();
        }

        public static function addStatic($data)
        {
            // Concrete\Core\Page\Page::addStatic();
            Concrete\Core\Page\Page::addStatic($data);
        }

        public static function getPagePaths()
        {
            // Concrete\Core\Page\Page::getPagePaths();
            Concrete\Core\Page\Page::getPagePaths();
        }

        public static function getCurrentPage()
        {
            // Concrete\Core\Page\Page::getCurrentPage();
            Concrete\Core\Page\Page::getCurrentPage();
        }

        /**
         * Returns the total number of page views for a specific page
         */
        public static function getTotalPageViews($date = null)
        {
            // Concrete\Core\Page\Page::getTotalPageViews();
            Concrete\Core\Page\Page::getTotalPageViews($date);
        }

        public static function getPageDraftTargetParentPageID()
        {
            // Concrete\Core\Page\Page::getPageDraftTargetParentPageID();
            Concrete\Core\Page\Page::getPageDraftTargetParentPageID();
        }

        public static function setPageDraftTargetParentPageID($cParentID)
        {
            // Concrete\Core\Page\Page::setPageDraftTargetParentPageID();
            Concrete\Core\Page\Page::setPageDraftTargetParentPageID($cParentID);
        }

        /**
         * Gets a pages statistics
         */
        public static function getPageStatistics($limit = 20)
        {
            // Concrete\Core\Page\Page::getPageStatistics();
            Concrete\Core\Page\Page::getPageStatistics($limit);
        }

        public static function loadVersionObject($cvID = "ACTIVE")
        {
            // Concrete\Core\Page\Collection\Collection::loadVersionObject();
            Concrete\Core\Page\Collection\Collection::loadVersionObject($cvID);
        }

        public static function getVersionToModify()
        {
            // Concrete\Core\Page\Collection\Collection::getVersionToModify();
            Concrete\Core\Page\Collection\Collection::getVersionToModify();
        }

        public static function getNextVersionComments()
        {
            // Concrete\Core\Page\Collection\Collection::getNextVersionComments();
            Concrete\Core\Page\Collection\Collection::getNextVersionComments();
        }

        public static function cloneVersion($versionComments)
        {
            // Concrete\Core\Page\Collection\Collection::cloneVersion();
            Concrete\Core\Page\Collection\Collection::cloneVersion($versionComments);
        }

        public static function getFeatureAssignments()
        {
            // Concrete\Core\Page\Collection\Collection::getFeatureAssignments();
            Concrete\Core\Page\Collection\Collection::getFeatureAssignments();
        }

        /**
         * Returns the value of the attribute with the handle $ak
         * of the current object.
         *
         * $displayMode makes it possible to get the correct output
         * value. When you need the raw attribute value or object, use
         * this:
         * <code>
         * $c = Page::getCurrentPage();
         * $attributeValue = $c->getAttribute('attribute_handle');
         * </code>
         *
         * But if you need the formatted output supported by some
         * attribute, use this:
         * <code>
         * $c = Page::getCurrentPage();
         * $attributeValue = $c->getAttribute('attribute_handle', 'display');
         * </code>
         *
         * An attribute type like "date" will then return the date in
         * the correct format just like other attributes will show
         * you a nicely formatted output and not just a simple value
         * or object.
         *
         *
         * @param string|object $akHandle
         * @param boolean $displayMode
         * @return type
         */
        public static function getAttribute($akHandle, $displayMode = null)
        {
            // Concrete\Core\Page\Collection\Collection::getAttribute();
            Concrete\Core\Page\Collection\Collection::getAttribute($akHandle, $displayMode);
        }

        public static function getCollectionAttributeValue($ak)
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionAttributeValue();
            Concrete\Core\Page\Collection\Collection::getCollectionAttributeValue($ak);
        }

        public static function clearCollectionAttributes($retainAKIDs = null)
        {
            // Concrete\Core\Page\Collection\Collection::clearCollectionAttributes();
            Concrete\Core\Page\Collection\Collection::clearCollectionAttributes($retainAKIDs);
        }

        public static function reindexPendingPages()
        {
            // Concrete\Core\Page\Collection\Collection::reindexPendingPages();
            Concrete\Core\Page\Collection\Collection::reindexPendingPages();
        }

        public static function reindex($index = null, $actuallyDoReindex = "1")
        {
            // Concrete\Core\Page\Collection\Collection::reindex();
            Concrete\Core\Page\Collection\Collection::reindex($index, $actuallyDoReindex);
        }

        public static function getAttributeValueObject($ak, $createIfNotFound = null)
        {
            // Concrete\Core\Page\Collection\Collection::getAttributeValueObject();
            Concrete\Core\Page\Collection\Collection::getAttributeValueObject($ak, $createIfNotFound);
        }

        public static function setAttribute($ak, $value)
        {
            // Concrete\Core\Page\Collection\Collection::setAttribute();
            Concrete\Core\Page\Collection\Collection::setAttribute($ak, $value);
        }

        public static function clearAttribute($ak)
        {
            // Concrete\Core\Page\Collection\Collection::clearAttribute();
            Concrete\Core\Page\Collection\Collection::clearAttribute($ak);
        }

        public static function getSetCollectionAttributes()
        {
            // Concrete\Core\Page\Collection\Collection::getSetCollectionAttributes();
            Concrete\Core\Page\Collection\Collection::getSetCollectionAttributes();
        }

        public static function addAttribute($ak, $value)
        {
            // Concrete\Core\Page\Collection\Collection::addAttribute();
            Concrete\Core\Page\Collection\Collection::addAttribute($ak, $value);
        }

        public static function getArea($arHandle)
        {
            // Concrete\Core\Page\Collection\Collection::getArea();
            Concrete\Core\Page\Collection\Collection::getArea($arHandle);
        }

        public static function hasAliasedContent()
        {
            // Concrete\Core\Page\Collection\Collection::hasAliasedContent();
            Concrete\Core\Page\Collection\Collection::hasAliasedContent();
        }

        public static function getCollectionID()
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionID();
            Concrete\Core\Page\Collection\Collection::getCollectionID();
        }

        public static function getCollectionDateLastModified($mask = null, $type = "system")
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionDateLastModified();
            Concrete\Core\Page\Collection\Collection::getCollectionDateLastModified($mask, $type);
        }

        public static function getVersionObject()
        {
            // Concrete\Core\Page\Collection\Collection::getVersionObject();
            Concrete\Core\Page\Collection\Collection::getVersionObject();
        }

        public static function getCollectionDateAdded($mask = null, $type = "system")
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionDateAdded();
            Concrete\Core\Page\Collection\Collection::getCollectionDateAdded($mask, $type);
        }

        public static function getVersionID()
        {
            // Concrete\Core\Page\Collection\Collection::getVersionID();
            Concrete\Core\Page\Collection\Collection::getVersionID();
        }

        public static function getCollectionAreaDisplayOrder($arHandle, $ignoreVersions = null)
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionAreaDisplayOrder();
            Concrete\Core\Page\Collection\Collection::getCollectionAreaDisplayOrder($arHandle, $ignoreVersions);
        }

        /**
         * Retrieves all custom style rules that should be inserted into the header on a page, whether they are defined in areas
         * or blocks
         */
        public static function outputCustomStyleHeaderItems($return = null)
        {
            // Concrete\Core\Page\Collection\Collection::outputCustomStyleHeaderItems();
            Concrete\Core\Page\Collection\Collection::outputCustomStyleHeaderItems($return);
        }

        public static function getAreaCustomStyleRule($area)
        {
            // Concrete\Core\Page\Collection\Collection::getAreaCustomStyleRule();
            Concrete\Core\Page\Collection\Collection::getAreaCustomStyleRule($area);
        }

        public static function resetAreaCustomStyle($area)
        {
            // Concrete\Core\Page\Collection\Collection::resetAreaCustomStyle();
            Concrete\Core\Page\Collection\Collection::resetAreaCustomStyle($area);
        }

        public static function setAreaCustomStyle($area, $csr)
        {
            // Concrete\Core\Page\Collection\Collection::setAreaCustomStyle();
            Concrete\Core\Page\Collection\Collection::setAreaCustomStyle($area, $csr);
        }

        public static function relateVersionEdits($oc)
        {
            // Concrete\Core\Page\Collection\Collection::relateVersionEdits();
            Concrete\Core\Page\Collection\Collection::relateVersionEdits($oc);
        }

        public static function rescanDisplayOrder($areaName)
        {
            // Concrete\Core\Page\Collection\Collection::rescanDisplayOrder();
            Concrete\Core\Page\Collection\Collection::rescanDisplayOrder($areaName);
        }

        public static function getByHandle($handle)
        {
            // Concrete\Core\Page\Collection\Collection::getByHandle();
            Concrete\Core\Page\Collection\Collection::getByHandle($handle);
        }

        public static function refreshCache()
        {
            // Concrete\Core\Page\Collection\Collection::refreshCache();
            Concrete\Core\Page\Collection\Collection::refreshCache();
        }

        public static function getGlobalBlocks()
        {
            // Concrete\Core\Page\Collection\Collection::getGlobalBlocks();
            Concrete\Core\Page\Collection\Collection::getGlobalBlocks();
        }

        /**
         * List the block IDs in a collection or area within a collection
         * @param string $arHandle. If specified, returns just the blocks in an area
         * @return array
         */
        public static function getBlockIDs($arHandle = null)
        {
            // Concrete\Core\Page\Collection\Collection::getBlockIDs();
            Concrete\Core\Page\Collection\Collection::getBlockIDs($arHandle);
        }

        /**
         * List the blocks in a collection or area within a collection
         * @param string $arHandle. If specified, returns just the blocks in an area
         * @return array
         */
        public static function getBlocks($arHandle = null)
        {
            // Concrete\Core\Page\Collection\Collection::getBlocks();
            Concrete\Core\Page\Collection\Collection::getBlocks($arHandle);
        }

        public static function addBlock($bt, $a, $data)
        {
            // Concrete\Core\Page\Collection\Collection::addBlock();
            Concrete\Core\Page\Collection\Collection::addBlock($bt, $a, $data);
        }

        public static function addFeature(Concrete\Core\Feature\Feature $fe)
        {
            // Concrete\Core\Page\Collection\Collection::addFeature();
            Concrete\Core\Page\Collection\Collection::addFeature($fe);
        }

        public static function addCollection($data)
        {
            // Concrete\Core\Page\Collection\Collection::addCollection();
            Concrete\Core\Page\Collection\Collection::addCollection($data);
        }

        public static function markModified()
        {
            // Concrete\Core\Page\Collection\Collection::markModified();
            Concrete\Core\Page\Collection\Collection::markModified();
        }

        public static function duplicateCollection()
        {
            // Concrete\Core\Page\Collection\Collection::duplicateCollection();
            Concrete\Core\Page\Collection\Collection::duplicateCollection();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class PageEditResponse extends \Concrete\Core\Page\EditResponse
    {

        public static function setPage(Concrete\Core\Page\Page $page)
        {
            // Concrete\Core\Page\EditResponse::setPage();
            Concrete\Core\Page\EditResponse::setPage($page);
        }

        public static function setPages($pages)
        {
            // Concrete\Core\Page\EditResponse::setPages();
            Concrete\Core\Page\EditResponse::setPages($pages);
        }

        public static function getJSONObject()
        {
            // Concrete\Core\Page\EditResponse::getJSONObject();
            Concrete\Core\Page\EditResponse::getJSONObject();
        }

        public static function setRedirectURL($url)
        {
            // Concrete\Core\Application\EditResponse::setRedirectURL();
            Concrete\Core\Application\EditResponse::setRedirectURL($url);
        }

        public static function getRedirectURL()
        {
            // Concrete\Core\Application\EditResponse::getRedirectURL();
            Concrete\Core\Application\EditResponse::getRedirectURL();
        }

        public static function __construct($e = null)
        {
            // Concrete\Core\Application\EditResponse::__construct();
            Concrete\Core\Application\EditResponse::__construct($e);
        }

        public static function setError($error)
        {
            // Concrete\Core\Application\EditResponse::setError();
            Concrete\Core\Application\EditResponse::setError($error);
        }

        public static function setMessage($message)
        {
            // Concrete\Core\Application\EditResponse::setMessage();
            Concrete\Core\Application\EditResponse::setMessage($message);
        }

        public static function getMessage()
        {
            // Concrete\Core\Application\EditResponse::getMessage();
            Concrete\Core\Application\EditResponse::getMessage();
        }

        public static function setTitle($title)
        {
            // Concrete\Core\Application\EditResponse::setTitle();
            Concrete\Core\Application\EditResponse::setTitle($title);
        }

        public static function getTitle()
        {
            // Concrete\Core\Application\EditResponse::getTitle();
            Concrete\Core\Application\EditResponse::getTitle();
        }

        public static function getJSON()
        {
            // Concrete\Core\Application\EditResponse::getJSON();
            Concrete\Core\Application\EditResponse::getJSON();
        }

        public static function setAdditionalDataAttribute($key, $value)
        {
            // Concrete\Core\Application\EditResponse::setAdditionalDataAttribute();
            Concrete\Core\Application\EditResponse::setAdditionalDataAttribute($key, $value);
        }

        public static function getBaseJSONObject()
        {
            // Concrete\Core\Application\EditResponse::getBaseJSONObject();
            Concrete\Core\Application\EditResponse::getBaseJSONObject();
        }

        public static function outputJSON()
        {
            // Concrete\Core\Application\EditResponse::outputJSON();
            Concrete\Core\Application\EditResponse::outputJSON();
        }

    }

    class Controller extends \Concrete\Core\Controller\Controller
    {

        public static function setViewObject(Concrete\Core\View\View $view)
        {
            // Concrete\Core\Controller\Controller::setViewObject();
            Concrete\Core\Controller\Controller::setViewObject($view);
        }

        public static function setTheme($mixed)
        {
            // Concrete\Core\Controller\Controller::setTheme();
            Concrete\Core\Controller\Controller::setTheme($mixed);
        }

        public static function getTheme()
        {
            // Concrete\Core\Controller\Controller::getTheme();
            Concrete\Core\Controller\Controller::getTheme();
        }

        public static function getControllerActionPath()
        {
            // Concrete\Core\Controller\Controller::getControllerActionPath();
            Concrete\Core\Controller\Controller::getControllerActionPath();
        }

        public static function __construct()
        {
            // Concrete\Core\Controller\Controller::__construct();
            Concrete\Core\Controller\Controller::__construct();
        }

        public static function getViewObject()
        {
            // Concrete\Core\Controller\Controller::getViewObject();
            Concrete\Core\Controller\Controller::getViewObject();
        }

        public static function action()
        {
            // Concrete\Core\Controller\Controller::action();
            Concrete\Core\Controller\Controller::action();
        }

        public static function requireAsset()
        {
            // Concrete\Core\Controller\AbstractController::requireAsset();
            Concrete\Core\Controller\AbstractController::requireAsset();
        }

        /**
         * Adds an item to the view's header. This item will then be automatically printed out before the <body> section of the page
         * @param string $item
         * @return void
         */
        public static function addHeaderItem($item)
        {
            // Concrete\Core\Controller\AbstractController::addHeaderItem();
            Concrete\Core\Controller\AbstractController::addHeaderItem($item);
        }

        /**
         * Adds an item to the view's footer. This item will then be automatically printed out before the </body> section of the page
         * @param string $item
         * @return void
         */
        public static function addFooterItem($item)
        {
            // Concrete\Core\Controller\AbstractController::addFooterItem();
            Concrete\Core\Controller\AbstractController::addFooterItem($item);
        }

        public static function set($key, $val)
        {
            // Concrete\Core\Controller\AbstractController::set();
            Concrete\Core\Controller\AbstractController::set($key, $val);
        }

        public static function getSets()
        {
            // Concrete\Core\Controller\AbstractController::getSets();
            Concrete\Core\Controller\AbstractController::getSets();
        }

        public static function getHelperObjects()
        {
            // Concrete\Core\Controller\AbstractController::getHelperObjects();
            Concrete\Core\Controller\AbstractController::getHelperObjects();
        }

        public static function get($key = null, $defaultValue = null)
        {
            // Concrete\Core\Controller\AbstractController::get();
            Concrete\Core\Controller\AbstractController::get($key, $defaultValue);
        }

        public static function getTask()
        {
            // Concrete\Core\Controller\AbstractController::getTask();
            Concrete\Core\Controller\AbstractController::getTask();
        }

        public static function getAction()
        {
            // Concrete\Core\Controller\AbstractController::getAction();
            Concrete\Core\Controller\AbstractController::getAction();
        }

        public static function getParameters()
        {
            // Concrete\Core\Controller\AbstractController::getParameters();
            Concrete\Core\Controller\AbstractController::getParameters();
        }

        public static function runAction($action, $parameters = null)
        {
            // Concrete\Core\Controller\AbstractController::runAction();
            Concrete\Core\Controller\AbstractController::runAction($action, $parameters);
        }

        public static function on_start()
        {
            // Concrete\Core\Controller\AbstractController::on_start();
            Concrete\Core\Controller\AbstractController::on_start();
        }

        public static function on_before_render()
        {
            // Concrete\Core\Controller\AbstractController::on_before_render();
            Concrete\Core\Controller\AbstractController::on_before_render();
        }

        /**
         * @deprecated
         */
        public static function isPost()
        {
            // Concrete\Core\Controller\AbstractController::isPost();
            Concrete\Core\Controller\AbstractController::isPost();
        }

        public static function post($key = null)
        {
            // Concrete\Core\Controller\AbstractController::post();
            Concrete\Core\Controller\AbstractController::post($key);
        }

        public static function redirect()
        {
            // Concrete\Core\Controller\AbstractController::redirect();
            Concrete\Core\Controller\AbstractController::redirect();
        }

        public static function runTask($action, $parameters)
        {
            // Concrete\Core\Controller\AbstractController::runTask();
            Concrete\Core\Controller\AbstractController::runTask($action, $parameters);
        }

        public static function request($key = null)
        {
            // Concrete\Core\Controller\AbstractController::request();
            Concrete\Core\Controller\AbstractController::request($key);
        }

    }

    class PageController extends \Concrete\Core\Page\Controller\PageController
    {

        public static function supportsPageCache()
        {
            // Concrete\Core\Page\Controller\PageController::supportsPageCache();
            Concrete\Core\Page\Controller\PageController::supportsPageCache();
        }

        public static function __construct(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Page\Controller\PageController::__construct();
            Concrete\Core\Page\Controller\PageController::__construct($c);
        }

        public static function getPageObject()
        {
            // Concrete\Core\Page\Controller\PageController::getPageObject();
            Concrete\Core\Page\Controller\PageController::getPageObject();
        }

        public static function getTheme()
        {
            // Concrete\Core\Page\Controller\PageController::getTheme();
            Concrete\Core\Page\Controller\PageController::getTheme();
        }

        public static function getRequestAction()
        {
            // Concrete\Core\Page\Controller\PageController::getRequestAction();
            Concrete\Core\Page\Controller\PageController::getRequestAction();
        }

        public static function getRequestActionParameters()
        {
            // Concrete\Core\Page\Controller\PageController::getRequestActionParameters();
            Concrete\Core\Page\Controller\PageController::getRequestActionParameters();
        }

        public static function getControllerActionPath()
        {
            // Concrete\Core\Page\Controller\PageController::getControllerActionPath();
            Concrete\Core\Page\Controller\PageController::getControllerActionPath();
        }

        public static function passthru($arHandle = null, $bID = null, $action = null)
        {
            // Concrete\Core\Page\Controller\PageController::passthru();
            Concrete\Core\Page\Controller\PageController::passthru($arHandle, $bID, $action);
        }

        public static function setupRequestActionAndParameters(Concrete\Core\Http\Request $request)
        {
            // Concrete\Core\Page\Controller\PageController::setupRequestActionAndParameters();
            Concrete\Core\Page\Controller\PageController::setupRequestActionAndParameters($request);
        }

        public static function validateRequest()
        {
            // Concrete\Core\Page\Controller\PageController::validateRequest();
            Concrete\Core\Page\Controller\PageController::validateRequest();
        }

        public static function setViewObject(Concrete\Core\View\View $view)
        {
            // Concrete\Core\Controller\Controller::setViewObject();
            Concrete\Core\Controller\Controller::setViewObject($view);
        }

        public static function setTheme($mixed)
        {
            // Concrete\Core\Controller\Controller::setTheme();
            Concrete\Core\Controller\Controller::setTheme($mixed);
        }

        public static function getViewObject()
        {
            // Concrete\Core\Controller\Controller::getViewObject();
            Concrete\Core\Controller\Controller::getViewObject();
        }

        public static function action()
        {
            // Concrete\Core\Controller\Controller::action();
            Concrete\Core\Controller\Controller::action();
        }

        public static function requireAsset()
        {
            // Concrete\Core\Controller\AbstractController::requireAsset();
            Concrete\Core\Controller\AbstractController::requireAsset();
        }

        /**
         * Adds an item to the view's header. This item will then be automatically printed out before the <body> section of the page
         * @param string $item
         * @return void
         */
        public static function addHeaderItem($item)
        {
            // Concrete\Core\Controller\AbstractController::addHeaderItem();
            Concrete\Core\Controller\AbstractController::addHeaderItem($item);
        }

        /**
         * Adds an item to the view's footer. This item will then be automatically printed out before the </body> section of the page
         * @param string $item
         * @return void
         */
        public static function addFooterItem($item)
        {
            // Concrete\Core\Controller\AbstractController::addFooterItem();
            Concrete\Core\Controller\AbstractController::addFooterItem($item);
        }

        public static function set($key, $val)
        {
            // Concrete\Core\Controller\AbstractController::set();
            Concrete\Core\Controller\AbstractController::set($key, $val);
        }

        public static function getSets()
        {
            // Concrete\Core\Controller\AbstractController::getSets();
            Concrete\Core\Controller\AbstractController::getSets();
        }

        public static function getHelperObjects()
        {
            // Concrete\Core\Controller\AbstractController::getHelperObjects();
            Concrete\Core\Controller\AbstractController::getHelperObjects();
        }

        public static function get($key = null, $defaultValue = null)
        {
            // Concrete\Core\Controller\AbstractController::get();
            Concrete\Core\Controller\AbstractController::get($key, $defaultValue);
        }

        public static function getTask()
        {
            // Concrete\Core\Controller\AbstractController::getTask();
            Concrete\Core\Controller\AbstractController::getTask();
        }

        public static function getAction()
        {
            // Concrete\Core\Controller\AbstractController::getAction();
            Concrete\Core\Controller\AbstractController::getAction();
        }

        public static function getParameters()
        {
            // Concrete\Core\Controller\AbstractController::getParameters();
            Concrete\Core\Controller\AbstractController::getParameters();
        }

        public static function runAction($action, $parameters = null)
        {
            // Concrete\Core\Controller\AbstractController::runAction();
            Concrete\Core\Controller\AbstractController::runAction($action, $parameters);
        }

        public static function on_start()
        {
            // Concrete\Core\Controller\AbstractController::on_start();
            Concrete\Core\Controller\AbstractController::on_start();
        }

        public static function on_before_render()
        {
            // Concrete\Core\Controller\AbstractController::on_before_render();
            Concrete\Core\Controller\AbstractController::on_before_render();
        }

        /**
         * @deprecated
         */
        public static function isPost()
        {
            // Concrete\Core\Controller\AbstractController::isPost();
            Concrete\Core\Controller\AbstractController::isPost();
        }

        public static function post($key = null)
        {
            // Concrete\Core\Controller\AbstractController::post();
            Concrete\Core\Controller\AbstractController::post($key);
        }

        public static function redirect()
        {
            // Concrete\Core\Controller\AbstractController::redirect();
            Concrete\Core\Controller\AbstractController::redirect();
        }

        public static function runTask($action, $parameters)
        {
            // Concrete\Core\Controller\AbstractController::runTask();
            Concrete\Core\Controller\AbstractController::runTask($action, $parameters);
        }

        public static function request($key = null)
        {
            // Concrete\Core\Controller\AbstractController::request();
            Concrete\Core\Controller\AbstractController::request($key);
        }

    }

    /**
     *
     * SinglePage extends the page class for those instances of pages that have no type, and are special "single pages"
     * within the system.
     * @package Pages
     *
     */
    class SinglePage extends \Concrete\Core\Page\Single
    {

        public static function getThemeableCorePages()
        {
            // Concrete\Core\Page\Single::getThemeableCorePages();
            Concrete\Core\Page\Single::getThemeableCorePages();
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Page\Single::getListByPackage();
            Concrete\Core\Page\Single::getListByPackage($pkg);
        }

        public static function sanitizePath($path)
        {
            // Concrete\Core\Page\Single::sanitizePath();
            Concrete\Core\Page\Single::sanitizePath($path);
        }

        public static function getPathToNode($node, $pkg)
        {
            // Concrete\Core\Page\Single::getPathToNode();
            Concrete\Core\Page\Single::getPathToNode($node, $pkg);
        }

        public static function refresh(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Page\Single::refresh();
            Concrete\Core\Page\Single::refresh($c);
        }

        public static function getByID($cID, $version = "RECENT")
        {
            // Concrete\Core\Page\Single::getByID();
            Concrete\Core\Page\Single::getByID($cID, $version);
        }

        public static function add($cPath, $pkg = null)
        {
            // Concrete\Core\Page\Single::add();
            Concrete\Core\Page\Single::add($cPath, $pkg);
        }

        public static function getList()
        {
            // Concrete\Core\Page\Single::getList();
            Concrete\Core\Page\Single::getList();
        }

    }

    class Config extends \Concrete\Core\Config\Config
    {

        public static function setStore(Concrete\Core\Config\ConfigStore $store)
        {
            // Concrete\Core\Config\Config::setStore();
            Concrete\Core\Config\Config::setStore($store);
        }

        /**
         * @return ConfigStore
         */
        protected static function getStore()
        {
            // Concrete\Core\Config\Config::getStore();
            Concrete\Core\Config\Config::getStore();
        }

        public static function setPackageObject($pkg)
        {
            // Concrete\Core\Config\Config::setPackageObject();
            Concrete\Core\Config\Config::setPackageObject($pkg);
        }

        /**
         * Gets the config value for a given key
         * @param string $cfKey
         * @param bool $getFullObject
         * @return string or full object $cv
         */
        public static function get($cfKey, $getFullObject = null)
        {
            // Concrete\Core\Config\Config::get();
            Concrete\Core\Config\Config::get($cfKey, $getFullObject);
        }

        /**
         * gets a list of all the configs associated with a package
         * @param package object $pkg
         * @return array $list
         */
        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Config\Config::getListByPackage();
            Concrete\Core\Config\Config::getListByPackage($pkg);
        }

        public static function getOrDefine($key, $defaultValue)
        {
            // Concrete\Core\Config\Config::getOrDefine();
            Concrete\Core\Config\Config::getOrDefine($key, $defaultValue);
        }

        /**
         * Checks to see if the given key is defined or not
         * if it isn't then it is defined as the default value
         * @param string $key
         * @param string $defaultValue
         */
        public static function getAndDefine($key, $defaultValue)
        {
            // Concrete\Core\Config\Config::getAndDefine();
            Concrete\Core\Config\Config::getAndDefine($key, $defaultValue);
        }

        /**
         * Clears a gived config key
         * @param string $cfKey
         */
        public static function clear($cfKey)
        {
            // Concrete\Core\Config\Config::clear();
            Concrete\Core\Config\Config::clear($cfKey);
        }

        /**
         * Saves a given value to a key
         * @param string $cfkey
         * @param string $cfValue
         */
        public static function save($cfKey, $cfValue)
        {
            // Concrete\Core\Config\Config::save();
            Concrete\Core\Config\Config::save($cfKey, $cfValue);
        }

        public static function exportList($x)
        {
            // Concrete\Core\Config\Config::exportList();
            Concrete\Core\Config\Config::exportList($x);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class PageType extends \Concrete\Core\Page\Type\Type
    {

        public static function getPageTypeID()
        {
            // Concrete\Core\Page\Type\Type::getPageTypeID();
            Concrete\Core\Page\Type\Type::getPageTypeID();
        }

        public static function getPageTypeName()
        {
            // Concrete\Core\Page\Type\Type::getPageTypeName();
            Concrete\Core\Page\Type\Type::getPageTypeName();
        }

        public static function getPageTypeHandle()
        {
            // Concrete\Core\Page\Type\Type::getPageTypeHandle();
            Concrete\Core\Page\Type\Type::getPageTypeHandle();
        }

        public static function getPageTypePublishTargetTypeID()
        {
            // Concrete\Core\Page\Type\Type::getPageTypePublishTargetTypeID();
            Concrete\Core\Page\Type\Type::getPageTypePublishTargetTypeID();
        }

        public static function getPageTypePublishTargetObject()
        {
            // Concrete\Core\Page\Type\Type::getPageTypePublishTargetObject();
            Concrete\Core\Page\Type\Type::getPageTypePublishTargetObject();
        }

        public static function getPageTypeAllowedPageTemplates()
        {
            // Concrete\Core\Page\Type\Type::getPageTypeAllowedPageTemplates();
            Concrete\Core\Page\Type\Type::getPageTypeAllowedPageTemplates();
        }

        public static function getPageTypeDefaultPageTemplateID()
        {
            // Concrete\Core\Page\Type\Type::getPageTypeDefaultPageTemplateID();
            Concrete\Core\Page\Type\Type::getPageTypeDefaultPageTemplateID();
        }

        public static function getPageTypeDefaultPageTemplateObject()
        {
            // Concrete\Core\Page\Type\Type::getPageTypeDefaultPageTemplateObject();
            Concrete\Core\Page\Type\Type::getPageTypeDefaultPageTemplateObject();
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\Page\Type\Type::getPermissionObjectIdentifier();
            Concrete\Core\Page\Type\Type::getPermissionObjectIdentifier();
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\Page\Type\Type::getPermissionResponseClassName();
            Concrete\Core\Page\Type\Type::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\Page\Type\Type::getPermissionAssignmentClassName();
            Concrete\Core\Page\Type\Type::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\Page\Type\Type::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\Page\Type\Type::getPermissionObjectKeyCategoryHandle();
        }

        public static function isPageTypeInternal()
        {
            // Concrete\Core\Page\Type\Type::isPageTypeInternal();
            Concrete\Core\Page\Type\Type::isPageTypeInternal();
        }

        public static function doesPageTypeLaunchInComposer()
        {
            // Concrete\Core\Page\Type\Type::doesPageTypeLaunchInComposer();
            Concrete\Core\Page\Type\Type::doesPageTypeLaunchInComposer();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Page\Type\Type::getPackageID();
            Concrete\Core\Page\Type\Type::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Page\Type\Type::getPackageHandle();
            Concrete\Core\Page\Type\Type::getPackageHandle();
        }

        protected static function stripEmptyPageTypeComposerControls(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Page\Type\Type::stripEmptyPageTypeComposerControls();
            Concrete\Core\Page\Type\Type::stripEmptyPageTypeComposerControls($c);
        }

        public static function publish(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Page\Type\Type::publish();
            Concrete\Core\Page\Type\Type::publish($c);
        }

        public static function savePageTypeComposerForm(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Page\Type\Type::savePageTypeComposerForm();
            Concrete\Core\Page\Type\Type::savePageTypeComposerForm($c);
        }

        public static function getPageTypeSelectedPageTemplateObjects()
        {
            // Concrete\Core\Page\Type\Type::getPageTypeSelectedPageTemplateObjects();
            Concrete\Core\Page\Type\Type::getPageTypeSelectedPageTemplateObjects();
        }

        public static function getByDefaultsPage(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Page\Type\Type::getByDefaultsPage();
            Concrete\Core\Page\Type\Type::getByDefaultsPage($c);
        }

        public static function getPageTypePageTemplateDefaultPageObject(Concrete\Core\Page\Template $template)
        {
            // Concrete\Core\Page\Type\Type::getPageTypePageTemplateDefaultPageObject();
            Concrete\Core\Page\Type\Type::getPageTypePageTemplateDefaultPageObject($template);
        }

        public static function getPageTypePageTemplateObjects()
        {
            // Concrete\Core\Page\Type\Type::getPageTypePageTemplateObjects();
            Concrete\Core\Page\Type\Type::getPageTypePageTemplateObjects();
        }

        public static function importTargets($node)
        {
            // Concrete\Core\Page\Type\Type::importTargets();
            Concrete\Core\Page\Type\Type::importTargets($node);
        }

        public static function import($node)
        {
            // Concrete\Core\Page\Type\Type::import();
            Concrete\Core\Page\Type\Type::import($node);
        }

        public static function importContent($node)
        {
            // Concrete\Core\Page\Type\Type::importContent();
            Concrete\Core\Page\Type\Type::importContent($node);
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Page\Type\Type::exportList();
            Concrete\Core\Page\Type\Type::exportList($xml);
        }

        public static function rescanPageTypeComposerOutputControlObjects()
        {
            // Concrete\Core\Page\Type\Type::rescanPageTypeComposerOutputControlObjects();
            Concrete\Core\Page\Type\Type::rescanPageTypeComposerOutputControlObjects();
        }

        public static function add($data, $pkg = null)
        {
            // Concrete\Core\Page\Type\Type::add();
            Concrete\Core\Page\Type\Type::add($data, $pkg);
        }

        public static function update($data)
        {
            // Concrete\Core\Page\Type\Type::update();
            Concrete\Core\Page\Type\Type::update($data);
        }

        public static function getList($includeInternal = null)
        {
            // Concrete\Core\Page\Type\Type::getList();
            Concrete\Core\Page\Type\Type::getList($includeInternal);
        }

        public static function getListByPackage(Concrete\Core\Package\Package $pkg)
        {
            // Concrete\Core\Page\Type\Type::getListByPackage();
            Concrete\Core\Page\Type\Type::getListByPackage($pkg);
        }

        public static function getByID($ptID)
        {
            // Concrete\Core\Page\Type\Type::getByID();
            Concrete\Core\Page\Type\Type::getByID($ptID);
        }

        public static function getByHandle($ptHandle)
        {
            // Concrete\Core\Page\Type\Type::getByHandle();
            Concrete\Core\Page\Type\Type::getByHandle($ptHandle);
        }

        public static function delete()
        {
            // Concrete\Core\Page\Type\Type::delete();
            Concrete\Core\Page\Type\Type::delete();
        }

        public static function setConfiguredPageTypePublishTargetObject(Concrete\Core\Page\Type\PublishTarget\Configuration\Configuration $configuredTarget)
        {
            // Concrete\Core\Page\Type\Type::setConfiguredPageTypePublishTargetObject();
            Concrete\Core\Page\Type\Type::setConfiguredPageTypePublishTargetObject($configuredTarget);
        }

        public static function rescanFormLayoutSetDisplayOrder()
        {
            // Concrete\Core\Page\Type\Type::rescanFormLayoutSetDisplayOrder();
            Concrete\Core\Page\Type\Type::rescanFormLayoutSetDisplayOrder();
        }

        public static function addPageTypeComposerFormLayoutSet($ptComposerFormLayoutSetName)
        {
            // Concrete\Core\Page\Type\Type::addPageTypeComposerFormLayoutSet();
            Concrete\Core\Page\Type\Type::addPageTypeComposerFormLayoutSet($ptComposerFormLayoutSetName);
        }

        public static function validateCreateDraftRequest($pt)
        {
            // Concrete\Core\Page\Type\Type::validateCreateDraftRequest();
            Concrete\Core\Page\Type\Type::validateCreateDraftRequest($pt);
        }

        public static function createDraft(Concrete\Core\Page\Template $pt, $u = null)
        {
            // Concrete\Core\Page\Type\Type::createDraft();
            Concrete\Core\Page\Type\Type::createDraft($pt, $u);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class PageTemplate extends \Concrete\Core\Page\Template
    {

        public static function exportList($xml)
        {
            // Concrete\Core\Page\Template::exportList();
            Concrete\Core\Page\Template::exportList($xml);
        }

        public static function getPageTemplateID()
        {
            // Concrete\Core\Page\Template::getPageTemplateID();
            Concrete\Core\Page\Template::getPageTemplateID();
        }

        public static function getPageTemplateName()
        {
            // Concrete\Core\Page\Template::getPageTemplateName();
            Concrete\Core\Page\Template::getPageTemplateName();
        }

        public static function getPageTemplateHandle()
        {
            // Concrete\Core\Page\Template::getPageTemplateHandle();
            Concrete\Core\Page\Template::getPageTemplateHandle();
        }

        public static function isPageTemplateInternal()
        {
            // Concrete\Core\Page\Template::isPageTemplateInternal();
            Concrete\Core\Page\Template::isPageTemplateInternal();
        }

        public static function getPageTemplateIcon()
        {
            // Concrete\Core\Page\Template::getPageTemplateIcon();
            Concrete\Core\Page\Template::getPageTemplateIcon();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Page\Template::getPackageID();
            Concrete\Core\Page\Template::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Page\Template::getPackageHandle();
            Concrete\Core\Page\Template::getPackageHandle();
        }

        public static function getByHandle($pTemplateHandle)
        {
            // Concrete\Core\Page\Template::getByHandle();
            Concrete\Core\Page\Template::getByHandle($pTemplateHandle);
        }

        public static function setPropertiesFromArray($row)
        {
            // Concrete\Core\Page\Template::setPropertiesFromArray();
            Concrete\Core\Page\Template::setPropertiesFromArray($row);
        }

        public static function getByID($pTemplateID)
        {
            // Concrete\Core\Page\Template::getByID();
            Concrete\Core\Page\Template::getByID($pTemplateID);
        }

        public static function delete()
        {
            // Concrete\Core\Page\Template::delete();
            Concrete\Core\Page\Template::delete();
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Page\Template::getListByPackage();
            Concrete\Core\Page\Template::getListByPackage($pkg);
        }

        public static function getList($includeInternal = null)
        {
            // Concrete\Core\Page\Template::getList();
            Concrete\Core\Page\Template::getList($includeInternal);
        }

        public static function add($pTemplateHandle, $pTemplateName, $pTemplateIcon = "main.png", $pkg = null, $pTemplateIsInternal = null)
        {
            // Concrete\Core\Page\Template::add();
            Concrete\Core\Page\Template::add($pTemplateHandle, $pTemplateName, $pTemplateIcon, $pkg, $pTemplateIsInternal);
        }

        public static function update($pTemplateHandle, $pTemplateName, $pTemplateIcon = "main.png")
        {
            // Concrete\Core\Page\Template::update();
            Concrete\Core\Page\Template::update($pTemplateHandle, $pTemplateName, $pTemplateIcon);
        }

        public static function getIcons()
        {
            // Concrete\Core\Page\Template::getIcons();
            Concrete\Core\Page\Template::getIcons();
        }

        public static function getPageTemplateIconImage()
        {
            // Concrete\Core\Page\Template::getPageTemplateIconImage();
            Concrete\Core\Page\Template::getPageTemplateIconImage();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    /**
     *
     * A page's theme is a pointer to a directory containing templates, CSS files and optionally PHP includes, images and JavaScript files.
     * Themes inherit down the tree when a page is added, but can also be set at the site-wide level (thereby overriding any previous choices.)
     * @package Pages and Collections
     * @subpackages Themes
     */
    class PageTheme extends \Concrete\Core\Page\Theme\Theme
    {

        public static function registerAssets()
        {
            // Concrete\Core\Page\Theme\Theme::registerAssets();
            Concrete\Core\Page\Theme\Theme::registerAssets();
        }

        public static function getGlobalList()
        {
            // Concrete\Core\Page\Theme\Theme::getGlobalList();
            Concrete\Core\Page\Theme\Theme::getGlobalList();
        }

        public static function getLocalList()
        {
            // Concrete\Core\Page\Theme\Theme::getLocalList();
            Concrete\Core\Page\Theme\Theme::getLocalList();
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Page\Theme\Theme::getListByPackage();
            Concrete\Core\Page\Theme\Theme::getListByPackage($pkg);
        }

        public static function getList($where = null)
        {
            // Concrete\Core\Page\Theme\Theme::getList();
            Concrete\Core\Page\Theme\Theme::getList($where);
        }

        public static function getInstalledHandles()
        {
            // Concrete\Core\Page\Theme\Theme::getInstalledHandles();
            Concrete\Core\Page\Theme\Theme::getInstalledHandles();
        }

        public static function supportsGridFramework()
        {
            // Concrete\Core\Page\Theme\Theme::supportsGridFramework();
            Concrete\Core\Page\Theme\Theme::supportsGridFramework();
        }

        public static function getThemeGridFrameworkObject()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeGridFrameworkObject();
            Concrete\Core\Page\Theme\Theme::getThemeGridFrameworkObject();
        }

        public static function providesAsset($assetType, $assetHandle)
        {
            // Concrete\Core\Page\Theme\Theme::providesAsset();
            Concrete\Core\Page\Theme\Theme::providesAsset($assetType, $assetHandle);
        }

        public static function getAvailableThemes($filterInstalled = "1")
        {
            // Concrete\Core\Page\Theme\Theme::getAvailableThemes();
            Concrete\Core\Page\Theme\Theme::getAvailableThemes($filterInstalled);
        }

        public static function getByFileHandle($handle, $dir = "/git/concrete5-5.7.0/web/application/themes")
        {
            // Concrete\Core\Page\Theme\Theme::getByFileHandle();
            Concrete\Core\Page\Theme\Theme::getByFileHandle($handle, $dir);
        }

        /**
         * Looks into the current theme and outputs the contents of the stylesheet.
         * This function will eventually check to see if a cached version is available, as well as tie the dynamic areas of the stylesheet to whatever they have been saved.
         * @param string $file
         */
        public static function outputStyleSheet($file, $styles = null)
        {
            // Concrete\Core\Page\Theme\Theme::outputStyleSheet();
            Concrete\Core\Page\Theme\Theme::outputStyleSheet($file, $styles);
        }

        public static function parseStyleSheet($file, $styles = null)
        {
            // Concrete\Core\Page\Theme\Theme::parseStyleSheet();
            Concrete\Core\Page\Theme\Theme::parseStyleSheet($file, $styles);
        }

        public static function mergeStylesFromPost($post)
        {
            // Concrete\Core\Page\Theme\Theme::mergeStylesFromPost();
            Concrete\Core\Page\Theme\Theme::mergeStylesFromPost($post);
        }

        /**
         * Removes any custom styles by clearing them out of the database
         * @return void
         */
        public static function reset()
        {
            // Concrete\Core\Page\Theme\Theme::reset();
            Concrete\Core\Page\Theme\Theme::reset();
        }

        /**
         * Takes an associative array of pagethemeeditablestyle objects and saves it to the PageThemeStyles table
         * @param array $styles
         */
        public static function saveEditableStyles($styles)
        {
            // Concrete\Core\Page\Theme\Theme::saveEditableStyles();
            Concrete\Core\Page\Theme\Theme::saveEditableStyles($styles);
        }

        public static function getStyleSheets()
        {
            // Concrete\Core\Page\Theme\Theme::getStyleSheets();
            Concrete\Core\Page\Theme\Theme::getStyleSheets();
        }

        public static function isThemeCustomizable()
        {
            // Concrete\Core\Page\Theme\Theme::isThemeCustomizable();
            Concrete\Core\Page\Theme\Theme::isThemeCustomizable();
        }

        /**
         * Retrieves an array of editable style objects from the current them. This is accomplished by locating all style sheets in the root of the theme, parsing all their contents
         * @param string $file
         * @return array
         */
        public static function getEditableStylesList($mergeStyles = null)
        {
            // Concrete\Core\Page\Theme\Theme::getEditableStylesList();
            Concrete\Core\Page\Theme\Theme::getEditableStylesList($mergeStyles);
        }

        /**
         * @param string $pThemeHandle
         * @return PageTheme
         */
        public static function getByHandle($pThemeHandle)
        {
            // Concrete\Core\Page\Theme\Theme::getByHandle();
            Concrete\Core\Page\Theme\Theme::getByHandle($pThemeHandle);
        }

        /**
         * @param int $ptID
         * @return PageTheme
         */
        public static function getByID($pThemeID)
        {
            // Concrete\Core\Page\Theme\Theme::getByID();
            Concrete\Core\Page\Theme\Theme::getByID($pThemeID);
        }

        protected static function populateThemeQuery($where, $args)
        {
            // Concrete\Core\Page\Theme\Theme::populateThemeQuery();
            Concrete\Core\Page\Theme\Theme::populateThemeQuery($where, $args);
        }

        public static function add($pThemeHandle, $pkg = null)
        {
            // Concrete\Core\Page\Theme\Theme::add();
            Concrete\Core\Page\Theme\Theme::add($pThemeHandle, $pkg);
        }

        public static function getFilesInTheme()
        {
            // Concrete\Core\Page\Theme\Theme::getFilesInTheme();
            Concrete\Core\Page\Theme\Theme::getFilesInTheme();
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Page\Theme\Theme::exportList();
            Concrete\Core\Page\Theme\Theme::exportList($xml);
        }

        protected static function install($dir, $pThemeHandle, $pkgID)
        {
            // Concrete\Core\Page\Theme\Theme::install();
            Concrete\Core\Page\Theme\Theme::install($dir, $pThemeHandle, $pkgID);
        }

        public static function updateThemeCustomClass()
        {
            // Concrete\Core\Page\Theme\Theme::updateThemeCustomClass();
            Concrete\Core\Page\Theme\Theme::updateThemeCustomClass();
        }

        public static function getThemeID()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeID();
            Concrete\Core\Page\Theme\Theme::getThemeID();
        }

        public static function getThemeName()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeName();
            Concrete\Core\Page\Theme\Theme::getThemeName();
        }

        /** Returns the display name for this theme (localized and escaped accordingly to $format)
         * @param string $format = 'html'
         *	Escape the result in html format (if $format is 'html').
         *	If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getThemeDisplayName($format = "html")
        {
            // Concrete\Core\Page\Theme\Theme::getThemeDisplayName();
            Concrete\Core\Page\Theme\Theme::getThemeDisplayName($format);
        }

        public static function getPackageID()
        {
            // Concrete\Core\Page\Theme\Theme::getPackageID();
            Concrete\Core\Page\Theme\Theme::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Page\Theme\Theme::getPackageHandle();
            Concrete\Core\Page\Theme\Theme::getPackageHandle();
        }

        /**
         * Returns whether a theme has a custom class.
         */
        public static function hasCustomClass()
        {
            // Concrete\Core\Page\Theme\Theme::hasCustomClass();
            Concrete\Core\Page\Theme\Theme::hasCustomClass();
        }

        public static function getThemeHandle()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeHandle();
            Concrete\Core\Page\Theme\Theme::getThemeHandle();
        }

        public static function getThemeDescription()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeDescription();
            Concrete\Core\Page\Theme\Theme::getThemeDescription();
        }

        public static function getThemeDisplayDescription($format = "html")
        {
            // Concrete\Core\Page\Theme\Theme::getThemeDisplayDescription();
            Concrete\Core\Page\Theme\Theme::getThemeDisplayDescription($format);
        }

        public static function getThemeDirectory()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeDirectory();
            Concrete\Core\Page\Theme\Theme::getThemeDirectory();
        }

        public static function getThemeURL()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeURL();
            Concrete\Core\Page\Theme\Theme::getThemeURL();
        }

        public static function getThemeEditorCSS()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeEditorCSS();
            Concrete\Core\Page\Theme\Theme::getThemeEditorCSS();
        }

        public static function isUninstallable()
        {
            // Concrete\Core\Page\Theme\Theme::isUninstallable();
            Concrete\Core\Page\Theme\Theme::isUninstallable();
        }

        public static function getThemeThumbnail()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeThumbnail();
            Concrete\Core\Page\Theme\Theme::getThemeThumbnail();
        }

        public static function applyToSite()
        {
            // Concrete\Core\Page\Theme\Theme::applyToSite();
            Concrete\Core\Page\Theme\Theme::applyToSite();
        }

        public static function getSiteTheme()
        {
            // Concrete\Core\Page\Theme\Theme::getSiteTheme();
            Concrete\Core\Page\Theme\Theme::getSiteTheme();
        }

        public static function uninstall()
        {
            // Concrete\Core\Page\Theme\Theme::uninstall();
            Concrete\Core\Page\Theme\Theme::uninstall();
        }

        public static function getThemeGatheringGridItemMargin()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeGatheringGridItemMargin();
            Concrete\Core\Page\Theme\Theme::getThemeGatheringGridItemMargin();
        }

        public static function getThemeGatheringGridItemWidth()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeGatheringGridItemWidth();
            Concrete\Core\Page\Theme\Theme::getThemeGatheringGridItemWidth();
        }

        public static function getThemeGatheringGridItemHeight()
        {
            // Concrete\Core\Page\Theme\Theme::getThemeGatheringGridItemHeight();
            Concrete\Core\Page\Theme\Theme::getThemeGatheringGridItemHeight();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    /**
     *
     * An object that allows a filtered list of pages to be returned.
     * @package Pages
     *
     */
    class PageList extends \Concrete\Core\Page\PageList
    {

        public static function __call($nm, $a)
        {
            // Concrete\Core\Page\PageList::__call();
            Concrete\Core\Page\PageList::__call($nm, $a);
        }

        public static function setViewPagePermissionKeyHandle($pkHandle)
        {
            // Concrete\Core\Page\PageList::setViewPagePermissionKeyHandle();
            Concrete\Core\Page\PageList::setViewPagePermissionKeyHandle($pkHandle);
        }

        public static function includeInactivePages()
        {
            // Concrete\Core\Page\PageList::includeInactivePages();
            Concrete\Core\Page\PageList::includeInactivePages();
        }

        public static function ignorePermissions()
        {
            // Concrete\Core\Page\PageList::ignorePermissions();
            Concrete\Core\Page\PageList::ignorePermissions();
        }

        public static function ignoreAliases()
        {
            // Concrete\Core\Page\PageList::ignoreAliases();
            Concrete\Core\Page\PageList::ignoreAliases();
        }

        public static function includeSystemPages()
        {
            // Concrete\Core\Page\PageList::includeSystemPages();
            Concrete\Core\Page\PageList::includeSystemPages();
        }

        public static function displayUnapprovedPages()
        {
            // Concrete\Core\Page\PageList::displayUnapprovedPages();
            Concrete\Core\Page\PageList::displayUnapprovedPages();
        }

        public static function isIndexedSearch()
        {
            // Concrete\Core\Page\PageList::isIndexedSearch();
            Concrete\Core\Page\PageList::isIndexedSearch();
        }

        /**
         * Filters by "keywords" (which searches everything including filenames, title, tags, users who uploaded the file, tags)
         */
        public static function filterByKeywords($keywords, $simple = null)
        {
            // Concrete\Core\Page\PageList::filterByKeywords();
            Concrete\Core\Page\PageList::filterByKeywords($keywords, $simple);
        }

        public static function filterByName($name, $exact = null)
        {
            // Concrete\Core\Page\PageList::filterByName();
            Concrete\Core\Page\PageList::filterByName($name, $exact);
        }

        public static function filterByPath($path, $includeAllChildren = "1")
        {
            // Concrete\Core\Page\PageList::filterByPath();
            Concrete\Core\Page\PageList::filterByPath($path, $includeAllChildren);
        }

        /**
         * Sets up a list to only return items the proper user can access
         */
        public static function setupPermissions()
        {
            // Concrete\Core\Page\PageList::setupPermissions();
            Concrete\Core\Page\PageList::setupPermissions();
        }

        public static function sortByRelevance()
        {
            // Concrete\Core\Page\PageList::sortByRelevance();
            Concrete\Core\Page\PageList::sortByRelevance();
        }

        /**
         * Sorts this list by display order
         */
        public static function sortByDisplayOrder()
        {
            // Concrete\Core\Page\PageList::sortByDisplayOrder();
            Concrete\Core\Page\PageList::sortByDisplayOrder();
        }

        /**
         * Sorts this list by display order descending
         */
        public static function sortByDisplayOrderDescending()
        {
            // Concrete\Core\Page\PageList::sortByDisplayOrderDescending();
            Concrete\Core\Page\PageList::sortByDisplayOrderDescending();
        }

        public static function sortByCollectionIDAscending()
        {
            // Concrete\Core\Page\PageList::sortByCollectionIDAscending();
            Concrete\Core\Page\PageList::sortByCollectionIDAscending();
        }

        /**
         * Sorts this list by public date ascending order
         */
        public static function sortByPublicDate()
        {
            // Concrete\Core\Page\PageList::sortByPublicDate();
            Concrete\Core\Page\PageList::sortByPublicDate();
        }

        /**
         * Sorts this list by name
         */
        public static function sortByName()
        {
            // Concrete\Core\Page\PageList::sortByName();
            Concrete\Core\Page\PageList::sortByName();
        }

        /**
         * Sorts this list by name descending order
         */
        public static function sortByNameDescending()
        {
            // Concrete\Core\Page\PageList::sortByNameDescending();
            Concrete\Core\Page\PageList::sortByNameDescending();
        }

        /**
         * Sorts this list by public date descending order
         */
        public static function sortByPublicDateDescending()
        {
            // Concrete\Core\Page\PageList::sortByPublicDateDescending();
            Concrete\Core\Page\PageList::sortByPublicDateDescending();
        }

        /**
         * Sets the parent ID that we will grab pages from.
         * @param mixed $cParentID
         */
        public static function filterByParentID($cParentID)
        {
            // Concrete\Core\Page\PageList::filterByParentID();
            Concrete\Core\Page\PageList::filterByParentID($cParentID);
        }

        /**
         * Filters by type of collection (using the ID field)
         * @param mixed $ptID
         */
        public static function filterByPageTypeID($ptID)
        {
            // Concrete\Core\Page\PageList::filterByPageTypeID();
            Concrete\Core\Page\PageList::filterByPageTypeID($ptID);
        }

        /**
         * @deprecated
         */
        public static function filterByCollectionTypeID($ctID)
        {
            // Concrete\Core\Page\PageList::filterByCollectionTypeID();
            Concrete\Core\Page\PageList::filterByCollectionTypeID($ctID);
        }

        /**
         * Filters by user ID of collection (using the uID field)
         * @param mixed $uID
         */
        public static function filterByUserID($uID)
        {
            // Concrete\Core\Page\PageList::filterByUserID();
            Concrete\Core\Page\PageList::filterByUserID($uID);
        }

        public static function filterByIsApproved($cvIsApproved)
        {
            // Concrete\Core\Page\PageList::filterByIsApproved();
            Concrete\Core\Page\PageList::filterByIsApproved($cvIsApproved);
        }

        public static function filterByIsAlias($ia)
        {
            // Concrete\Core\Page\PageList::filterByIsAlias();
            Concrete\Core\Page\PageList::filterByIsAlias($ia);
        }

        /**
         * Filters by type of collection (using the handle field)
         * @param mixed $ptHandle
         */
        public static function filterByPageTypeHandle($ptHandle)
        {
            // Concrete\Core\Page\PageList::filterByPageTypeHandle();
            Concrete\Core\Page\PageList::filterByPageTypeHandle($ptHandle);
        }

        public static function filterByCollectionTypeHandle($ctHandle)
        {
            // Concrete\Core\Page\PageList::filterByCollectionTypeHandle();
            Concrete\Core\Page\PageList::filterByCollectionTypeHandle($ctHandle);
        }

        /**
         * Filters by date added
         * @param string $date
         */
        public static function filterByDateAdded($date, $comparison = "=")
        {
            // Concrete\Core\Page\PageList::filterByDateAdded();
            Concrete\Core\Page\PageList::filterByDateAdded($date, $comparison);
        }

        public static function filterByNumberOfChildren($num, $comparison = ">")
        {
            // Concrete\Core\Page\PageList::filterByNumberOfChildren();
            Concrete\Core\Page\PageList::filterByNumberOfChildren($num, $comparison);
        }

        public static function filterByDateLastModified($date, $comparison = "=")
        {
            // Concrete\Core\Page\PageList::filterByDateLastModified();
            Concrete\Core\Page\PageList::filterByDateLastModified($date, $comparison);
        }

        /**
         * Filters by public date
         * @param string $date
         */
        public static function filterByPublicDate($date, $comparison = "=")
        {
            // Concrete\Core\Page\PageList::filterByPublicDate();
            Concrete\Core\Page\PageList::filterByPublicDate($date, $comparison);
        }

        public static function filterBySelectAttribute($akHandle, $value)
        {
            // Concrete\Core\Page\PageList::filterBySelectAttribute();
            Concrete\Core\Page\PageList::filterBySelectAttribute($akHandle, $value);
        }

        /**
         * If true, pages will be checked for permissions prior to being returned
         * @param bool $checkForPermissions
         */
        public static function displayOnlyPermittedPages($checkForPermissions)
        {
            // Concrete\Core\Page\PageList::displayOnlyPermittedPages();
            Concrete\Core\Page\PageList::displayOnlyPermittedPages($checkForPermissions);
        }

        protected static function setBaseQuery($additionalFields = null)
        {
            // Concrete\Core\Page\PageList::setBaseQuery();
            Concrete\Core\Page\PageList::setBaseQuery($additionalFields);
        }

        protected static function setupSystemPagesToExclude()
        {
            // Concrete\Core\Page\PageList::setupSystemPagesToExclude();
            Concrete\Core\Page\PageList::setupSystemPagesToExclude();
        }

        protected static function loadPageID($cID, $versionOrig = "RECENT")
        {
            // Concrete\Core\Page\PageList::loadPageID();
            Concrete\Core\Page\PageList::loadPageID($cID, $versionOrig);
        }

        public static function getTotal()
        {
            // Concrete\Core\Page\PageList::getTotal();
            Concrete\Core\Page\PageList::getTotal();
        }

        /**
         * Returns an array of page objects based on current settings
         */
        public static function get($itemsToGet = null, $offset = null)
        {
            // Concrete\Core\Page\PageList::get();
            Concrete\Core\Page\PageList::get($itemsToGet, $offset);
        }

        public static function debug($dbg = "1")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug($dbg);
        }

        protected static function setQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery($query);
        }

        protected static function getQuery()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
        }

        public static function addToQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery($query);
        }

        protected static function setupAutoSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
        }

        protected static function executeBase()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
        }

        protected static function setupSortByString()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
        }

        protected static function setupAttributeSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
        }

        /**
         * Adds a filter to this item list
         */
        public static function filter($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter($column, $value, $comparison);
        }

        public static function getSearchResultsClass($field)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass($field);
        }

        public static function sortBy($key, $dir = "asc")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy($key, $dir);
        }

        public static function groupBy($key)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy($key);
        }

        public static function having($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having($column, $value, $comparison);
        }

        public static function getSortByURL($column, $dir = "asc", $baseURL = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL($column, $dir, $baseURL, $additionalVars);
        }

        protected static function setupAttributeFilters($join)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters($join);
        }

        public static function filterByAttribute($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute($column, $value, $comparison);
        }

        public static function enableStickySearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest($namespace);
        }

        public static function getQueryStringPagingVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
        }

        public static function getQueryStringSortVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
        }

        public static function getQueryStringSortDirectionVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
        }

        protected static function getStickySearchNameSpace()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
        }

        public static function resetSearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest($namespace);
        }

        public static function addToSearchRequest($key, $value)
        {
            // Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest($key, $value);
        }

        public static function getSearchRequest()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
        }

        public static function setItemsPerPage($num)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage($num);
        }

        public static function getItemsPerPage()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
        }

        public static function setItems($items)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItems();
            Concrete\Core\Foundation\Collection\ItemList::setItems($items);
        }

        public static function setNameSpace($ns)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::setNameSpace($ns);
        }

        /**
         * Returns an array of object by "page"
         */
        public static function getPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPage();
            Concrete\Core\Foundation\Collection\ItemList::getPage($page);
        }

        protected static function setCurrentPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setCurrentPage();
            Concrete\Core\Foundation\Collection\ItemList::setCurrentPage($page);
        }

        /**
         * Displays summary text about a list
         */
        public static function displaySummary($right_content = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displaySummary();
            Concrete\Core\Foundation\Collection\ItemList::displaySummary($right_content);
        }

        public static function isActiveSortColumn($column)
        {
            // Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn($column);
        }

        public static function getActiveSortColumn()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
        }

        public static function getActiveSortDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
        }

        public static function requiresPaging()
        {
            // Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
            Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
        }

        public static function getPagination($url = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPagination();
            Concrete\Core\Foundation\Collection\ItemList::getPagination($url, $additionalVars);
        }

        /**
         * Gets paging that works in our new format */
        public static function displayPagingV2($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPagingV2();
            Concrete\Core\Foundation\Collection\ItemList::displayPagingV2($script, $return, $additionalVars);
        }

        /**
         * Gets standard HTML to display paging */
        public static function displayPaging($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPaging();
            Concrete\Core\Foundation\Collection\ItemList::displayPaging($script, $return, $additionalVars);
        }

        /**
         * Returns an object with properties useful for paging
         */
        public static function getSummary()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSummary();
            Concrete\Core\Foundation\Collection\ItemList::getSummary();
        }

        public static function getSortBy()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortBy();
            Concrete\Core\Foundation\Collection\ItemList::getSortBy();
        }

        public static function getSortByDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
            Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
        }

        /**
         * Sets up a multiple columns to search by. Each argument is taken "as-is" (including asc or desc) and concatenated with commas
         * Note that this is overrides any previous sortByMultiple() call, and all sortBy() calls. Alternatively, you can pass a single
         * array with multiple columns to sort by as its values.
         * e.g. $list->sortByMultiple('columna desc', 'columnb asc');
         * or $list->sortByMultiple(array('columna desc', 'columnb asc'));
         */
        public static function sortByMultiple()
        {
            // Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
            Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
        }

    }

    class PageCache extends \Concrete\Core\Cache\Page\PageCache
    {

        public static function deliver(Concrete\Core\Cache\Page\PageCacheRecord $record)
        {
            // Concrete\Core\Cache\Page\PageCache::deliver();
            Concrete\Core\Cache\Page\PageCache::deliver($record);
        }

        public static function getLibrary()
        {
            // Concrete\Core\Cache\Page\PageCache::getLibrary();
            Concrete\Core\Cache\Page\PageCache::getLibrary();
        }

        /**
         * Note: can't use the User object directly because it might query the database
         */
        public static function shouldCheckCache(Concrete\Core\Http\Request $req)
        {
            // Concrete\Core\Cache\Page\PageCache::shouldCheckCache();
            Concrete\Core\Cache\Page\PageCache::shouldCheckCache($req);
        }

        public static function outputCacheHeaders(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Cache\Page\PageCache::outputCacheHeaders();
            Concrete\Core\Cache\Page\PageCache::outputCacheHeaders($c);
        }

        public static function getCacheHeaders(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Cache\Page\PageCache::getCacheHeaders();
            Concrete\Core\Cache\Page\PageCache::getCacheHeaders($c);
        }

        public static function shouldAddToCache(Concrete\Core\Page\View\PageView $v)
        {
            // Concrete\Core\Cache\Page\PageCache::shouldAddToCache();
            Concrete\Core\Cache\Page\PageCache::shouldAddToCache($v);
        }

        public static function getCacheKey($mixed)
        {
            // Concrete\Core\Cache\Page\PageCache::getCacheKey();
            Concrete\Core\Cache\Page\PageCache::getCacheKey($mixed);
        }

    }

    class Conversation extends \Concrete\Core\Conversation\Conversation
    {

        public static function getConversationID()
        {
            // Concrete\Core\Conversation\Conversation::getConversationID();
            Concrete\Core\Conversation\Conversation::getConversationID();
        }

        public static function getConversationParentMessageID()
        {
            // Concrete\Core\Conversation\Conversation::getConversationParentMessageID();
            Concrete\Core\Conversation\Conversation::getConversationParentMessageID();
        }

        public static function getConversationDateCreated()
        {
            // Concrete\Core\Conversation\Conversation::getConversationDateCreated();
            Concrete\Core\Conversation\Conversation::getConversationDateCreated();
        }

        public static function getConversationDateLastMessage()
        {
            // Concrete\Core\Conversation\Conversation::getConversationDateLastMessage();
            Concrete\Core\Conversation\Conversation::getConversationDateLastMessage();
        }

        public static function getConversationMessagesTotal()
        {
            // Concrete\Core\Conversation\Conversation::getConversationMessagesTotal();
            Concrete\Core\Conversation\Conversation::getConversationMessagesTotal();
        }

        public static function getByID($cnvID)
        {
            // Concrete\Core\Conversation\Conversation::getByID();
            Concrete\Core\Conversation\Conversation::getByID($cnvID);
        }

        public static function getConversationPageObject()
        {
            // Concrete\Core\Conversation\Conversation::getConversationPageObject();
            Concrete\Core\Conversation\Conversation::getConversationPageObject();
        }

        public static function setConversationPageObject($c)
        {
            // Concrete\Core\Conversation\Conversation::setConversationPageObject();
            Concrete\Core\Conversation\Conversation::setConversationPageObject($c);
        }

        public static function updateConversationSummary()
        {
            // Concrete\Core\Conversation\Conversation::updateConversationSummary();
            Concrete\Core\Conversation\Conversation::updateConversationSummary();
        }

        public static function getConversationMessageUsers()
        {
            // Concrete\Core\Conversation\Conversation::getConversationMessageUsers();
            Concrete\Core\Conversation\Conversation::getConversationMessageUsers();
        }

        public static function setConversationParentMessageID($cnvParentMessageID)
        {
            // Concrete\Core\Conversation\Conversation::setConversationParentMessageID();
            Concrete\Core\Conversation\Conversation::setConversationParentMessageID($cnvParentMessageID);
        }

        public static function add()
        {
            // Concrete\Core\Conversation\Conversation::add();
            Concrete\Core\Conversation\Conversation::add();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class ConversationFlagType extends \Concrete\Core\Conversation\FlagType\FlagType
    {

        public static function getConversationFlagTypeHandle()
        {
            // Concrete\Core\Conversation\FlagType\FlagType::getConversationFlagTypeHandle();
            Concrete\Core\Conversation\FlagType\FlagType::getConversationFlagTypeHandle();
        }

        public static function getConversationFlagTypeID()
        {
            // Concrete\Core\Conversation\FlagType\FlagType::getConversationFlagTypeID();
            Concrete\Core\Conversation\FlagType\FlagType::getConversationFlagTypeID();
        }

        public static function __construct($id = null, $handle = null)
        {
            // Concrete\Core\Conversation\FlagType\FlagType::__construct();
            Concrete\Core\Conversation\FlagType\FlagType::__construct($id, $handle);
        }

        public static function init($id, $handle)
        {
            // Concrete\Core\Conversation\FlagType\FlagType::init();
            Concrete\Core\Conversation\FlagType\FlagType::init($id, $handle);
        }

        public static function delete()
        {
            // Concrete\Core\Conversation\FlagType\FlagType::delete();
            Concrete\Core\Conversation\FlagType\FlagType::delete();
        }

        public static function getByID($id)
        {
            // Concrete\Core\Conversation\FlagType\FlagType::getByID();
            Concrete\Core\Conversation\FlagType\FlagType::getByID($id);
        }

        public static function getByhandle($handle)
        {
            // Concrete\Core\Conversation\FlagType\FlagType::getByhandle();
            Concrete\Core\Conversation\FlagType\FlagType::getByhandle($handle);
        }

        public static function add($handle)
        {
            // Concrete\Core\Conversation\FlagType\FlagType::add();
            Concrete\Core\Conversation\FlagType\FlagType::add($handle);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class Queue extends \Concrete\Core\Foundation\Queue
    {

        public static function get($name, $additionalConfig = null)
        {
            // Concrete\Core\Foundation\Queue::get();
            Concrete\Core\Foundation\Queue::get($name, $additionalConfig);
        }

        public static function exists($name)
        {
            // Concrete\Core\Foundation\Queue::exists();
            Concrete\Core\Foundation\Queue::exists($name);
        }

    }

    class Block extends \Concrete\Core\Block\Block
    {

        public static function populateManually($blockInfo, $c, $a)
        {
            // Concrete\Core\Block\Block::populateManually();
            Concrete\Core\Block\Block::populateManually($blockInfo, $c, $a);
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\Block\Block::getPermissionObjectIdentifier();
            Concrete\Core\Block\Block::getPermissionObjectIdentifier();
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\Block\Block::getPermissionResponseClassName();
            Concrete\Core\Block\Block::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\Block\Block::getPermissionAssignmentClassName();
            Concrete\Core\Block\Block::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\Block\Block::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\Block\Block::getPermissionObjectKeyCategoryHandle();
        }

        public static function getByID($bID, $c = null, $a = null)
        {
            // Concrete\Core\Block\Block::getByID();
            Concrete\Core\Block\Block::getByID($bID, $c, $a);
        }

        /**
         * Returns a global block
         */
        public static function getByName($globalBlockName)
        {
            // Concrete\Core\Block\Block::getByName();
            Concrete\Core\Block\Block::getByName($globalBlockName);
        }

        public static function setProxyBlock($block)
        {
            // Concrete\Core\Block\Block::setProxyBlock();
            Concrete\Core\Block\Block::setProxyBlock($block);
        }

        public static function getProxyBlock()
        {
            // Concrete\Core\Block\Block::getProxyBlock();
            Concrete\Core\Block\Block::getProxyBlock();
        }

        public static function display($view = "view")
        {
            // Concrete\Core\Block\Block::display();
            Concrete\Core\Block\Block::display($view);
        }

        public static function isAlias($c = null)
        {
            // Concrete\Core\Block\Block::isAlias();
            Concrete\Core\Block\Block::isAlias($c);
        }

        public static function isAliasOfMasterCollection()
        {
            // Concrete\Core\Block\Block::isAliasOfMasterCollection();
            Concrete\Core\Block\Block::isAliasOfMasterCollection();
        }

        public static function isGlobal()
        {
            // Concrete\Core\Block\Block::isGlobal();
            Concrete\Core\Block\Block::isGlobal();
        }

        public static function isBlockInStack()
        {
            // Concrete\Core\Block\Block::isBlockInStack();
            Concrete\Core\Block\Block::isBlockInStack();
        }

        public static function getBlockCachedRecord()
        {
            // Concrete\Core\Block\Block::getBlockCachedRecord();
            Concrete\Core\Block\Block::getBlockCachedRecord();
        }

        public static function getBlockCachedOutput($area)
        {
            // Concrete\Core\Block\Block::getBlockCachedOutput();
            Concrete\Core\Block\Block::getBlockCachedOutput($area);
        }

        public static function setBlockCachedOutput($content, $lifetime, $area)
        {
            // Concrete\Core\Block\Block::setBlockCachedOutput();
            Concrete\Core\Block\Block::setBlockCachedOutput($content, $lifetime, $area);
        }

        public static function inc($file)
        {
            // Concrete\Core\Block\Block::inc();
            Concrete\Core\Block\Block::inc($file);
        }

        public static function revertToAreaPermissions()
        {
            // Concrete\Core\Block\Block::revertToAreaPermissions();
            Concrete\Core\Block\Block::revertToAreaPermissions();
        }

        public static function getBlockPath()
        {
            // Concrete\Core\Block\Block::getBlockPath();
            Concrete\Core\Block\Block::getBlockPath();
        }

        public static function loadNewCollection(&$c)
        {
            // Concrete\Core\Block\Block::loadNewCollection();
            Concrete\Core\Block\Block::loadNewCollection($c);
        }

        public static function setBlockAreaObject(&$a)
        {
            // Concrete\Core\Block\Block::setBlockAreaObject();
            Concrete\Core\Block\Block::setBlockAreaObject($a);
        }

        public static function getBlockAreaObject()
        {
            // Concrete\Core\Block\Block::getBlockAreaObject();
            Concrete\Core\Block\Block::getBlockAreaObject();
        }

        public static function disableBlockVersioning()
        {
            // Concrete\Core\Block\Block::disableBlockVersioning();
            Concrete\Core\Block\Block::disableBlockVersioning();
        }

        public static function getOriginalCollection()
        {
            // Concrete\Core\Block\Block::getOriginalCollection();
            Concrete\Core\Block\Block::getOriginalCollection();
        }

        public static function getNumChildren()
        {
            // Concrete\Core\Block\Block::getNumChildren();
            Concrete\Core\Block\Block::getNumChildren();
        }

        public static function getInstance()
        {
            // Concrete\Core\Block\Block::getInstance();
            Concrete\Core\Block\Block::getInstance();
        }

        public static function getController()
        {
            // Concrete\Core\Block\Block::getController();
            Concrete\Core\Block\Block::getController();
        }

        public static function getCollectionList()
        {
            // Concrete\Core\Block\Block::getCollectionList();
            Concrete\Core\Block\Block::getCollectionList();
        }

        public static function update($data)
        {
            // Concrete\Core\Block\Block::update();
            Concrete\Core\Block\Block::update($data);
        }

        public static function isActive()
        {
            // Concrete\Core\Block\Block::isActive();
            Concrete\Core\Block\Block::isActive();
        }

        public static function deactivate()
        {
            // Concrete\Core\Block\Block::deactivate();
            Concrete\Core\Block\Block::deactivate();
        }

        public static function activate()
        {
            // Concrete\Core\Block\Block::activate();
            Concrete\Core\Block\Block::activate();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Block\Block::getPackageID();
            Concrete\Core\Block\Block::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Block\Block::getPackageHandle();
            Concrete\Core\Block\Block::getPackageHandle();
        }

        public static function updateBlockName($name, $force = null)
        {
            // Concrete\Core\Block\Block::updateBlockName();
            Concrete\Core\Block\Block::updateBlockName($name, $force);
        }

        public static function alias($c)
        {
            // Concrete\Core\Block\Block::alias();
            Concrete\Core\Block\Block::alias($c);
        }

        /**
         * Moves a block onto a new page and into a new area. Does not change any data about the block otherwise
         */
        public static function move($nc, $area)
        {
            // Concrete\Core\Block\Block::move();
            Concrete\Core\Block\Block::move($nc, $area);
        }

        public static function duplicate($nc, $isCopyFromMasterCollectionPropagation = null)
        {
            // Concrete\Core\Block\Block::duplicate();
            Concrete\Core\Block\Block::duplicate($nc, $isCopyFromMasterCollectionPropagation);
        }

        public static function getBlockCustomStyleRule()
        {
            // Concrete\Core\Block\Block::getBlockCustomStyleRule();
            Concrete\Core\Block\Block::getBlockCustomStyleRule();
        }

        public static function getBlockCustomStyleRuleID()
        {
            // Concrete\Core\Block\Block::getBlockCustomStyleRuleID();
            Concrete\Core\Block\Block::getBlockCustomStyleRuleID();
        }

        public static function resetBlockCustomStyle($updateAll = null)
        {
            // Concrete\Core\Block\Block::resetBlockCustomStyle();
            Concrete\Core\Block\Block::resetBlockCustomStyle($updateAll);
        }

        public static function __destruct()
        {
            // Concrete\Core\Block\Block::__destruct();
            Concrete\Core\Block\Block::__destruct();
        }

        public static function setBlockCustomStyle($csr, $updateAll = null)
        {
            // Concrete\Core\Block\Block::setBlockCustomStyle();
            Concrete\Core\Block\Block::setBlockCustomStyle($csr, $updateAll);
        }

        public static function getBlockCollectionObject()
        {
            // Concrete\Core\Block\Block::getBlockCollectionObject();
            Concrete\Core\Block\Block::getBlockCollectionObject();
        }

        public static function setBlockCollectionObject($c)
        {
            // Concrete\Core\Block\Block::setBlockCollectionObject();
            Concrete\Core\Block\Block::setBlockCollectionObject($c);
        }

        public static function getBlockCollectionID()
        {
            // Concrete\Core\Block\Block::getBlockCollectionID();
            Concrete\Core\Block\Block::getBlockCollectionID();
        }

        public static function getBlockTypeName()
        {
            // Concrete\Core\Block\Block::getBlockTypeName();
            Concrete\Core\Block\Block::getBlockTypeName();
        }

        public static function getBlockTypeHandle()
        {
            // Concrete\Core\Block\Block::getBlockTypeHandle();
            Concrete\Core\Block\Block::getBlockTypeHandle();
        }

        public static function getBlockFilename()
        {
            // Concrete\Core\Block\Block::getBlockFilename();
            Concrete\Core\Block\Block::getBlockFilename();
        }

        public static function getBlockDisplayOrder()
        {
            // Concrete\Core\Block\Block::getBlockDisplayOrder();
            Concrete\Core\Block\Block::getBlockDisplayOrder();
        }

        public static function getBlockID()
        {
            // Concrete\Core\Block\Block::getBlockID();
            Concrete\Core\Block\Block::getBlockID();
        }

        public static function getBlockTypeID()
        {
            // Concrete\Core\Block\Block::getBlockTypeID();
            Concrete\Core\Block\Block::getBlockTypeID();
        }

        public static function getBlockTypeObject()
        {
            // Concrete\Core\Block\Block::getBlockTypeObject();
            Concrete\Core\Block\Block::getBlockTypeObject();
        }

        public static function getAreaHandle()
        {
            // Concrete\Core\Block\Block::getAreaHandle();
            Concrete\Core\Block\Block::getAreaHandle();
        }

        public static function getBlockUserID()
        {
            // Concrete\Core\Block\Block::getBlockUserID();
            Concrete\Core\Block\Block::getBlockUserID();
        }

        public static function getBlockName()
        {
            // Concrete\Core\Block\Block::getBlockName();
            Concrete\Core\Block\Block::getBlockName();
        }

        /**
         * Gets the date the block was added
         * if user is specified, returns in the current user's timezone
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getBlockDateAdded($type = "system")
        {
            // Concrete\Core\Block\Block::getBlockDateAdded();
            Concrete\Core\Block\Block::getBlockDateAdded($type);
        }

        public static function getBlockDateLastModified()
        {
            // Concrete\Core\Block\Block::getBlockDateLastModified();
            Concrete\Core\Block\Block::getBlockDateLastModified();
        }

        public static function _getBlockAction()
        {
            // Concrete\Core\Block\Block::_getBlockAction();
            Concrete\Core\Block\Block::_getBlockAction();
        }

        public static function setBlockActionCollectionID($bActionCID)
        {
            // Concrete\Core\Block\Block::setBlockActionCollectionID();
            Concrete\Core\Block\Block::setBlockActionCollectionID($bActionCID);
        }

        /**
         * @return integer|false The block action collection id or false if not found
         */
        public static function getBlockActionCollectionID()
        {
            // Concrete\Core\Block\Block::getBlockActionCollectionID();
            Concrete\Core\Block\Block::getBlockActionCollectionID();
        }

        public static function getBlockEditAction()
        {
            // Concrete\Core\Block\Block::getBlockEditAction();
            Concrete\Core\Block\Block::getBlockEditAction();
        }

        public static function getBlockUpdateInformationAction()
        {
            // Concrete\Core\Block\Block::getBlockUpdateInformationAction();
            Concrete\Core\Block\Block::getBlockUpdateInformationAction();
        }

        public static function getBlockMasterCollectionAliasAction()
        {
            // Concrete\Core\Block\Block::getBlockMasterCollectionAliasAction();
            Concrete\Core\Block\Block::getBlockMasterCollectionAliasAction();
        }

        public static function getBlockUpdateCssAction()
        {
            // Concrete\Core\Block\Block::getBlockUpdateCssAction();
            Concrete\Core\Block\Block::getBlockUpdateCssAction();
        }

        public static function isEditable()
        {
            // Concrete\Core\Block\Block::isEditable();
            Concrete\Core\Block\Block::isEditable();
        }

        public static function overrideAreaPermissions()
        {
            // Concrete\Core\Block\Block::overrideAreaPermissions();
            Concrete\Core\Block\Block::overrideAreaPermissions();
        }

        public static function delete($forceDelete = null)
        {
            // Concrete\Core\Block\Block::delete();
            Concrete\Core\Block\Block::delete($forceDelete);
        }

        public static function deleteBlock($forceDelete = null)
        {
            // Concrete\Core\Block\Block::deleteBlock();
            Concrete\Core\Block\Block::deleteBlock($forceDelete);
        }

        public static function setOriginalBlockID($originalBID)
        {
            // Concrete\Core\Block\Block::setOriginalBlockID();
            Concrete\Core\Block\Block::setOriginalBlockID($originalBID);
        }

        public static function moveBlockToDisplayOrderPosition($afterBlock)
        {
            // Concrete\Core\Block\Block::moveBlockToDisplayOrderPosition();
            Concrete\Core\Block\Block::moveBlockToDisplayOrderPosition($afterBlock);
        }

        public static function setAbsoluteBlockDisplayOrder($do)
        {
            // Concrete\Core\Block\Block::setAbsoluteBlockDisplayOrder();
            Concrete\Core\Block\Block::setAbsoluteBlockDisplayOrder($do);
        }

        public static function doOverrideAreaPermissions()
        {
            // Concrete\Core\Block\Block::doOverrideAreaPermissions();
            Concrete\Core\Block\Block::doOverrideAreaPermissions();
        }

        public static function setCustomTemplate($template)
        {
            // Concrete\Core\Block\Block::setCustomTemplate();
            Concrete\Core\Block\Block::setCustomTemplate($template);
        }

        public static function setName($name)
        {
            // Concrete\Core\Block\Block::setName();
            Concrete\Core\Block\Block::setName($name);
        }

        public static function refreshBlockOutputCache()
        {
            // Concrete\Core\Block\Block::refreshBlockOutputCache();
            Concrete\Core\Block\Block::refreshBlockOutputCache();
        }

        /**
         * Removes a cached version of the block
         */
        public static function refreshCache()
        {
            // Concrete\Core\Block\Block::refreshCache();
            Concrete\Core\Block\Block::refreshCache();
        }

        public static function refreshCacheAll()
        {
            // Concrete\Core\Block\Block::refreshCacheAll();
            Concrete\Core\Block\Block::refreshCacheAll();
        }

        public static function export($node, $exportType = "full")
        {
            // Concrete\Core\Block\Block::export();
            Concrete\Core\Block\Block::export($node, $exportType);
        }

        public static function updateBlockInformation($data)
        {
            // Concrete\Core\Block\Block::updateBlockInformation();
            Concrete\Core\Block\Block::updateBlockInformation($data);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class Marketplace extends \Concrete\Core\Marketplace\Marketplace
    {

        public static function getInstance()
        {
            // Concrete\Core\Marketplace\Marketplace::getInstance();
            Concrete\Core\Marketplace\Marketplace::getInstance();
        }

        public static function __construct()
        {
            // Concrete\Core\Marketplace\Marketplace::__construct();
            Concrete\Core\Marketplace\Marketplace::__construct();
        }

        public static function isConnected()
        {
            // Concrete\Core\Marketplace\Marketplace::isConnected();
            Concrete\Core\Marketplace\Marketplace::isConnected();
        }

        public static function hasConnectionError()
        {
            // Concrete\Core\Marketplace\Marketplace::hasConnectionError();
            Concrete\Core\Marketplace\Marketplace::hasConnectionError();
        }

        public static function getConnectionError()
        {
            // Concrete\Core\Marketplace\Marketplace::getConnectionError();
            Concrete\Core\Marketplace\Marketplace::getConnectionError();
        }

        public static function generateSiteToken()
        {
            // Concrete\Core\Marketplace\Marketplace::generateSiteToken();
            Concrete\Core\Marketplace\Marketplace::generateSiteToken();
        }

        public static function getSiteToken()
        {
            // Concrete\Core\Marketplace\Marketplace::getSiteToken();
            Concrete\Core\Marketplace\Marketplace::getSiteToken();
        }

        public static function getSitePageURL()
        {
            // Concrete\Core\Marketplace\Marketplace::getSitePageURL();
            Concrete\Core\Marketplace\Marketplace::getSitePageURL();
        }

        public static function downloadRemoteFile($file)
        {
            // Concrete\Core\Marketplace\Marketplace::downloadRemoteFile();
            Concrete\Core\Marketplace\Marketplace::downloadRemoteFile($file);
        }

        public static function getMarketplaceFrame($width = "100%", $height = 300, $completeURL = null, $connectMethod = "view")
        {
            // Concrete\Core\Marketplace\Marketplace::getMarketplaceFrame();
            Concrete\Core\Marketplace\Marketplace::getMarketplaceFrame($width, $height, $completeURL, $connectMethod);
        }

        public static function getMarketplacePurchaseFrame($mp, $width = "100%", $height = 530)
        {
            // Concrete\Core\Marketplace\Marketplace::getMarketplacePurchaseFrame();
            Concrete\Core\Marketplace\Marketplace::getMarketplacePurchaseFrame($mp, $width, $height);
        }

        /**
         * Runs through all packages on the marketplace, sees if they're installed here, and updates the available version number for them
         */
        public static function checkPackageUpdates()
        {
            // Concrete\Core\Marketplace\Marketplace::checkPackageUpdates();
            Concrete\Core\Marketplace\Marketplace::checkPackageUpdates();
        }

        public static function getAvailableMarketplaceItems($filterInstalled = "1")
        {
            // Concrete\Core\Marketplace\Marketplace::getAvailableMarketplaceItems();
            Concrete\Core\Marketplace\Marketplace::getAvailableMarketplaceItems($filterInstalled);
        }

    }

    class BlockType extends \Concrete\Core\Block\BlockType\BlockType
    {

        /**
         * Sets the block type handle
         */
        public static function setBlockTypeHandle($btHandle)
        {
            // Concrete\Core\Block\BlockType\BlockType::setBlockTypeHandle();
            Concrete\Core\Block\BlockType\BlockType::setBlockTypeHandle($btHandle);
        }

        /**
         * Determines if the block type has templates available
         * @return boolean
         */
        public static function hasAddTemplate()
        {
            // Concrete\Core\Block\BlockType\BlockType::hasAddTemplate();
            Concrete\Core\Block\BlockType\BlockType::hasAddTemplate();
        }

        /**
         * Retrieves a BlockType object based on its btHandle
         * @return BlockType
         */
        public static function getByHandle($btHandle)
        {
            // Concrete\Core\Block\BlockType\BlockType::getByHandle();
            Concrete\Core\Block\BlockType\BlockType::getByHandle($btHandle);
        }

        /**
         * gets the available composer templates
         * used for editing instances of the BlockType while in the composer ui in the dashboard
         * @return TemplateFile[]
         */
        public static function getBlockTypeComposerTemplates()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeComposerTemplates();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeComposerTemplates();
        }

        /**
         * Retrieves a BlockType object based on its btID
         * @return BlockType
         */
        public static function getByID($btID)
        {
            // Concrete\Core\Block\BlockType\BlockType::getByID();
            Concrete\Core\Block\BlockType\BlockType::getByID($btID);
        }

        /**
         * Loads controller
         */
        protected static function loadController()
        {
            // Concrete\Core\Block\BlockType\BlockType::loadController();
            Concrete\Core\Block\BlockType\BlockType::loadController();
        }

        /**
         * if a the current BlockType is Internal or not - meaning one of the core built-in concrete5 blocks
         * @access private
         * @return boolean
         */
        public static function isBlockTypeInternal()
        {
            // Concrete\Core\Block\BlockType\BlockType::isBlockTypeInternal();
            Concrete\Core\Block\BlockType\BlockType::isBlockTypeInternal();
        }

        /**
         * if a the current BlockType supports inline edit or not
         * @return boolean
         */
        public static function supportsInlineEdit()
        {
            // Concrete\Core\Block\BlockType\BlockType::supportsInlineEdit();
            Concrete\Core\Block\BlockType\BlockType::supportsInlineEdit();
        }

        /**
         * if a the current BlockType supports inline add or not
         * @return boolean
         */
        public static function supportsInlineAdd()
        {
            // Concrete\Core\Block\BlockType\BlockType::supportsInlineAdd();
            Concrete\Core\Block\BlockType\BlockType::supportsInlineAdd();
        }

        /**
         * Returns true if the block type is internal (and therefore cannot be removed) a core block
         * @return boolean
         */
        public static function isInternalBlockType()
        {
            // Concrete\Core\Block\BlockType\BlockType::isInternalBlockType();
            Concrete\Core\Block\BlockType\BlockType::isInternalBlockType();
        }

        /**
         * returns the width in pixels that the block type's editing dialog will open in
         * @return int
         */
        public static function getBlockTypeInterfaceWidth()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeInterfaceWidth();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeInterfaceWidth();
        }

        /**
         * returns the height in pixels that the block type's editing dialog will open in
         * @return int
         */
        public static function getBlockTypeInterfaceHeight()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeInterfaceHeight();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeInterfaceHeight();
        }

        /**
         * returns the id of the BlockType's package if it's in a package
         * @return int
         */
        public static function getPackageID()
        {
            // Concrete\Core\Block\BlockType\BlockType::getPackageID();
            Concrete\Core\Block\BlockType\BlockType::getPackageID();
        }

        /**
         * returns the handle of the BlockType's package if it's in a package
         * @return string
         */
        public static function getPackageHandle()
        {
            // Concrete\Core\Block\BlockType\BlockType::getPackageHandle();
            Concrete\Core\Block\BlockType\BlockType::getPackageHandle();
        }

        /**
         * gets the BlockTypes description text
         * @return string
         */
        public static function getBlockTypeDescription()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeDescription();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeDescription();
        }

        /**
         * @return int
         */
        public static function getBlockTypeID()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeID();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeID();
        }

        /**
         * @return string
         */
        public static function getBlockTypeHandle()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeHandle();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeHandle();
        }

        /**
         * @return string
         */
        public static function getBlockTypeName()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeName();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeName();
        }

        /**
         * @return boolean
         */
        public static function isCopiedWhenPropagated()
        {
            // Concrete\Core\Block\BlockType\BlockType::isCopiedWhenPropagated();
            Concrete\Core\Block\BlockType\BlockType::isCopiedWhenPropagated();
        }

        /**
         * If true, this block is not versioned on a page – it is included as is on all versions of the page, even when updated.
         * @return boolean
         */
        public static function includeAll()
        {
            // Concrete\Core\Block\BlockType\BlockType::includeAll();
            Concrete\Core\Block\BlockType\BlockType::includeAll();
        }

        /**
         * Returns the class for the current block type.
         */
        public static function getBlockTypeClass()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeClass();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeClass();
        }

        /**
         * @deprecated
         */
        public static function getBlockTypeClassFromHandle()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeClassFromHandle();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeClassFromHandle();
        }

        /**
         * Return the class file that this BlockType uses
         * @return string
         */
        public static function getBlockTypeMappedClass($btHandle, $pkgHandle = null)
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeMappedClass();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeMappedClass($btHandle, $pkgHandle);
        }

        /**
         * Returns an array of all BlockTypeSet objects that this block is in
         */
        public static function getBlockTypeSets()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeSets();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeSets();
        }

        /**
         * Returns the number of unique instances of this block throughout the entire site
         * note - this count could include blocks in areas that are no longer rendered by the theme
         * @param boolean specify true if you only want to see the number of blocks in active pages
         * @return int
         */
        public static function getCount($ignoreUnapprovedVersions = null)
        {
            // Concrete\Core\Block\BlockType\BlockType::getCount();
            Concrete\Core\Block\BlockType\BlockType::getCount($ignoreUnapprovedVersions);
        }

        /**
         * Not a permissions call. Actually checks to see whether this block is not an internal one.
         * @return boolean
         */
        public static function canUnInstall()
        {
            // Concrete\Core\Block\BlockType\BlockType::canUnInstall();
            Concrete\Core\Block\BlockType\BlockType::canUnInstall();
        }

        /**
         * Renders a particular view of a block type, using the public $controller variable as the block type's controller
         * @param string template 'view' for the default
         * @return void
         */
        public static function render($view = "view")
        {
            // Concrete\Core\Block\BlockType\BlockType::render();
            Concrete\Core\Block\BlockType\BlockType::render($view);
        }

        /**
         * get's the block type controller
         * @return BlockTypeController
         */
        public static function getController()
        {
            // Concrete\Core\Block\BlockType\BlockType::getController();
            Concrete\Core\Block\BlockType\BlockType::getController();
        }

        /**
         * Gets the custom templates available for the current BlockType
         * @return TemplateFile[]
         */
        public static function getBlockTypeCustomTemplates()
        {
            // Concrete\Core\Block\BlockType\BlockType::getBlockTypeCustomTemplates();
            Concrete\Core\Block\BlockType\BlockType::getBlockTypeCustomTemplates();
        }

        /**
         * @private
         */
        public static function setBlockTypeDisplayOrder($displayOrder)
        {
            // Concrete\Core\Block\BlockType\BlockType::setBlockTypeDisplayOrder();
            Concrete\Core\Block\BlockType\BlockType::setBlockTypeDisplayOrder($displayOrder);
        }

        /**
         * @deprecated
         */
        public static function installBlockTypeFromPackage($btHandle, $pkg)
        {
            // Concrete\Core\Block\BlockType\BlockType::installBlockTypeFromPackage();
            Concrete\Core\Block\BlockType\BlockType::installBlockTypeFromPackage($btHandle, $pkg);
        }

        /**
         * refreshes the BlockType's database schema throws an Exception if error
         * @return void
         */
        public static function refresh()
        {
            // Concrete\Core\Block\BlockType\BlockType::refresh();
            Concrete\Core\Block\BlockType\BlockType::refresh();
        }

        /**
         * Installs a BlockType that is passed via a btHandle string. The core or override directories are parsed.
         */
        public static function installBlockType($btHandle, $pkg = null)
        {
            // Concrete\Core\Block\BlockType\BlockType::installBlockType();
            Concrete\Core\Block\BlockType\BlockType::installBlockType($btHandle, $pkg);
        }

        protected static function loadFromController($bta)
        {
            // Concrete\Core\Block\BlockType\BlockType::loadFromController();
            Concrete\Core\Block\BlockType\BlockType::loadFromController($bta);
        }

        /**
         * Removes the block type. Also removes instances of content.
         */
        public static function delete()
        {
            // Concrete\Core\Block\BlockType\BlockType::delete();
            Concrete\Core\Block\BlockType\BlockType::delete();
        }

        /**
         * Adds a block to the system without adding it to a collection.
         * Passes page and area data along if it is available, however.
         */
        public static function add($data, $c = null, $a = null)
        {
            // Concrete\Core\Block\BlockType\BlockType::add();
            Concrete\Core\Block\BlockType\BlockType::add($data, $c, $a);
        }

    }

    class BlockTypeList extends \Concrete\Core\Block\BlockType\BlockTypeList
    {

        public static function __construct()
        {
            // Concrete\Core\Block\BlockType\BlockTypeList::__construct();
            Concrete\Core\Block\BlockType\BlockTypeList::__construct();
        }

        public static function get($itemsToGet = 100, $offset = null)
        {
            // Concrete\Core\Block\BlockType\BlockTypeList::get();
            Concrete\Core\Block\BlockType\BlockTypeList::get($itemsToGet, $offset);
        }

        public static function filterByPackage(Concrete\Core\Package\Package $pkg)
        {
            // Concrete\Core\Block\BlockType\BlockTypeList::filterByPackage();
            Concrete\Core\Block\BlockType\BlockTypeList::filterByPackage($pkg);
        }

        /**
         * @todo comment this one
         * @param string $xml
         * @return void
         */
        public static function exportList($xml)
        {
            // Concrete\Core\Block\BlockType\BlockTypeList::exportList();
            Concrete\Core\Block\BlockType\BlockTypeList::exportList($xml);
        }

        /**
         * returns an array of Block Types used in the concrete5 Dashboard
         * @return BlockType[]
         */
        public static function getDashboardBlockTypes()
        {
            // Concrete\Core\Block\BlockType\BlockTypeList::getDashboardBlockTypes();
            Concrete\Core\Block\BlockType\BlockTypeList::getDashboardBlockTypes();
        }

        /**
         * Gets a list of block types that are not installed, used to get blocks that can be installed
         * This function only surveys the web/blocks directory - it's not looking at the package level.
         * @return BlockType[]
         */
        public static function getAvailableList()
        {
            // Concrete\Core\Block\BlockType\BlockTypeList::getAvailableList();
            Concrete\Core\Block\BlockType\BlockTypeList::getAvailableList();
        }

        /**
         * gets a list of installed BlockTypes
         * @return BlockType[]
         */
        public static function getInstalledList()
        {
            // Concrete\Core\Block\BlockType\BlockTypeList::getInstalledList();
            Concrete\Core\Block\BlockType\BlockTypeList::getInstalledList();
        }

        public static function resetBlockTypeDisplayOrder($column = "btID")
        {
            // Concrete\Core\Block\BlockType\BlockTypeList::resetBlockTypeDisplayOrder();
            Concrete\Core\Block\BlockType\BlockTypeList::resetBlockTypeDisplayOrder($column);
        }

        public static function getTotal()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getTotal();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getTotal();
        }

        public static function debug($dbg = "1")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug($dbg);
        }

        protected static function setQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery($query);
        }

        protected static function getQuery()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
        }

        public static function addToQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery($query);
        }

        protected static function setupAutoSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
        }

        protected static function executeBase()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
        }

        protected static function setupSortByString()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
        }

        protected static function setupAttributeSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
        }

        /**
         * Adds a filter to this item list
         */
        public static function filter($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter($column, $value, $comparison);
        }

        public static function getSearchResultsClass($field)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass($field);
        }

        public static function sortBy($key, $dir = "asc")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy($key, $dir);
        }

        public static function groupBy($key)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy($key);
        }

        public static function having($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having($column, $value, $comparison);
        }

        public static function getSortByURL($column, $dir = "asc", $baseURL = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL($column, $dir, $baseURL, $additionalVars);
        }

        protected static function setupAttributeFilters($join)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters($join);
        }

        public static function filterByAttribute($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute($column, $value, $comparison);
        }

        public static function enableStickySearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest($namespace);
        }

        public static function getQueryStringPagingVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
        }

        public static function getQueryStringSortVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
        }

        public static function getQueryStringSortDirectionVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
        }

        protected static function getStickySearchNameSpace()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
        }

        public static function resetSearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest($namespace);
        }

        public static function addToSearchRequest($key, $value)
        {
            // Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest($key, $value);
        }

        public static function getSearchRequest()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
        }

        public static function setItemsPerPage($num)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage($num);
        }

        public static function getItemsPerPage()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
        }

        public static function setItems($items)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItems();
            Concrete\Core\Foundation\Collection\ItemList::setItems($items);
        }

        public static function setNameSpace($ns)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::setNameSpace($ns);
        }

        /**
         * Returns an array of object by "page"
         */
        public static function getPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPage();
            Concrete\Core\Foundation\Collection\ItemList::getPage($page);
        }

        protected static function setCurrentPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setCurrentPage();
            Concrete\Core\Foundation\Collection\ItemList::setCurrentPage($page);
        }

        /**
         * Displays summary text about a list
         */
        public static function displaySummary($right_content = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displaySummary();
            Concrete\Core\Foundation\Collection\ItemList::displaySummary($right_content);
        }

        public static function isActiveSortColumn($column)
        {
            // Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn($column);
        }

        public static function getActiveSortColumn()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
        }

        public static function getActiveSortDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
        }

        public static function requiresPaging()
        {
            // Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
            Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
        }

        public static function getPagination($url = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPagination();
            Concrete\Core\Foundation\Collection\ItemList::getPagination($url, $additionalVars);
        }

        /**
         * Gets paging that works in our new format */
        public static function displayPagingV2($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPagingV2();
            Concrete\Core\Foundation\Collection\ItemList::displayPagingV2($script, $return, $additionalVars);
        }

        /**
         * Gets standard HTML to display paging */
        public static function displayPaging($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPaging();
            Concrete\Core\Foundation\Collection\ItemList::displayPaging($script, $return, $additionalVars);
        }

        /**
         * Returns an object with properties useful for paging
         */
        public static function getSummary()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSummary();
            Concrete\Core\Foundation\Collection\ItemList::getSummary();
        }

        public static function getSortBy()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortBy();
            Concrete\Core\Foundation\Collection\ItemList::getSortBy();
        }

        public static function getSortByDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
            Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
        }

        /**
         * Sets up a multiple columns to search by. Each argument is taken "as-is" (including asc or desc) and concatenated with commas
         * Note that this is overrides any previous sortByMultiple() call, and all sortBy() calls. Alternatively, you can pass a single
         * array with multiple columns to sort by as its values.
         * e.g. $list->sortByMultiple('columna desc', 'columnb asc');
         * or $list->sortByMultiple(array('columna desc', 'columnb asc'));
         */
        public static function sortByMultiple()
        {
            // Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
            Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
        }

    }

    class BlockTypeSet extends \Concrete\Core\Block\BlockType\Set
    {

        public static function getByID($btsID)
        {
            // Concrete\Core\Block\BlockType\Set::getByID();
            Concrete\Core\Block\BlockType\Set::getByID($btsID);
        }

        public static function getByHandle($btsHandle)
        {
            // Concrete\Core\Block\BlockType\Set::getByHandle();
            Concrete\Core\Block\BlockType\Set::getByHandle($btsHandle);
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Block\BlockType\Set::getListByPackage();
            Concrete\Core\Block\BlockType\Set::getListByPackage($pkg);
        }

        public static function getList()
        {
            // Concrete\Core\Block\BlockType\Set::getList();
            Concrete\Core\Block\BlockType\Set::getList();
        }

        public static function getBlockTypeSetID()
        {
            // Concrete\Core\Block\BlockType\Set::getBlockTypeSetID();
            Concrete\Core\Block\BlockType\Set::getBlockTypeSetID();
        }

        public static function getBlockTypeSetHandle()
        {
            // Concrete\Core\Block\BlockType\Set::getBlockTypeSetHandle();
            Concrete\Core\Block\BlockType\Set::getBlockTypeSetHandle();
        }

        public static function getBlockTypeSetName()
        {
            // Concrete\Core\Block\BlockType\Set::getBlockTypeSetName();
            Concrete\Core\Block\BlockType\Set::getBlockTypeSetName();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Block\BlockType\Set::getPackageID();
            Concrete\Core\Block\BlockType\Set::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Block\BlockType\Set::getPackageHandle();
            Concrete\Core\Block\BlockType\Set::getPackageHandle();
        }

        public static function updateBlockTypeSetName($btsName)
        {
            // Concrete\Core\Block\BlockType\Set::updateBlockTypeSetName();
            Concrete\Core\Block\BlockType\Set::updateBlockTypeSetName($btsName);
        }

        public static function updateBlockTypeSetHandle($btsHandle)
        {
            // Concrete\Core\Block\BlockType\Set::updateBlockTypeSetHandle();
            Concrete\Core\Block\BlockType\Set::updateBlockTypeSetHandle($btsHandle);
        }

        public static function addBlockType(Concrete\Core\Block\BlockType\BlockType $bt)
        {
            // Concrete\Core\Block\BlockType\Set::addBlockType();
            Concrete\Core\Block\BlockType\Set::addBlockType($bt);
        }

        public static function clearBlockTypes()
        {
            // Concrete\Core\Block\BlockType\Set::clearBlockTypes();
            Concrete\Core\Block\BlockType\Set::clearBlockTypes();
        }

        public static function add($btsHandle, $btsName, $pkg = null)
        {
            // Concrete\Core\Block\BlockType\Set::add();
            Concrete\Core\Block\BlockType\Set::add($btsHandle, $btsName, $pkg);
        }

        public static function export($axml)
        {
            // Concrete\Core\Block\BlockType\Set::export();
            Concrete\Core\Block\BlockType\Set::export($axml);
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Block\BlockType\Set::exportList();
            Concrete\Core\Block\BlockType\Set::exportList($xml);
        }

        public static function getBlockTypes()
        {
            // Concrete\Core\Block\BlockType\Set::getBlockTypes();
            Concrete\Core\Block\BlockType\Set::getBlockTypes();
        }

        public static function get()
        {
            // Concrete\Core\Block\BlockType\Set::get();
            Concrete\Core\Block\BlockType\Set::get();
        }

        public static function contains($bt)
        {
            // Concrete\Core\Block\BlockType\Set::contains();
            Concrete\Core\Block\BlockType\Set::contains($bt);
        }

        public static function delete()
        {
            // Concrete\Core\Block\BlockType\Set::delete();
            Concrete\Core\Block\BlockType\Set::delete();
        }

        public static function deleteKey($bt)
        {
            // Concrete\Core\Block\BlockType\Set::deleteKey();
            Concrete\Core\Block\BlockType\Set::deleteKey($bt);
        }

        protected static function rescanDisplayOrder()
        {
            // Concrete\Core\Block\BlockType\Set::rescanDisplayOrder();
            Concrete\Core\Block\BlockType\Set::rescanDisplayOrder();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class Package extends \Concrete\Core\Package\Package
    {

        public static function getRelativePath()
        {
            // Concrete\Core\Package\Package::getRelativePath();
            Concrete\Core\Package\Package::getRelativePath();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Package\Package::getPackageID();
            Concrete\Core\Package\Package::getPackageID();
        }

        public static function getPackageName()
        {
            // Concrete\Core\Package\Package::getPackageName();
            Concrete\Core\Package\Package::getPackageName();
        }

        public static function getPackageDescription()
        {
            // Concrete\Core\Package\Package::getPackageDescription();
            Concrete\Core\Package\Package::getPackageDescription();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Package\Package::getPackageHandle();
            Concrete\Core\Package\Package::getPackageHandle();
        }

        /**
         * Gets the date the package was added to the system,
         * if user is specified, returns in the current user's timezone
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getPackageDateInstalled($type = "system")
        {
            // Concrete\Core\Package\Package::getPackageDateInstalled();
            Concrete\Core\Package\Package::getPackageDateInstalled($type);
        }

        public static function getPackageVersion()
        {
            // Concrete\Core\Package\Package::getPackageVersion();
            Concrete\Core\Package\Package::getPackageVersion();
        }

        public static function getPackageVersionUpdateAvailable()
        {
            // Concrete\Core\Package\Package::getPackageVersionUpdateAvailable();
            Concrete\Core\Package\Package::getPackageVersionUpdateAvailable();
        }

        public static function isPackageInstalled()
        {
            // Concrete\Core\Package\Package::isPackageInstalled();
            Concrete\Core\Package\Package::isPackageInstalled();
        }

        public static function getChangelogContents()
        {
            // Concrete\Core\Package\Package::getChangelogContents();
            Concrete\Core\Package\Package::getChangelogContents();
        }

        /**
         * Returns the currently installed package version.
         * NOTE: This function only returns a value if getLocalUpgradeablePackages() has been called first!
         */
        public static function getPackageCurrentlyInstalledVersion()
        {
            // Concrete\Core\Package\Package::getPackageCurrentlyInstalledVersion();
            Concrete\Core\Package\Package::getPackageCurrentlyInstalledVersion();
        }

        public static function getApplicationVersionRequired()
        {
            // Concrete\Core\Package\Package::getApplicationVersionRequired();
            Concrete\Core\Package\Package::getApplicationVersionRequired();
        }

        public static function hasInstallNotes()
        {
            // Concrete\Core\Package\Package::hasInstallNotes();
            Concrete\Core\Package\Package::hasInstallNotes();
        }

        public static function hasInstallPostScreen()
        {
            // Concrete\Core\Package\Package::hasInstallPostScreen();
            Concrete\Core\Package\Package::hasInstallPostScreen();
        }

        public static function allowsFullContentSwap()
        {
            // Concrete\Core\Package\Package::allowsFullContentSwap();
            Concrete\Core\Package\Package::allowsFullContentSwap();
        }

        public static function showInstallOptionsScreen()
        {
            // Concrete\Core\Package\Package::showInstallOptionsScreen();
            Concrete\Core\Package\Package::showInstallOptionsScreen();
        }

        public static function installDB($xmlFile)
        {
            // Concrete\Core\Package\Package::installDB();
            Concrete\Core\Package\Package::installDB($xmlFile);
        }

        public static function getClass($pkgHandle)
        {
            // Concrete\Core\Package\Package::getClass();
            Concrete\Core\Package\Package::getClass($pkgHandle);
        }

        /**
         * Loads package translation files into zend translate
         * @param string $locale
         * @param string $key
         * @return void
         */
        public static function setupPackageLocalization($locale = null, $key = null)
        {
            // Concrete\Core\Package\Package::setupPackageLocalization();
            Concrete\Core\Package\Package::setupPackageLocalization($locale, $key);
        }

        /**
         * Returns an array of package items (e.g. blocks, themes)
         */
        public static function getPackageItems()
        {
            // Concrete\Core\Package\Package::getPackageItems();
            Concrete\Core\Package\Package::getPackageItems();
        }

        /** Returns the display name of a category of package items (localized and escaped accordingly to $format)
         * @param string $categoryHandle The category handle
         * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getPackageItemsCategoryDisplayName($categoryHandle, $format = "html")
        {
            // Concrete\Core\Package\Package::getPackageItemsCategoryDisplayName();
            Concrete\Core\Package\Package::getPackageItemsCategoryDisplayName($categoryHandle, $format);
        }

        public static function getItemName($item)
        {
            // Concrete\Core\Package\Package::getItemName();
            Concrete\Core\Package\Package::getItemName($item);
        }

        /**
         * Uninstalls the package. Removes any blocks, themes, or pages associated with the package.
         */
        public static function uninstall()
        {
            // Concrete\Core\Package\Package::uninstall();
            Concrete\Core\Package\Package::uninstall();
        }

        protected static function validateClearSiteContents($options)
        {
            // Concrete\Core\Package\Package::validateClearSiteContents();
            Concrete\Core\Package\Package::validateClearSiteContents($options);
        }

        public static function swapContent($options)
        {
            // Concrete\Core\Package\Package::swapContent();
            Concrete\Core\Package\Package::swapContent($options);
        }

        public static function testForInstall($package, $testForAlreadyInstalled = "1")
        {
            // Concrete\Core\Package\Package::testForInstall();
            Concrete\Core\Package\Package::testForInstall($package, $testForAlreadyInstalled);
        }

        public static function mapError($testResults)
        {
            // Concrete\Core\Package\Package::mapError();
            Concrete\Core\Package\Package::mapError($testResults);
        }

        public static function getPackagePath()
        {
            // Concrete\Core\Package\Package::getPackagePath();
            Concrete\Core\Package\Package::getPackagePath();
        }

        /**
         * returns a Package object for the given package id, null if not found
         * @param int $pkgID
         * @return Package
         */
        public static function getByID($pkgID)
        {
            // Concrete\Core\Package\Package::getByID();
            Concrete\Core\Package\Package::getByID($pkgID);
        }

        /**
         * returns a Package object for the given package handle, null if not found
         * @param string $pkgHandle
         * @return Package
         */
        public static function getByHandle($pkgHandle)
        {
            // Concrete\Core\Package\Package::getByHandle();
            Concrete\Core\Package\Package::getByHandle($pkgHandle);
        }

        /**
         * @return Package
         */
        public static function install()
        {
            // Concrete\Core\Package\Package::install();
            Concrete\Core\Package\Package::install();
        }

        public static function updateAvailableVersionNumber($vNum)
        {
            // Concrete\Core\Package\Package::updateAvailableVersionNumber();
            Concrete\Core\Package\Package::updateAvailableVersionNumber($vNum);
        }

        public static function upgradeCoreData()
        {
            // Concrete\Core\Package\Package::upgradeCoreData();
            Concrete\Core\Package\Package::upgradeCoreData();
        }

        public static function upgrade()
        {
            // Concrete\Core\Package\Package::upgrade();
            Concrete\Core\Package\Package::upgrade();
        }

        public static function getInstalledHandles()
        {
            // Concrete\Core\Package\Package::getInstalledHandles();
            Concrete\Core\Package\Package::getInstalledHandles();
        }

        public static function getInstalledList()
        {
            // Concrete\Core\Package\Package::getInstalledList();
            Concrete\Core\Package\Package::getInstalledList();
        }

        /**
         * Returns an array of packages that have newer versions in the local packages directory
         * than those which are in the Packages table. This means they're ready to be upgraded
         */
        public static function getLocalUpgradeablePackages()
        {
            // Concrete\Core\Package\Package::getLocalUpgradeablePackages();
            Concrete\Core\Package\Package::getLocalUpgradeablePackages();
        }

        public static function getRemotelyUpgradeablePackages()
        {
            // Concrete\Core\Package\Package::getRemotelyUpgradeablePackages();
            Concrete\Core\Package\Package::getRemotelyUpgradeablePackages();
        }

        /**
         * moves the current package's directory to the trash directory renamed with the package handle and a date code.
         */
        public static function backup()
        {
            // Concrete\Core\Package\Package::backup();
            Concrete\Core\Package\Package::backup();
        }

        /**
         * if a packate was just backed up by this instance of the package object and the packages/package handle directory doesn't exist, this will restore the
         * package from the trash
         */
        public static function restore()
        {
            // Concrete\Core\Package\Package::restore();
            Concrete\Core\Package\Package::restore();
        }

        public static function config($cfKey, $getFullObject = null)
        {
            // Concrete\Core\Package\Package::config();
            Concrete\Core\Package\Package::config($cfKey, $getFullObject);
        }

        public static function saveConfig($cfKey, $value)
        {
            // Concrete\Core\Package\Package::saveConfig();
            Concrete\Core\Package\Package::saveConfig($cfKey, $value);
        }

        public static function clearConfig($cfKey)
        {
            // Concrete\Core\Package\Package::clearConfig();
            Concrete\Core\Package\Package::clearConfig($cfKey);
        }

        public static function getAvailablePackages($filterInstalled = "1")
        {
            // Concrete\Core\Package\Package::getAvailablePackages();
            Concrete\Core\Package\Package::getAvailablePackages($filterInstalled);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class Collection extends \Concrete\Core\Page\Collection\Collection
    {

        public static function loadVersionObject($cvID = "ACTIVE")
        {
            // Concrete\Core\Page\Collection\Collection::loadVersionObject();
            Concrete\Core\Page\Collection\Collection::loadVersionObject($cvID);
        }

        public static function getVersionToModify()
        {
            // Concrete\Core\Page\Collection\Collection::getVersionToModify();
            Concrete\Core\Page\Collection\Collection::getVersionToModify();
        }

        public static function getNextVersionComments()
        {
            // Concrete\Core\Page\Collection\Collection::getNextVersionComments();
            Concrete\Core\Page\Collection\Collection::getNextVersionComments();
        }

        public static function cloneVersion($versionComments)
        {
            // Concrete\Core\Page\Collection\Collection::cloneVersion();
            Concrete\Core\Page\Collection\Collection::cloneVersion($versionComments);
        }

        public static function getFeatureAssignments()
        {
            // Concrete\Core\Page\Collection\Collection::getFeatureAssignments();
            Concrete\Core\Page\Collection\Collection::getFeatureAssignments();
        }

        /**
         * Returns the value of the attribute with the handle $ak
         * of the current object.
         *
         * $displayMode makes it possible to get the correct output
         * value. When you need the raw attribute value or object, use
         * this:
         * <code>
         * $c = Page::getCurrentPage();
         * $attributeValue = $c->getAttribute('attribute_handle');
         * </code>
         *
         * But if you need the formatted output supported by some
         * attribute, use this:
         * <code>
         * $c = Page::getCurrentPage();
         * $attributeValue = $c->getAttribute('attribute_handle', 'display');
         * </code>
         *
         * An attribute type like "date" will then return the date in
         * the correct format just like other attributes will show
         * you a nicely formatted output and not just a simple value
         * or object.
         *
         *
         * @param string|object $akHandle
         * @param boolean $displayMode
         * @return type
         */
        public static function getAttribute($akHandle, $displayMode = null)
        {
            // Concrete\Core\Page\Collection\Collection::getAttribute();
            Concrete\Core\Page\Collection\Collection::getAttribute($akHandle, $displayMode);
        }

        public static function getCollectionAttributeValue($ak)
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionAttributeValue();
            Concrete\Core\Page\Collection\Collection::getCollectionAttributeValue($ak);
        }

        public static function clearCollectionAttributes($retainAKIDs = null)
        {
            // Concrete\Core\Page\Collection\Collection::clearCollectionAttributes();
            Concrete\Core\Page\Collection\Collection::clearCollectionAttributes($retainAKIDs);
        }

        public static function reindexPendingPages()
        {
            // Concrete\Core\Page\Collection\Collection::reindexPendingPages();
            Concrete\Core\Page\Collection\Collection::reindexPendingPages();
        }

        public static function reindex($index = null, $actuallyDoReindex = "1")
        {
            // Concrete\Core\Page\Collection\Collection::reindex();
            Concrete\Core\Page\Collection\Collection::reindex($index, $actuallyDoReindex);
        }

        public static function getAttributeValueObject($ak, $createIfNotFound = null)
        {
            // Concrete\Core\Page\Collection\Collection::getAttributeValueObject();
            Concrete\Core\Page\Collection\Collection::getAttributeValueObject($ak, $createIfNotFound);
        }

        public static function setAttribute($ak, $value)
        {
            // Concrete\Core\Page\Collection\Collection::setAttribute();
            Concrete\Core\Page\Collection\Collection::setAttribute($ak, $value);
        }

        public static function clearAttribute($ak)
        {
            // Concrete\Core\Page\Collection\Collection::clearAttribute();
            Concrete\Core\Page\Collection\Collection::clearAttribute($ak);
        }

        public static function getSetCollectionAttributes()
        {
            // Concrete\Core\Page\Collection\Collection::getSetCollectionAttributes();
            Concrete\Core\Page\Collection\Collection::getSetCollectionAttributes();
        }

        public static function addAttribute($ak, $value)
        {
            // Concrete\Core\Page\Collection\Collection::addAttribute();
            Concrete\Core\Page\Collection\Collection::addAttribute($ak, $value);
        }

        public static function getArea($arHandle)
        {
            // Concrete\Core\Page\Collection\Collection::getArea();
            Concrete\Core\Page\Collection\Collection::getArea($arHandle);
        }

        public static function hasAliasedContent()
        {
            // Concrete\Core\Page\Collection\Collection::hasAliasedContent();
            Concrete\Core\Page\Collection\Collection::hasAliasedContent();
        }

        public static function getCollectionID()
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionID();
            Concrete\Core\Page\Collection\Collection::getCollectionID();
        }

        public static function getCollectionDateLastModified($mask = null, $type = "system")
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionDateLastModified();
            Concrete\Core\Page\Collection\Collection::getCollectionDateLastModified($mask, $type);
        }

        public static function getVersionObject()
        {
            // Concrete\Core\Page\Collection\Collection::getVersionObject();
            Concrete\Core\Page\Collection\Collection::getVersionObject();
        }

        public static function getCollectionHandle()
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionHandle();
            Concrete\Core\Page\Collection\Collection::getCollectionHandle();
        }

        public static function getCollectionDateAdded($mask = null, $type = "system")
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionDateAdded();
            Concrete\Core\Page\Collection\Collection::getCollectionDateAdded($mask, $type);
        }

        public static function getVersionID()
        {
            // Concrete\Core\Page\Collection\Collection::getVersionID();
            Concrete\Core\Page\Collection\Collection::getVersionID();
        }

        public static function __destruct()
        {
            // Concrete\Core\Page\Collection\Collection::__destruct();
            Concrete\Core\Page\Collection\Collection::__destruct();
        }

        public static function getCollectionAreaDisplayOrder($arHandle, $ignoreVersions = null)
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionAreaDisplayOrder();
            Concrete\Core\Page\Collection\Collection::getCollectionAreaDisplayOrder($arHandle, $ignoreVersions);
        }

        /**
         * Retrieves all custom style rules that should be inserted into the header on a page, whether they are defined in areas
         * or blocks
         */
        public static function outputCustomStyleHeaderItems($return = null)
        {
            // Concrete\Core\Page\Collection\Collection::outputCustomStyleHeaderItems();
            Concrete\Core\Page\Collection\Collection::outputCustomStyleHeaderItems($return);
        }

        public static function getAreaCustomStyleRule($area)
        {
            // Concrete\Core\Page\Collection\Collection::getAreaCustomStyleRule();
            Concrete\Core\Page\Collection\Collection::getAreaCustomStyleRule($area);
        }

        public static function resetAreaCustomStyle($area)
        {
            // Concrete\Core\Page\Collection\Collection::resetAreaCustomStyle();
            Concrete\Core\Page\Collection\Collection::resetAreaCustomStyle($area);
        }

        public static function setAreaCustomStyle($area, $csr)
        {
            // Concrete\Core\Page\Collection\Collection::setAreaCustomStyle();
            Concrete\Core\Page\Collection\Collection::setAreaCustomStyle($area, $csr);
        }

        public static function relateVersionEdits($oc)
        {
            // Concrete\Core\Page\Collection\Collection::relateVersionEdits();
            Concrete\Core\Page\Collection\Collection::relateVersionEdits($oc);
        }

        public static function getCollectionTypeID()
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionTypeID();
            Concrete\Core\Page\Collection\Collection::getCollectionTypeID();
        }

        public static function getPageTypeID()
        {
            // Concrete\Core\Page\Collection\Collection::getPageTypeID();
            Concrete\Core\Page\Collection\Collection::getPageTypeID();
        }

        public static function rescanDisplayOrder($areaName)
        {
            // Concrete\Core\Page\Collection\Collection::rescanDisplayOrder();
            Concrete\Core\Page\Collection\Collection::rescanDisplayOrder($areaName);
        }

        /**
         * @param int $cID
         * @param mixed $version 'RECENT'|'ACTIVE'|version id
         * @return Collection
         */
        public static function getByID($cID, $version = "RECENT")
        {
            // Concrete\Core\Page\Collection\Collection::getByID();
            Concrete\Core\Page\Collection\Collection::getByID($cID, $version);
        }

        public static function getByHandle($handle)
        {
            // Concrete\Core\Page\Collection\Collection::getByHandle();
            Concrete\Core\Page\Collection\Collection::getByHandle($handle);
        }

        public static function refreshCache()
        {
            // Concrete\Core\Page\Collection\Collection::refreshCache();
            Concrete\Core\Page\Collection\Collection::refreshCache();
        }

        public static function getGlobalBlocks()
        {
            // Concrete\Core\Page\Collection\Collection::getGlobalBlocks();
            Concrete\Core\Page\Collection\Collection::getGlobalBlocks();
        }

        /**
         * List the block IDs in a collection or area within a collection
         * @param string $arHandle. If specified, returns just the blocks in an area
         * @return array
         */
        public static function getBlockIDs($arHandle = null)
        {
            // Concrete\Core\Page\Collection\Collection::getBlockIDs();
            Concrete\Core\Page\Collection\Collection::getBlockIDs($arHandle);
        }

        /**
         * List the blocks in a collection or area within a collection
         * @param string $arHandle. If specified, returns just the blocks in an area
         * @return array
         */
        public static function getBlocks($arHandle = null)
        {
            // Concrete\Core\Page\Collection\Collection::getBlocks();
            Concrete\Core\Page\Collection\Collection::getBlocks($arHandle);
        }

        public static function addBlock($bt, $a, $data)
        {
            // Concrete\Core\Page\Collection\Collection::addBlock();
            Concrete\Core\Page\Collection\Collection::addBlock($bt, $a, $data);
        }

        public static function addFeature(Concrete\Core\Feature\Feature $fe)
        {
            // Concrete\Core\Page\Collection\Collection::addFeature();
            Concrete\Core\Page\Collection\Collection::addFeature($fe);
        }

        public static function addCollection($data)
        {
            // Concrete\Core\Page\Collection\Collection::addCollection();
            Concrete\Core\Page\Collection\Collection::addCollection($data);
        }

        public static function markModified()
        {
            // Concrete\Core\Page\Collection\Collection::markModified();
            Concrete\Core\Page\Collection\Collection::markModified();
        }

        public static function delete()
        {
            // Concrete\Core\Page\Collection\Collection::delete();
            Concrete\Core\Page\Collection\Collection::delete();
        }

        public static function duplicateCollection()
        {
            // Concrete\Core\Page\Collection\Collection::duplicateCollection();
            Concrete\Core\Page\Collection\Collection::duplicateCollection();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class CollectionVersion extends \Concrete\Core\Page\Collection\Version\Version
    {

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\Page\Collection\Version\Version::getPermissionObjectIdentifier();
            Concrete\Core\Page\Collection\Version\Version::getPermissionObjectIdentifier();
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\Page\Collection\Version\Version::getPermissionResponseClassName();
            Concrete\Core\Page\Collection\Version\Version::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\Page\Collection\Version\Version::getPermissionAssignmentClassName();
            Concrete\Core\Page\Collection\Version\Version::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\Page\Collection\Version\Version::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\Page\Collection\Version\Version::getPermissionObjectKeyCategoryHandle();
        }

        public static function refreshCache()
        {
            // Concrete\Core\Page\Collection\Version\Version::refreshCache();
            Concrete\Core\Page\Collection\Version\Version::refreshCache();
        }

        public static function get(&$c, $cvID)
        {
            // Concrete\Core\Page\Collection\Version\Version::get();
            Concrete\Core\Page\Collection\Version\Version::get($c, $cvID);
        }

        public static function getAttribute($ak, $c, $displayMode = null)
        {
            // Concrete\Core\Page\Collection\Version\Version::getAttribute();
            Concrete\Core\Page\Collection\Version\Version::getAttribute($ak, $c, $displayMode);
        }

        public static function isApproved()
        {
            // Concrete\Core\Page\Collection\Version\Version::isApproved();
            Concrete\Core\Page\Collection\Version\Version::isApproved();
        }

        public static function isMostRecent()
        {
            // Concrete\Core\Page\Collection\Version\Version::isMostRecent();
            Concrete\Core\Page\Collection\Version\Version::isMostRecent();
        }

        public static function isNew()
        {
            // Concrete\Core\Page\Collection\Version\Version::isNew();
            Concrete\Core\Page\Collection\Version\Version::isNew();
        }

        public static function getVersionID()
        {
            // Concrete\Core\Page\Collection\Version\Version::getVersionID();
            Concrete\Core\Page\Collection\Version\Version::getVersionID();
        }

        public static function getCollectionID()
        {
            // Concrete\Core\Page\Collection\Version\Version::getCollectionID();
            Concrete\Core\Page\Collection\Version\Version::getCollectionID();
        }

        public static function getVersionName()
        {
            // Concrete\Core\Page\Collection\Version\Version::getVersionName();
            Concrete\Core\Page\Collection\Version\Version::getVersionName();
        }

        public static function getVersionComments()
        {
            // Concrete\Core\Page\Collection\Version\Version::getVersionComments();
            Concrete\Core\Page\Collection\Version\Version::getVersionComments();
        }

        public static function getVersionAuthorUserID()
        {
            // Concrete\Core\Page\Collection\Version\Version::getVersionAuthorUserID();
            Concrete\Core\Page\Collection\Version\Version::getVersionAuthorUserID();
        }

        public static function getVersionApproverUserID()
        {
            // Concrete\Core\Page\Collection\Version\Version::getVersionApproverUserID();
            Concrete\Core\Page\Collection\Version\Version::getVersionApproverUserID();
        }

        public static function getVersionAuthorUserName()
        {
            // Concrete\Core\Page\Collection\Version\Version::getVersionAuthorUserName();
            Concrete\Core\Page\Collection\Version\Version::getVersionAuthorUserName();
        }

        public static function getVersionApproverUserName()
        {
            // Concrete\Core\Page\Collection\Version\Version::getVersionApproverUserName();
            Concrete\Core\Page\Collection\Version\Version::getVersionApproverUserName();
        }

        public static function getCustomAreaStyles()
        {
            // Concrete\Core\Page\Collection\Version\Version::getCustomAreaStyles();
            Concrete\Core\Page\Collection\Version\Version::getCustomAreaStyles();
        }

        /**
         * Gets the date the collection version was created
         * if user is specified, returns in the current user's timezone
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getVersionDateCreated($type = "system")
        {
            // Concrete\Core\Page\Collection\Version\Version::getVersionDateCreated();
            Concrete\Core\Page\Collection\Version\Version::getVersionDateCreated($type);
        }

        public static function canWrite()
        {
            // Concrete\Core\Page\Collection\Version\Version::canWrite();
            Concrete\Core\Page\Collection\Version\Version::canWrite();
        }

        public static function setComment($comment)
        {
            // Concrete\Core\Page\Collection\Version\Version::setComment();
            Concrete\Core\Page\Collection\Version\Version::setComment($comment);
        }

        public static function createNew($versionComments)
        {
            // Concrete\Core\Page\Collection\Version\Version::createNew();
            Concrete\Core\Page\Collection\Version\Version::createNew($versionComments);
        }

        public static function approve($doReindexImmediately = "1")
        {
            // Concrete\Core\Page\Collection\Version\Version::approve();
            Concrete\Core\Page\Collection\Version\Version::approve($doReindexImmediately);
        }

        public static function discard()
        {
            // Concrete\Core\Page\Collection\Version\Version::discard();
            Concrete\Core\Page\Collection\Version\Version::discard();
        }

        public static function canDiscard()
        {
            // Concrete\Core\Page\Collection\Version\Version::canDiscard();
            Concrete\Core\Page\Collection\Version\Version::canDiscard();
        }

        public static function removeNewStatus()
        {
            // Concrete\Core\Page\Collection\Version\Version::removeNewStatus();
            Concrete\Core\Page\Collection\Version\Version::removeNewStatus();
        }

        public static function deny()
        {
            // Concrete\Core\Page\Collection\Version\Version::deny();
            Concrete\Core\Page\Collection\Version\Version::deny();
        }

        public static function delete()
        {
            // Concrete\Core\Page\Collection\Version\Version::delete();
            Concrete\Core\Page\Collection\Version\Version::delete();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class Area extends \Concrete\Core\Area\Area
    {

        public static function setAreaDisplayName($arDisplayName)
        {
            // Concrete\Core\Area\Area::setAreaDisplayName();
            Concrete\Core\Area\Area::setAreaDisplayName($arDisplayName);
        }

        public static function setAreaGridColumnSpan($cspan)
        {
            // Concrete\Core\Area\Area::setAreaGridColumnSpan();
            Concrete\Core\Area\Area::setAreaGridColumnSpan($cspan);
        }

        public static function getAreaGridColumnSpan()
        {
            // Concrete\Core\Area\Area::getAreaGridColumnSpan();
            Concrete\Core\Area\Area::getAreaGridColumnSpan();
        }

        public static function getAreaDisplayName()
        {
            // Concrete\Core\Area\Area::getAreaDisplayName();
            Concrete\Core\Area\Area::getAreaDisplayName();
        }

        /**
         * The constructor is used primarily on page templates to create areas of content that are editable within the cms.
         * ex: $a = new Area('Main'); $a->display($c)
         * We actually use Collection::getArea() when we want to interact with a fully
         * qualified Area object when dealing with a Page/Collection object
         *
         * @param string
         * @return void
         */
        public static function __construct($arHandle)
        {
            // Concrete\Core\Area\Area::__construct();
            Concrete\Core\Area\Area::__construct($arHandle);
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\Area\Area::getPermissionObjectIdentifier();
            Concrete\Core\Area\Area::getPermissionObjectIdentifier();
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\Area\Area::getPermissionResponseClassName();
            Concrete\Core\Area\Area::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\Area\Area::getPermissionAssignmentClassName();
            Concrete\Core\Area\Area::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\Area\Area::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\Area\Area::getPermissionObjectKeyCategoryHandle();
        }

        /**
         * returns the Collection's cID
         * @return int
         */
        public static function getCollectionID()
        {
            // Concrete\Core\Area\Area::getCollectionID();
            Concrete\Core\Area\Area::getCollectionID();
        }

        /**
         * returns the Collection object for the current Area
         * @return Collection
         */
        public static function getAreaCollectionObject()
        {
            // Concrete\Core\Area\Area::getAreaCollectionObject();
            Concrete\Core\Area\Area::getAreaCollectionObject();
        }

        /**
         * whether or not it's a global area
         * @return bool
         */
        public static function isGlobalArea()
        {
            // Concrete\Core\Area\Area::isGlobalArea();
            Concrete\Core\Area\Area::isGlobalArea();
        }

        /**
         * returns the arID of the current area
         * @return int
         */
        public static function getAreaID()
        {
            // Concrete\Core\Area\Area::getAreaID();
            Concrete\Core\Area\Area::getAreaID();
        }

        /**
         * returns the handle for the current area
         * @return string
         */
        public static function getAreaHandle()
        {
            // Concrete\Core\Area\Area::getAreaHandle();
            Concrete\Core\Area\Area::getAreaHandle();
        }

        /**
         * returns an array of custom templates
         * @return array
         */
        public static function getCustomTemplates()
        {
            // Concrete\Core\Area\Area::getCustomTemplates();
            Concrete\Core\Area\Area::getCustomTemplates();
        }

        /**
         * sets a custom block template for blocks of a type specified by the btHandle
         * @param string $btHandle handle for the block type
         * @param string $view string identifying the block template ex: 'templates/breadcrumb.php'
         */
        public static function setCustomTemplate($btHandle, $view)
        {
            // Concrete\Core\Area\Area::setCustomTemplate();
            Concrete\Core\Area\Area::setCustomTemplate($btHandle, $view);
        }

        /**
         * Returns the total number of blocks in an area.
         * @param Page $c must be passed if the display() method has not been run on the area object yet.
         */
        public static function getTotalBlocksInArea($c = null)
        {
            // Concrete\Core\Area\Area::getTotalBlocksInArea();
            Concrete\Core\Area\Area::getTotalBlocksInArea($c);
        }

        /**
         * Returns the amount of actual blocks in the area, does not exclude core blocks or layouts, does not recurse.
         */
        public static function getTotalBlocksInAreaEditMode()
        {
            // Concrete\Core\Area\Area::getTotalBlocksInAreaEditMode();
            Concrete\Core\Area\Area::getTotalBlocksInAreaEditMode();
        }

        /**
         * check if the area has permissions that override the page's permissions
         * @return boolean
         */
        public static function overrideCollectionPermissions()
        {
            // Concrete\Core\Area\Area::overrideCollectionPermissions();
            Concrete\Core\Area\Area::overrideCollectionPermissions();
        }

        /**
         * @return int
         */
        public static function getAreaCollectionInheritID()
        {
            // Concrete\Core\Area\Area::getAreaCollectionInheritID();
            Concrete\Core\Area\Area::getAreaCollectionInheritID();
        }

        /**
         * Sets the total number of blocks an area allows. Does not limit by type.
         * @param int $num
         * @return void
         */
        public static function setBlockLimit($num)
        {
            // Concrete\Core\Area\Area::setBlockLimit();
            Concrete\Core\Area\Area::setBlockLimit($num);
        }

        /**
         * disables controls for the current area
         * @return void
         */
        public static function disableControls()
        {
            // Concrete\Core\Area\Area::disableControls();
            Concrete\Core\Area\Area::disableControls();
        }

        /**
         * gets the maximum allowed number of blocks, -1 if unlimited
         * @return int
         */
        public static function getMaximumBlocks()
        {
            // Concrete\Core\Area\Area::getMaximumBlocks();
            Concrete\Core\Area\Area::getMaximumBlocks();
        }

        /**
         *
         * @return string
         */
        public static function getAreaUpdateAction($task = "update", $alternateHandler = null)
        {
            // Concrete\Core\Area\Area::getAreaUpdateAction();
            Concrete\Core\Area\Area::getAreaUpdateAction($task, $alternateHandler);
        }

        /**
         * Gets the Area object for the given page and area handle
         * @param Page|Collection $c
         * @param string $arHandle
         * @param int|null $arIsGlobal
         * @return Area
         */
        final public static function get(&$c, $arHandle)
        {
            // Concrete\Core\Area\Area::get();
            Concrete\Core\Area\Area::get($c, $arHandle);
        }

        /**
         * Creates an area in the database. I would like to make this static but PHP pre 5.3 sucks at this stuff.
         */
        public static function create($c, $arHandle)
        {
            // Concrete\Core\Area\Area::create();
            Concrete\Core\Area\Area::create($c, $arHandle);
        }

        public static function getAreaHandleFromID($arID)
        {
            // Concrete\Core\Area\Area::getAreaHandleFromID();
            Concrete\Core\Area\Area::getAreaHandleFromID($arID);
        }

        /**
         * Get all of the blocks within the current area for a given page
         * @param Page|Collection $c
         * @return Block[]
         */
        public static function getAreaBlocksArray($c = null)
        {
            // Concrete\Core\Area\Area::getAreaBlocksArray();
            Concrete\Core\Area\Area::getAreaBlocksArray($c);
        }

        /**
         * gets a list of all areas - no relation to the current page or area object
         * possibly could be set as a static method??
         * @return array
         */
        public static function getHandleList()
        {
            // Concrete\Core\Area\Area::getHandleList();
            Concrete\Core\Area\Area::getHandleList();
        }

        public static function getListOnPage(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Area\Area::getListOnPage();
            Concrete\Core\Area\Area::getListOnPage($c);
        }

        /**
         * This function removes all permissions records for the current Area
         * and sets it to inherit from the Page permissions
         * @return void
         */
        public static function revertToPagePermissions()
        {
            // Concrete\Core\Area\Area::revertToPagePermissions();
            Concrete\Core\Area\Area::revertToPagePermissions();
        }

        public static function __destruct()
        {
            // Concrete\Core\Area\Area::__destruct();
            Concrete\Core\Area\Area::__destruct();
        }

        /**
         * Rescans the current Area's permissions ensuring that it's enheriting permissions properly up the chain
         * @return void
         */
        public static function rescanAreaPermissionsChain()
        {
            // Concrete\Core\Area\Area::rescanAreaPermissionsChain();
            Concrete\Core\Area\Area::rescanAreaPermissionsChain();
        }

        /**
         * works a lot like rescanAreaPermissionsChain() but it works down. This is typically only
         * called when we update an area to have specific permissions, and all areas that are on pagesbelow it with the same
         * handle, etc... should now inherit from it.
         * @return void
         */
        public static function rescanSubAreaPermissions($cIDToCheck = null)
        {
            // Concrete\Core\Area\Area::rescanSubAreaPermissions();
            Concrete\Core\Area\Area::rescanSubAreaPermissions($cIDToCheck);
        }

        /**
         * similar to rescanSubAreaPermissions, but for those who have setup their pages to inherit master collection permissions
         * @see Area::rescanSubAreaPermissions()
         * @return void
         */
        public static function rescanSubAreaPermissionsMasterCollection($masterCollection)
        {
            // Concrete\Core\Area\Area::rescanSubAreaPermissionsMasterCollection();
            Concrete\Core\Area\Area::rescanSubAreaPermissionsMasterCollection($masterCollection);
        }

        public static function getOrCreate($c, $arHandle)
        {
            // Concrete\Core\Area\Area::getOrCreate();
            Concrete\Core\Area\Area::getOrCreate($c, $arHandle);
        }

        public static function load($c)
        {
            // Concrete\Core\Area\Area::load();
            Concrete\Core\Area\Area::load($c);
        }

        protected static function getAreaBlocks()
        {
            // Concrete\Core\Area\Area::getAreaBlocks();
            Concrete\Core\Area\Area::getAreaBlocks();
        }

        /**
         * displays the Area in the page
         * ex: $a = new Area('Main'); $a->display($c);
         * @param Page|Collection $c
         * @param Block[] $alternateBlockArray optional array of blocks to render instead of default behavior
         * @return void
         */
        public static function display($c, $alternateBlockArray = null)
        {
            // Concrete\Core\Area\Area::display();
            Concrete\Core\Area\Area::display($c, $alternateBlockArray);
        }

        /**
         * Exports the area to content format
         * @todo need more documentation export?
         */
        public static function export($p, $page)
        {
            // Concrete\Core\Area\Area::export();
            Concrete\Core\Area\Area::export($p, $page);
        }

        /**
         * Specify HTML to automatically print before blocks contained within the area
         * @param string $html
         * @return void
         */
        public static function setBlockWrapperStart($html)
        {
            // Concrete\Core\Area\Area::setBlockWrapperStart();
            Concrete\Core\Area\Area::setBlockWrapperStart($html);
        }

        /**
         * Set HTML that automatically prints after any blocks contained within the area
         * @param string $html
         * @return void
         */
        public static function setBlockWrapperEnd($html)
        {
            // Concrete\Core\Area\Area::setBlockWrapperEnd();
            Concrete\Core\Area\Area::setBlockWrapperEnd($html);
        }

        public static function overridePagePermissions()
        {
            // Concrete\Core\Area\Area::overridePagePermissions();
            Concrete\Core\Area\Area::overridePagePermissions();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class GlobalArea extends \Concrete\Core\Area\GlobalArea
    {

        public static function isGlobalArea()
        {
            // Concrete\Core\Area\GlobalArea::isGlobalArea();
            Concrete\Core\Area\GlobalArea::isGlobalArea();
        }

        public static function create($c, $arHandle)
        {
            // Concrete\Core\Area\GlobalArea::create();
            Concrete\Core\Area\GlobalArea::create($c, $arHandle);
        }

        public static function getAreaDisplayName()
        {
            // Concrete\Core\Area\GlobalArea::getAreaDisplayName();
            Concrete\Core\Area\GlobalArea::getAreaDisplayName();
        }

        public static function getTotalBlocksInArea()
        {
            // Concrete\Core\Area\GlobalArea::getTotalBlocksInArea();
            Concrete\Core\Area\GlobalArea::getTotalBlocksInArea();
        }

        protected static function getGlobalAreaStackObject()
        {
            // Concrete\Core\Area\GlobalArea::getGlobalAreaStackObject();
            Concrete\Core\Area\GlobalArea::getGlobalAreaStackObject();
        }

        public static function getTotalBlocksInAreaEditMode()
        {
            // Concrete\Core\Area\GlobalArea::getTotalBlocksInAreaEditMode();
            Concrete\Core\Area\GlobalArea::getTotalBlocksInAreaEditMode();
        }

        public static function getAreaBlocks()
        {
            // Concrete\Core\Area\GlobalArea::getAreaBlocks();
            Concrete\Core\Area\GlobalArea::getAreaBlocks();
        }

        public static function display()
        {
            // Concrete\Core\Area\GlobalArea::display();
            Concrete\Core\Area\GlobalArea::display();
        }

        /**
         * Note that this function does not delete the global area's stack.
         * You probably want to call the "delete" method of the Stack model instead.
         */
        public static function deleteByName($arHandle)
        {
            // Concrete\Core\Area\GlobalArea::deleteByName();
            Concrete\Core\Area\GlobalArea::deleteByName($arHandle);
        }

        public static function setAreaDisplayName($arDisplayName)
        {
            // Concrete\Core\Area\Area::setAreaDisplayName();
            Concrete\Core\Area\Area::setAreaDisplayName($arDisplayName);
        }

        public static function setAreaGridColumnSpan($cspan)
        {
            // Concrete\Core\Area\Area::setAreaGridColumnSpan();
            Concrete\Core\Area\Area::setAreaGridColumnSpan($cspan);
        }

        public static function getAreaGridColumnSpan()
        {
            // Concrete\Core\Area\Area::getAreaGridColumnSpan();
            Concrete\Core\Area\Area::getAreaGridColumnSpan();
        }

        /**
         * The constructor is used primarily on page templates to create areas of content that are editable within the cms.
         * ex: $a = new Area('Main'); $a->display($c)
         * We actually use Collection::getArea() when we want to interact with a fully
         * qualified Area object when dealing with a Page/Collection object
         *
         * @param string
         * @return void
         */
        public static function __construct($arHandle)
        {
            // Concrete\Core\Area\Area::__construct();
            Concrete\Core\Area\Area::__construct($arHandle);
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\Area\Area::getPermissionObjectIdentifier();
            Concrete\Core\Area\Area::getPermissionObjectIdentifier();
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\Area\Area::getPermissionResponseClassName();
            Concrete\Core\Area\Area::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\Area\Area::getPermissionAssignmentClassName();
            Concrete\Core\Area\Area::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\Area\Area::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\Area\Area::getPermissionObjectKeyCategoryHandle();
        }

        /**
         * returns the Collection's cID
         * @return int
         */
        public static function getCollectionID()
        {
            // Concrete\Core\Area\Area::getCollectionID();
            Concrete\Core\Area\Area::getCollectionID();
        }

        /**
         * returns the Collection object for the current Area
         * @return Collection
         */
        public static function getAreaCollectionObject()
        {
            // Concrete\Core\Area\Area::getAreaCollectionObject();
            Concrete\Core\Area\Area::getAreaCollectionObject();
        }

        /**
         * returns the arID of the current area
         * @return int
         */
        public static function getAreaID()
        {
            // Concrete\Core\Area\Area::getAreaID();
            Concrete\Core\Area\Area::getAreaID();
        }

        /**
         * returns the handle for the current area
         * @return string
         */
        public static function getAreaHandle()
        {
            // Concrete\Core\Area\Area::getAreaHandle();
            Concrete\Core\Area\Area::getAreaHandle();
        }

        /**
         * returns an array of custom templates
         * @return array
         */
        public static function getCustomTemplates()
        {
            // Concrete\Core\Area\Area::getCustomTemplates();
            Concrete\Core\Area\Area::getCustomTemplates();
        }

        /**
         * sets a custom block template for blocks of a type specified by the btHandle
         * @param string $btHandle handle for the block type
         * @param string $view string identifying the block template ex: 'templates/breadcrumb.php'
         */
        public static function setCustomTemplate($btHandle, $view)
        {
            // Concrete\Core\Area\Area::setCustomTemplate();
            Concrete\Core\Area\Area::setCustomTemplate($btHandle, $view);
        }

        /**
         * check if the area has permissions that override the page's permissions
         * @return boolean
         */
        public static function overrideCollectionPermissions()
        {
            // Concrete\Core\Area\Area::overrideCollectionPermissions();
            Concrete\Core\Area\Area::overrideCollectionPermissions();
        }

        /**
         * @return int
         */
        public static function getAreaCollectionInheritID()
        {
            // Concrete\Core\Area\Area::getAreaCollectionInheritID();
            Concrete\Core\Area\Area::getAreaCollectionInheritID();
        }

        /**
         * Sets the total number of blocks an area allows. Does not limit by type.
         * @param int $num
         * @return void
         */
        public static function setBlockLimit($num)
        {
            // Concrete\Core\Area\Area::setBlockLimit();
            Concrete\Core\Area\Area::setBlockLimit($num);
        }

        /**
         * disables controls for the current area
         * @return void
         */
        public static function disableControls()
        {
            // Concrete\Core\Area\Area::disableControls();
            Concrete\Core\Area\Area::disableControls();
        }

        /**
         * gets the maximum allowed number of blocks, -1 if unlimited
         * @return int
         */
        public static function getMaximumBlocks()
        {
            // Concrete\Core\Area\Area::getMaximumBlocks();
            Concrete\Core\Area\Area::getMaximumBlocks();
        }

        /**
         *
         * @return string
         */
        public static function getAreaUpdateAction($task = "update", $alternateHandler = null)
        {
            // Concrete\Core\Area\Area::getAreaUpdateAction();
            Concrete\Core\Area\Area::getAreaUpdateAction($task, $alternateHandler);
        }

        /**
         * Gets the Area object for the given page and area handle
         * @param Page|Collection $c
         * @param string $arHandle
         * @param int|null $arIsGlobal
         * @return Area
         */
        final public static function get(&$c, $arHandle)
        {
            // Concrete\Core\Area\Area::get();
            Concrete\Core\Area\Area::get($c, $arHandle);
        }

        public static function getAreaHandleFromID($arID)
        {
            // Concrete\Core\Area\Area::getAreaHandleFromID();
            Concrete\Core\Area\Area::getAreaHandleFromID($arID);
        }

        /**
         * Get all of the blocks within the current area for a given page
         * @param Page|Collection $c
         * @return Block[]
         */
        public static function getAreaBlocksArray($c = null)
        {
            // Concrete\Core\Area\Area::getAreaBlocksArray();
            Concrete\Core\Area\Area::getAreaBlocksArray($c);
        }

        /**
         * gets a list of all areas - no relation to the current page or area object
         * possibly could be set as a static method??
         * @return array
         */
        public static function getHandleList()
        {
            // Concrete\Core\Area\Area::getHandleList();
            Concrete\Core\Area\Area::getHandleList();
        }

        public static function getListOnPage(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\Area\Area::getListOnPage();
            Concrete\Core\Area\Area::getListOnPage($c);
        }

        /**
         * This function removes all permissions records for the current Area
         * and sets it to inherit from the Page permissions
         * @return void
         */
        public static function revertToPagePermissions()
        {
            // Concrete\Core\Area\Area::revertToPagePermissions();
            Concrete\Core\Area\Area::revertToPagePermissions();
        }

        public static function __destruct()
        {
            // Concrete\Core\Area\Area::__destruct();
            Concrete\Core\Area\Area::__destruct();
        }

        /**
         * Rescans the current Area's permissions ensuring that it's enheriting permissions properly up the chain
         * @return void
         */
        public static function rescanAreaPermissionsChain()
        {
            // Concrete\Core\Area\Area::rescanAreaPermissionsChain();
            Concrete\Core\Area\Area::rescanAreaPermissionsChain();
        }

        /**
         * works a lot like rescanAreaPermissionsChain() but it works down. This is typically only
         * called when we update an area to have specific permissions, and all areas that are on pagesbelow it with the same
         * handle, etc... should now inherit from it.
         * @return void
         */
        public static function rescanSubAreaPermissions($cIDToCheck = null)
        {
            // Concrete\Core\Area\Area::rescanSubAreaPermissions();
            Concrete\Core\Area\Area::rescanSubAreaPermissions($cIDToCheck);
        }

        /**
         * similar to rescanSubAreaPermissions, but for those who have setup their pages to inherit master collection permissions
         * @see Area::rescanSubAreaPermissions()
         * @return void
         */
        public static function rescanSubAreaPermissionsMasterCollection($masterCollection)
        {
            // Concrete\Core\Area\Area::rescanSubAreaPermissionsMasterCollection();
            Concrete\Core\Area\Area::rescanSubAreaPermissionsMasterCollection($masterCollection);
        }

        public static function getOrCreate($c, $arHandle)
        {
            // Concrete\Core\Area\Area::getOrCreate();
            Concrete\Core\Area\Area::getOrCreate($c, $arHandle);
        }

        public static function load($c)
        {
            // Concrete\Core\Area\Area::load();
            Concrete\Core\Area\Area::load($c);
        }

        /**
         * Exports the area to content format
         * @todo need more documentation export?
         */
        public static function export($p, $page)
        {
            // Concrete\Core\Area\Area::export();
            Concrete\Core\Area\Area::export($p, $page);
        }

        /**
         * Specify HTML to automatically print before blocks contained within the area
         * @param string $html
         * @return void
         */
        public static function setBlockWrapperStart($html)
        {
            // Concrete\Core\Area\Area::setBlockWrapperStart();
            Concrete\Core\Area\Area::setBlockWrapperStart($html);
        }

        /**
         * Set HTML that automatically prints after any blocks contained within the area
         * @param string $html
         * @return void
         */
        public static function setBlockWrapperEnd($html)
        {
            // Concrete\Core\Area\Area::setBlockWrapperEnd();
            Concrete\Core\Area\Area::setBlockWrapperEnd($html);
        }

        public static function overridePagePermissions()
        {
            // Concrete\Core\Area\Area::overridePagePermissions();
            Concrete\Core\Area\Area::overridePagePermissions();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class Stack extends \Concrete\Core\Page\Stack\Stack
    {

        public static function getStackName()
        {
            // Concrete\Core\Page\Stack\Stack::getStackName();
            Concrete\Core\Page\Stack\Stack::getStackName();
        }

        public static function getStackType()
        {
            // Concrete\Core\Page\Stack\Stack::getStackType();
            Concrete\Core\Page\Stack\Stack::getStackType();
        }

        public static function getStackTypeExportText()
        {
            // Concrete\Core\Page\Stack\Stack::getStackTypeExportText();
            Concrete\Core\Page\Stack\Stack::getStackTypeExportText();
        }

        public static function mapImportTextToType($type)
        {
            // Concrete\Core\Page\Stack\Stack::mapImportTextToType();
            Concrete\Core\Page\Stack\Stack::mapImportTextToType($type);
        }

        protected static function isValidStack($stack)
        {
            // Concrete\Core\Page\Stack\Stack::isValidStack();
            Concrete\Core\Page\Stack\Stack::isValidStack($stack);
        }

        public static function addStack($stackName, $type = null)
        {
            // Concrete\Core\Page\Stack\Stack::addStack();
            Concrete\Core\Page\Stack\Stack::addStack($stackName, $type);
        }

        public static function duplicate($nc = null, $preserveUserID = null)
        {
            // Concrete\Core\Page\Stack\Stack::duplicate();
            Concrete\Core\Page\Stack\Stack::duplicate($nc, $preserveUserID);
        }

        public static function getByName($stackName, $cvID = "RECENT")
        {
            // Concrete\Core\Page\Stack\Stack::getByName();
            Concrete\Core\Page\Stack\Stack::getByName($stackName, $cvID);
        }

        public static function update($data)
        {
            // Concrete\Core\Page\Stack\Stack::update();
            Concrete\Core\Page\Stack\Stack::update($data);
        }

        public static function delete()
        {
            // Concrete\Core\Page\Stack\Stack::delete();
            Concrete\Core\Page\Stack\Stack::delete();
        }

        public static function display()
        {
            // Concrete\Core\Page\Stack\Stack::display();
            Concrete\Core\Page\Stack\Stack::display();
        }

        public static function getOrCreateGlobalArea($stackName)
        {
            // Concrete\Core\Page\Stack\Stack::getOrCreateGlobalArea();
            Concrete\Core\Page\Stack\Stack::getOrCreateGlobalArea($stackName);
        }

        public static function getByID($cID, $cvID = "RECENT")
        {
            // Concrete\Core\Page\Stack\Stack::getByID();
            Concrete\Core\Page\Stack\Stack::getByID($cID, $cvID);
        }

        public static function export($pageNode)
        {
            // Concrete\Core\Page\Stack\Stack::export();
            Concrete\Core\Page\Stack\Stack::export($pageNode);
        }

        /**
         * @param string $path /path/to/page
         * @param string $version ACTIVE or RECENT
         * @return Page
         */
        public static function getByPath($path, $version = "RECENT")
        {
            // Concrete\Core\Page\Page::getByPath();
            Concrete\Core\Page\Page::getByPath($path, $version);
        }

        public static function __construct()
        {
            // Concrete\Core\Page\Page::__construct();
            Concrete\Core\Page\Page::__construct();
        }

        /**
         * @access private
         */
        protected static function populatePage($cInfo, $where, $cvID)
        {
            // Concrete\Core\Page\Page::populatePage();
            Concrete\Core\Page\Page::populatePage($cInfo, $where, $cvID);
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\Page\Page::getPermissionResponseClassName();
            Concrete\Core\Page\Page::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\Page\Page::getPermissionAssignmentClassName();
            Concrete\Core\Page\Page::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\Page\Page::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\Page\Page::getPermissionObjectKeyCategoryHandle();
        }

        public static function getPageController()
        {
            // Concrete\Core\Page\Page::getPageController();
            Concrete\Core\Page\Page::getPageController();
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\Page\Page::getPermissionObjectIdentifier();
            Concrete\Core\Page\Page::getPermissionObjectIdentifier();
        }

        /**
         * Returns 1 if the page is in edit mode
         * @return bool
         */
        public static function isEditMode()
        {
            // Concrete\Core\Page\Page::isEditMode();
            Concrete\Core\Page\Page::isEditMode();
        }

        /**
         * Get the package ID for a page (page thats added by a package) (returns 0 if its not in a package)
         * @return int
         */
        public static function getPackageID()
        {
            // Concrete\Core\Page\Page::getPackageID();
            Concrete\Core\Page\Page::getPackageID();
        }

        /**
         * Get the package handle for a page (page thats added by a package)
         * @return string
         */
        public static function getPackageHandle()
        {
            // Concrete\Core\Page\Page::getPackageHandle();
            Concrete\Core\Page\Page::getPackageHandle();
        }

        /**
         * Returns 1 if the page is in arrange mode
         * @return bool
         */
        public static function isArrangeMode()
        {
            // Concrete\Core\Page\Page::isArrangeMode();
            Concrete\Core\Page\Page::isArrangeMode();
        }

        /**
         * Forces the page to be checked in if its checked out
         */
        public static function forceCheckIn()
        {
            // Concrete\Core\Page\Page::forceCheckIn();
            Concrete\Core\Page\Page::forceCheckIn();
        }

        /**
         * Checks if the page is a dashboard page, returns true if it is
         * @return bool
         */
        public static function isAdminArea()
        {
            // Concrete\Core\Page\Page::isAdminArea();
            Concrete\Core\Page\Page::isAdminArea();
        }

        /**
         * Uses a Request object to determine which page to load. queries by path and then
         * by cID
         */
        public static function getFromRequest(Concrete\Core\Http\Request $request)
        {
            // Concrete\Core\Page\Page::getFromRequest();
            Concrete\Core\Page\Page::getFromRequest($request);
        }

        public static function processArrangement($area_id, $moved_block_id, $block_order)
        {
            // Concrete\Core\Page\Page::processArrangement();
            Concrete\Core\Page\Page::processArrangement($area_id, $moved_block_id, $block_order);
        }

        /**
         * checks if the page is checked out, if it is return true
         * @return bool
         */
        public static function isCheckedOut()
        {
            // Concrete\Core\Page\Page::isCheckedOut();
            Concrete\Core\Page\Page::isCheckedOut();
        }

        /**
         * Gets the user that is editing the current page.
         * $return string $name
         */
        public static function getCollectionCheckedOutUserName()
        {
            // Concrete\Core\Page\Page::getCollectionCheckedOutUserName();
            Concrete\Core\Page\Page::getCollectionCheckedOutUserName();
        }

        /**
         * Checks if the page is checked out by the current user
         * @return bool
         */
        public static function isCheckedOutByMe()
        {
            // Concrete\Core\Page\Page::isCheckedOutByMe();
            Concrete\Core\Page\Page::isCheckedOutByMe();
        }

        /**
         * Checks if the page is a single page
         * @return bool
         */
        public static function isGeneratedCollection()
        {
            // Concrete\Core\Page\Page::isGeneratedCollection();
            Concrete\Core\Page\Page::isGeneratedCollection();
        }

        public static function assignPermissions($userOrGroup, $permissions = null, $accessType = 10)
        {
            // Concrete\Core\Page\Page::assignPermissions();
            Concrete\Core\Page\Page::assignPermissions($userOrGroup, $permissions, $accessType);
        }

        public static function getDrafts()
        {
            // Concrete\Core\Page\Page::getDrafts();
            Concrete\Core\Page\Page::getDrafts();
        }

        public static function isPageDraft()
        {
            // Concrete\Core\Page\Page::isPageDraft();
            Concrete\Core\Page\Page::isPageDraft();
        }

        public static function setController($controller)
        {
            // Concrete\Core\Page\Page::setController();
            Concrete\Core\Page\Page::setController($controller);
        }

        /**
         * @deprecated
         */
        public static function getController()
        {
            // Concrete\Core\Page\Page::getController();
            Concrete\Core\Page\Page::getController();
        }

        /**
         * @private
         */
        public static function assignPermissionSet($px)
        {
            // Concrete\Core\Page\Page::assignPermissionSet();
            Concrete\Core\Page\Page::assignPermissionSet($px);
        }

        /**
         * Make an alias to a page
         * @param Collection $c
         * @return int $newCID
         */
        public static function addCollectionAlias($c)
        {
            // Concrete\Core\Page\Page::addCollectionAlias();
            Concrete\Core\Page\Page::addCollectionAlias($c);
        }

        /**
         * Update the name, link, and to open in a new window for an external link
         * @param string $cName
         * @param string $cLink
         * @param bool $newWindow
         */
        public static function updateCollectionAliasExternal($cName, $cLink, $newWindow = null)
        {
            // Concrete\Core\Page\Page::updateCollectionAliasExternal();
            Concrete\Core\Page\Page::updateCollectionAliasExternal($cName, $cLink, $newWindow);
        }

        /**
         * Add a new external link
         * @param string $cName
         * @param string $cLink
         * @param bool $newWindow
         * @return int $newCID
         */
        public static function addCollectionAliasExternal($cName, $cLink, $newWindow = null)
        {
            // Concrete\Core\Page\Page::addCollectionAliasExternal();
            Concrete\Core\Page\Page::addCollectionAliasExternal($cName, $cLink, $newWindow);
        }

        /**
         * Check if a page is a single page that is in the core (/concrete directory)
         * @return bool
         */
        public static function isSystemPage()
        {
            // Concrete\Core\Page\Page::isSystemPage();
            Concrete\Core\Page\Page::isSystemPage();
        }

        /**
         * Gets the icon for a page (also fires the on_page_get_icon event)
         * @return string $icon Path to the icon
         */
        public static function getCollectionIcon()
        {
            // Concrete\Core\Page\Page::getCollectionIcon();
            Concrete\Core\Page\Page::getCollectionIcon();
        }

        /**
         * Remove an external link/alias
         * @return int $cIDRedir cID for the original page if the page was an alias
         */
        public static function removeThisAlias()
        {
            // Concrete\Core\Page\Page::removeThisAlias();
            Concrete\Core\Page\Page::removeThisAlias();
        }

        public static function populateRecursivePages($pages, $pageRow, $cParentID, $level, $includeThisPage = "1")
        {
            // Concrete\Core\Page\Page::populateRecursivePages();
            Concrete\Core\Page\Page::populateRecursivePages($pages, $pageRow, $cParentID, $level, $includeThisPage);
        }

        public static function queueForDeletionSort($a, $b)
        {
            // Concrete\Core\Page\Page::queueForDeletionSort();
            Concrete\Core\Page\Page::queueForDeletionSort($a, $b);
        }

        public static function queueForDuplicationSort($a, $b)
        {
            // Concrete\Core\Page\Page::queueForDuplicationSort();
            Concrete\Core\Page\Page::queueForDuplicationSort($a, $b);
        }

        public static function queueForDeletion()
        {
            // Concrete\Core\Page\Page::queueForDeletion();
            Concrete\Core\Page\Page::queueForDeletion();
        }

        public static function queueForDeletionRequest()
        {
            // Concrete\Core\Page\Page::queueForDeletionRequest();
            Concrete\Core\Page\Page::queueForDeletionRequest();
        }

        public static function queueForDuplication($destination, $includeParent = "1")
        {
            // Concrete\Core\Page\Page::queueForDuplication();
            Concrete\Core\Page\Page::queueForDuplication($destination, $includeParent);
        }

        /**
         * Returns the uID for a page that is checked out
         * @return int
         */
        public static function getCollectionCheckedOutUserID()
        {
            // Concrete\Core\Page\Page::getCollectionCheckedOutUserID();
            Concrete\Core\Page\Page::getCollectionCheckedOutUserID();
        }

        /**
         * Returns the path for the current page
         * @return string
         */
        public static function getCollectionPath()
        {
            // Concrete\Core\Page\Page::getCollectionPath();
            Concrete\Core\Page\Page::getCollectionPath();
        }

        /**
         * Returns full url for the current page
         * @return string
         */
        public static function getCollectionLink($appendBaseURL = null, $ignoreUrlRewriting = null)
        {
            // Concrete\Core\Page\Page::getCollectionLink();
            Concrete\Core\Page\Page::getCollectionLink($appendBaseURL, $ignoreUrlRewriting);
        }

        /**
         * Returns the path for a page from its cID
         * @param int cID
         * @return string $path
         */
        public static function getCollectionPathFromID($cID)
        {
            // Concrete\Core\Page\Page::getCollectionPathFromID();
            Concrete\Core\Page\Page::getCollectionPathFromID($cID);
        }

        /**
         * Returns the uID for a page ownder
         * @return int
         */
        public static function getCollectionUserID()
        {
            // Concrete\Core\Page\Page::getCollectionUserID();
            Concrete\Core\Page\Page::getCollectionUserID();
        }

        /**
         * Returns the page's handle
         * @return string
         */
        public static function getCollectionHandle()
        {
            // Concrete\Core\Page\Page::getCollectionHandle();
            Concrete\Core\Page\Page::getCollectionHandle();
        }

        /**
         * @deprecated
         */
        public static function getCollectionTypeName()
        {
            // Concrete\Core\Page\Page::getCollectionTypeName();
            Concrete\Core\Page\Page::getCollectionTypeName();
        }

        public static function getPageTypeName()
        {
            // Concrete\Core\Page\Page::getPageTypeName();
            Concrete\Core\Page\Page::getPageTypeName();
        }

        /**
         * @deprecated
         */
        public static function getCollectionTypeID()
        {
            // Concrete\Core\Page\Page::getCollectionTypeID();
            Concrete\Core\Page\Page::getCollectionTypeID();
        }

        /**
         * Returns the Collection Type ID
         * @return int
         */
        public static function getPageTypeID()
        {
            // Concrete\Core\Page\Page::getPageTypeID();
            Concrete\Core\Page\Page::getPageTypeID();
        }

        public static function getPageTypeObject()
        {
            // Concrete\Core\Page\Page::getPageTypeObject();
            Concrete\Core\Page\Page::getPageTypeObject();
        }

        /**
         * Returns the Page Template ID
         * @return int
         */
        public static function getPageTemplateID()
        {
            // Concrete\Core\Page\Page::getPageTemplateID();
            Concrete\Core\Page\Page::getPageTemplateID();
        }

        /**
         * Returns the Collection Type handle
         * @return string
         */
        public static function getPageTypeHandle()
        {
            // Concrete\Core\Page\Page::getPageTypeHandle();
            Concrete\Core\Page\Page::getPageTypeHandle();
        }

        public static function getCollectionTypeHandle()
        {
            // Concrete\Core\Page\Page::getCollectionTypeHandle();
            Concrete\Core\Page\Page::getCollectionTypeHandle();
        }

        /**
         * Returns theme id for the collection
         * @return int
         */
        public static function getCollectionThemeID()
        {
            // Concrete\Core\Page\Page::getCollectionThemeID();
            Concrete\Core\Page\Page::getCollectionThemeID();
        }

        /**
         * Check if a block is an alias from a page default
         * @param Block $b
         * @return bool
         */
        public static function isBlockAliasedFromMasterCollection($b)
        {
            // Concrete\Core\Page\Page::isBlockAliasedFromMasterCollection();
            Concrete\Core\Page\Page::isBlockAliasedFromMasterCollection($b);
        }

        /**
         * Returns Collection's theme object
         * @return PageTheme
         */
        public static function getCollectionThemeObject()
        {
            // Concrete\Core\Page\Page::getCollectionThemeObject();
            Concrete\Core\Page\Page::getCollectionThemeObject();
        }

        /**
         * Returns the page's name
         * @return string
         */
        public static function getCollectionName()
        {
            // Concrete\Core\Page\Page::getCollectionName();
            Concrete\Core\Page\Page::getCollectionName();
        }

        /**
         * Returns the collection ID for the aliased page (returns 0 unless used on an actual alias)
         * @return int
         */
        public static function getCollectionPointerID()
        {
            // Concrete\Core\Page\Page::getCollectionPointerID();
            Concrete\Core\Page\Page::getCollectionPointerID();
        }

        /**
         * Returns link for the aliased page
         * @return string
         */
        public static function getCollectionPointerExternalLink()
        {
            // Concrete\Core\Page\Page::getCollectionPointerExternalLink();
            Concrete\Core\Page\Page::getCollectionPointerExternalLink();
        }

        /**
         * Returns if the alias opens in a new window
         * @return bool
         */
        public static function openCollectionPointerExternalLinkInNewWindow()
        {
            // Concrete\Core\Page\Page::openCollectionPointerExternalLinkInNewWindow();
            Concrete\Core\Page\Page::openCollectionPointerExternalLinkInNewWindow();
        }

        /**
         * Checks to see if the page is an alias
         * @return bool
         */
        public static function isAlias()
        {
            // Concrete\Core\Page\Page::isAlias();
            Concrete\Core\Page\Page::isAlias();
        }

        /**
         * Checks if a page is an external link
         * @return bool
         */
        public static function isExternalLink()
        {
            // Concrete\Core\Page\Page::isExternalLink();
            Concrete\Core\Page\Page::isExternalLink();
        }

        /**
         * Get the original cID of a page
         * @return int
         */
        public static function getCollectionPointerOriginalID()
        {
            // Concrete\Core\Page\Page::getCollectionPointerOriginalID();
            Concrete\Core\Page\Page::getCollectionPointerOriginalID();
        }

        /**
         * Get the file name of a page (single pages)
         * @return string
         */
        public static function getCollectionFilename()
        {
            // Concrete\Core\Page\Page::getCollectionFilename();
            Concrete\Core\Page\Page::getCollectionFilename();
        }

        /**
         * Gets the date a the current version was made public,
         * if user is specified, returns in the current user's timezone
         * @param string $mask
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getCollectionDatePublic($mask = null, $type = "system")
        {
            // Concrete\Core\Page\Page::getCollectionDatePublic();
            Concrete\Core\Page\Page::getCollectionDatePublic($mask, $type);
        }

        /**
         * Get the description of a page
         * @return string
         */
        public static function getCollectionDescription()
        {
            // Concrete\Core\Page\Page::getCollectionDescription();
            Concrete\Core\Page\Page::getCollectionDescription();
        }

        /**
         * Gets the cID of the page's parent
         * @return int
         */
        public static function getCollectionParentID()
        {
            // Concrete\Core\Page\Page::getCollectionParentID();
            Concrete\Core\Page\Page::getCollectionParentID();
        }

        /**
         * Get the Parent cID from a page by using a cID
         * @param int $cID
         * @return int
         */
        public static function getCollectionParentIDFromChildID($cID)
        {
            // Concrete\Core\Page\Page::getCollectionParentIDFromChildID();
            Concrete\Core\Page\Page::getCollectionParentIDFromChildID($cID);
        }

        /**
         * Returns an array of this cParentID and aliased parentIDs
         * @return array $cID
         */
        public static function getCollectionParentIDs()
        {
            // Concrete\Core\Page\Page::getCollectionParentIDs();
            Concrete\Core\Page\Page::getCollectionParentIDs();
        }

        /**
         * Checks if a page is a page default
         * @return bool
         */
        public static function isMasterCollection()
        {
            // Concrete\Core\Page\Page::isMasterCollection();
            Concrete\Core\Page\Page::isMasterCollection();
        }

        /**
         * Gets the template permissions
         * @return string
         */
        public static function overrideTemplatePermissions()
        {
            // Concrete\Core\Page\Page::overrideTemplatePermissions();
            Concrete\Core\Page\Page::overrideTemplatePermissions();
        }

        /**
         * Gets the position of the page in the sitemap
         * @return int
         */
        public static function getCollectionDisplayOrder()
        {
            // Concrete\Core\Page\Page::getCollectionDisplayOrder();
            Concrete\Core\Page\Page::getCollectionDisplayOrder();
        }

        /**
         * Set the theme for a page using the page object
         * @param PageTheme $pl
         */
        public static function setTheme($pl)
        {
            // Concrete\Core\Page\Page::setTheme();
            Concrete\Core\Page\Page::setTheme($pl);
        }

        /**
         * Set the permissions of sub-collections added beneath this permissions to inherit from the template
         */
        public static function setPermissionsInheritanceToTemplate()
        {
            // Concrete\Core\Page\Page::setPermissionsInheritanceToTemplate();
            Concrete\Core\Page\Page::setPermissionsInheritanceToTemplate();
        }

        /**
         * Set the permissions of sub-collections added beneath this permissions to inherit from the parent
         */
        public static function setPermissionsInheritanceToOverride()
        {
            // Concrete\Core\Page\Page::setPermissionsInheritanceToOverride();
            Concrete\Core\Page\Page::setPermissionsInheritanceToOverride();
        }

        public static function getPermissionsCollectionID()
        {
            // Concrete\Core\Page\Page::getPermissionsCollectionID();
            Concrete\Core\Page\Page::getPermissionsCollectionID();
        }

        public static function getCollectionInheritance()
        {
            // Concrete\Core\Page\Page::getCollectionInheritance();
            Concrete\Core\Page\Page::getCollectionInheritance();
        }

        public static function getParentPermissionsCollectionID()
        {
            // Concrete\Core\Page\Page::getParentPermissionsCollectionID();
            Concrete\Core\Page\Page::getParentPermissionsCollectionID();
        }

        public static function getPermissionsCollectionObject()
        {
            // Concrete\Core\Page\Page::getPermissionsCollectionObject();
            Concrete\Core\Page\Page::getPermissionsCollectionObject();
        }

        /**
         * Given the current page's template and page type, we return the master page
         */
        public static function getMasterCollectionID()
        {
            // Concrete\Core\Page\Page::getMasterCollectionID();
            Concrete\Core\Page\Page::getMasterCollectionID();
        }

        public static function getOriginalCollectionID()
        {
            // Concrete\Core\Page\Page::getOriginalCollectionID();
            Concrete\Core\Page\Page::getOriginalCollectionID();
        }

        public static function getNumChildren()
        {
            // Concrete\Core\Page\Page::getNumChildren();
            Concrete\Core\Page\Page::getNumChildren();
        }

        public static function getNumChildrenDirect()
        {
            // Concrete\Core\Page\Page::getNumChildrenDirect();
            Concrete\Core\Page\Page::getNumChildrenDirect();
        }

        /**
         * Returns the first child of the current page, or null if there is no child
         * @param string $sortColumn
         * @return Page
         */
        public static function getFirstChild($sortColumn = "cDisplayOrder asc", $excludeSystemPages = null)
        {
            // Concrete\Core\Page\Page::getFirstChild();
            Concrete\Core\Page\Page::getFirstChild($sortColumn, $excludeSystemPages);
        }

        public static function getCollectionChildrenArray($oneLevelOnly = null)
        {
            // Concrete\Core\Page\Page::getCollectionChildrenArray();
            Concrete\Core\Page\Page::getCollectionChildrenArray($oneLevelOnly);
        }

        public static function _getNumChildren($cID, $oneLevelOnly = null, $sortColumn = "cDisplayOrder asc")
        {
            // Concrete\Core\Page\Page::_getNumChildren();
            Concrete\Core\Page\Page::_getNumChildren($cID, $oneLevelOnly, $sortColumn);
        }

        public static function canMoveCopyTo($cobj)
        {
            // Concrete\Core\Page\Page::canMoveCopyTo();
            Concrete\Core\Page\Page::canMoveCopyTo($cobj);
        }

        public static function updateCollectionName($name)
        {
            // Concrete\Core\Page\Page::updateCollectionName();
            Concrete\Core\Page\Page::updateCollectionName($name);
        }

        public static function hasPageThemeCustomizations()
        {
            // Concrete\Core\Page\Page::hasPageThemeCustomizations();
            Concrete\Core\Page\Page::hasPageThemeCustomizations();
        }

        public static function resetCustomThemeStyles()
        {
            // Concrete\Core\Page\Page::resetCustomThemeStyles();
            Concrete\Core\Page\Page::resetCustomThemeStyles();
        }

        public static function getCustomThemeStyles()
        {
            // Concrete\Core\Page\Page::getCustomThemeStyles();
            Concrete\Core\Page\Page::getCustomThemeStyles();
        }

        public static function updateCustomThemeStyles($styles)
        {
            // Concrete\Core\Page\Page::updateCustomThemeStyles();
            Concrete\Core\Page\Page::updateCustomThemeStyles($styles);
        }

        public static function writePageThemeCustomizations()
        {
            // Concrete\Core\Page\Page::writePageThemeCustomizations();
            Concrete\Core\Page\Page::writePageThemeCustomizations();
        }

        public static function uniquifyPagePath($origPath)
        {
            // Concrete\Core\Page\Page::uniquifyPagePath();
            Concrete\Core\Page\Page::uniquifyPagePath($origPath);
        }

        public static function rescanPagePaths($newPaths)
        {
            // Concrete\Core\Page\Page::rescanPagePaths();
            Concrete\Core\Page\Page::rescanPagePaths($newPaths);
        }

        public static function clearPagePermissions()
        {
            // Concrete\Core\Page\Page::clearPagePermissions();
            Concrete\Core\Page\Page::clearPagePermissions();
        }

        public static function inheritPermissionsFromParent()
        {
            // Concrete\Core\Page\Page::inheritPermissionsFromParent();
            Concrete\Core\Page\Page::inheritPermissionsFromParent();
        }

        public static function inheritPermissionsFromDefaults()
        {
            // Concrete\Core\Page\Page::inheritPermissionsFromDefaults();
            Concrete\Core\Page\Page::inheritPermissionsFromDefaults();
        }

        public static function setPermissionsToManualOverride()
        {
            // Concrete\Core\Page\Page::setPermissionsToManualOverride();
            Concrete\Core\Page\Page::setPermissionsToManualOverride();
        }

        public static function rescanAreaPermissions()
        {
            // Concrete\Core\Page\Page::rescanAreaPermissions();
            Concrete\Core\Page\Page::rescanAreaPermissions();
        }

        public static function setOverrideTemplatePermissions($cOverrideTemplatePermissions)
        {
            // Concrete\Core\Page\Page::setOverrideTemplatePermissions();
            Concrete\Core\Page\Page::setOverrideTemplatePermissions($cOverrideTemplatePermissions);
        }

        public static function updatePermissionsCollectionID($cParentIDString, $npID)
        {
            // Concrete\Core\Page\Page::updatePermissionsCollectionID();
            Concrete\Core\Page\Page::updatePermissionsCollectionID($cParentIDString, $npID);
        }

        public static function acquireAreaPermissions($permissionsCollectionID)
        {
            // Concrete\Core\Page\Page::acquireAreaPermissions();
            Concrete\Core\Page\Page::acquireAreaPermissions($permissionsCollectionID);
        }

        public static function acquirePagePermissions($permissionsCollectionID)
        {
            // Concrete\Core\Page\Page::acquirePagePermissions();
            Concrete\Core\Page\Page::acquirePagePermissions($permissionsCollectionID);
        }

        public static function __destruct()
        {
            // Concrete\Core\Page\Page::__destruct();
            Concrete\Core\Page\Page::__destruct();
        }

        public static function updateGroupsSubCollection($cParentIDString)
        {
            // Concrete\Core\Page\Page::updateGroupsSubCollection();
            Concrete\Core\Page\Page::updateGroupsSubCollection($cParentIDString);
        }

        public static function move($nc, $retainOldPagePath = null)
        {
            // Concrete\Core\Page\Page::move();
            Concrete\Core\Page\Page::move($nc, $retainOldPagePath);
        }

        public static function duplicateAll($nc, $preserveUserID = null)
        {
            // Concrete\Core\Page\Page::duplicateAll();
            Concrete\Core\Page\Page::duplicateAll($nc, $preserveUserID);
        }

        /**
         * @access private
         **/
        public static function _duplicateAll($cParent, $cNewParent, $preserveUserID = null)
        {
            // Concrete\Core\Page\Page::_duplicateAll();
            Concrete\Core\Page\Page::_duplicateAll($cParent, $cNewParent, $preserveUserID);
        }

        public static function moveToTrash()
        {
            // Concrete\Core\Page\Page::moveToTrash();
            Concrete\Core\Page\Page::moveToTrash();
        }

        public static function rescanChildrenDisplayOrder()
        {
            // Concrete\Core\Page\Page::rescanChildrenDisplayOrder();
            Concrete\Core\Page\Page::rescanChildrenDisplayOrder();
        }

        public static function getNextSubPageDisplayOrder()
        {
            // Concrete\Core\Page\Page::getNextSubPageDisplayOrder();
            Concrete\Core\Page\Page::getNextSubPageDisplayOrder();
        }

        public static function rescanCollectionPath($retainOldPagePath = null)
        {
            // Concrete\Core\Page\Page::rescanCollectionPath();
            Concrete\Core\Page\Page::rescanCollectionPath($retainOldPagePath);
        }

        public static function updateDisplayOrder($do, $cID = null)
        {
            // Concrete\Core\Page\Page::updateDisplayOrder();
            Concrete\Core\Page\Page::updateDisplayOrder($do, $cID);
        }

        public static function movePageDisplayOrderToTop()
        {
            // Concrete\Core\Page\Page::movePageDisplayOrderToTop();
            Concrete\Core\Page\Page::movePageDisplayOrderToTop();
        }

        public static function movePageDisplayOrderToBottom()
        {
            // Concrete\Core\Page\Page::movePageDisplayOrderToBottom();
            Concrete\Core\Page\Page::movePageDisplayOrderToBottom();
        }

        public static function movePageDisplayOrderToSibling(Concrete\Core\Page\Page $c, $position = "before")
        {
            // Concrete\Core\Page\Page::movePageDisplayOrderToSibling();
            Concrete\Core\Page\Page::movePageDisplayOrderToSibling($c, $position);
        }

        public static function rescanCollectionPathIndividual($cID, $cPath, $retainOldPagePath = null)
        {
            // Concrete\Core\Page\Page::rescanCollectionPathIndividual();
            Concrete\Core\Page\Page::rescanCollectionPathIndividual($cID, $cPath, $retainOldPagePath);
        }

        public static function rescanSystemPageStatus()
        {
            // Concrete\Core\Page\Page::rescanSystemPageStatus();
            Concrete\Core\Page\Page::rescanSystemPageStatus();
        }

        public static function isInTrash()
        {
            // Concrete\Core\Page\Page::isInTrash();
            Concrete\Core\Page\Page::isInTrash();
        }

        public static function moveToRoot()
        {
            // Concrete\Core\Page\Page::moveToRoot();
            Concrete\Core\Page\Page::moveToRoot();
        }

        public static function rescanSystemPages()
        {
            // Concrete\Core\Page\Page::rescanSystemPages();
            Concrete\Core\Page\Page::rescanSystemPages();
        }

        public static function deactivate()
        {
            // Concrete\Core\Page\Page::deactivate();
            Concrete\Core\Page\Page::deactivate();
        }

        public static function activate()
        {
            // Concrete\Core\Page\Page::activate();
            Concrete\Core\Page\Page::activate();
        }

        public static function isActive()
        {
            // Concrete\Core\Page\Page::isActive();
            Concrete\Core\Page\Page::isActive();
        }

        public static function setPageIndexScore($score)
        {
            // Concrete\Core\Page\Page::setPageIndexScore();
            Concrete\Core\Page\Page::setPageIndexScore($score);
        }

        public static function getPageIndexScore()
        {
            // Concrete\Core\Page\Page::getPageIndexScore();
            Concrete\Core\Page\Page::getPageIndexScore();
        }

        public static function getPageIndexContent()
        {
            // Concrete\Core\Page\Page::getPageIndexContent();
            Concrete\Core\Page\Page::getPageIndexContent();
        }

        public static function rescanCollectionPathChildren($cID, $cPath)
        {
            // Concrete\Core\Page\Page::rescanCollectionPathChildren();
            Concrete\Core\Page\Page::rescanCollectionPathChildren($cID, $cPath);
        }

        public static function getCollectionAction()
        {
            // Concrete\Core\Page\Page::getCollectionAction();
            Concrete\Core\Page\Page::getCollectionAction();
        }

        public static function _associateMasterCollectionBlocks($newCID, $masterCID)
        {
            // Concrete\Core\Page\Page::_associateMasterCollectionBlocks();
            Concrete\Core\Page\Page::_associateMasterCollectionBlocks($newCID, $masterCID);
        }

        public static function _associateMasterCollectionAttributes($newCID, $masterCID)
        {
            // Concrete\Core\Page\Page::_associateMasterCollectionAttributes();
            Concrete\Core\Page\Page::_associateMasterCollectionAttributes($newCID, $masterCID);
        }

        /**
         * Adds the home page to the system. Typically used only by the installation program.
         * @return page
         **/
        public static function addHomePage()
        {
            // Concrete\Core\Page\Page::addHomePage();
            Concrete\Core\Page\Page::addHomePage();
        }

        /**
         * Adds a new page of a certain type, using a passed associate array to setup value. $data may contain any or all of the following:
         * "uID": User ID of the page's owner
         * "pkgID": Package ID the page belongs to
         * "cName": The name of the page
         * "cHandle": The handle of the page as used in the path
         * "cDatePublic": The date assigned to the page
         * @param collectiontype $ct
         * @param array $data
         * @return page
         **/
        public static function add($pt, $data, $template = null)
        {
            // Concrete\Core\Page\Page::add();
            Concrete\Core\Page\Page::add($pt, $data, $template);
        }

        public static function getCollectionFullPageCaching()
        {
            // Concrete\Core\Page\Page::getCollectionFullPageCaching();
            Concrete\Core\Page\Page::getCollectionFullPageCaching();
        }

        public static function getCollectionFullPageCachingLifetime()
        {
            // Concrete\Core\Page\Page::getCollectionFullPageCachingLifetime();
            Concrete\Core\Page\Page::getCollectionFullPageCachingLifetime();
        }

        public static function getCollectionFullPageCachingLifetimeCustomValue()
        {
            // Concrete\Core\Page\Page::getCollectionFullPageCachingLifetimeCustomValue();
            Concrete\Core\Page\Page::getCollectionFullPageCachingLifetimeCustomValue();
        }

        public static function getCollectionFullPageCachingLifetimeValue()
        {
            // Concrete\Core\Page\Page::getCollectionFullPageCachingLifetimeValue();
            Concrete\Core\Page\Page::getCollectionFullPageCachingLifetimeValue();
        }

        public static function addStatic($data)
        {
            // Concrete\Core\Page\Page::addStatic();
            Concrete\Core\Page\Page::addStatic($data);
        }

        public static function getPagePaths()
        {
            // Concrete\Core\Page\Page::getPagePaths();
            Concrete\Core\Page\Page::getPagePaths();
        }

        public static function getCurrentPage()
        {
            // Concrete\Core\Page\Page::getCurrentPage();
            Concrete\Core\Page\Page::getCurrentPage();
        }

        /**
         * Returns the total number of page views for a specific page
         */
        public static function getTotalPageViews($date = null)
        {
            // Concrete\Core\Page\Page::getTotalPageViews();
            Concrete\Core\Page\Page::getTotalPageViews($date);
        }

        public static function getPageDraftTargetParentPageID()
        {
            // Concrete\Core\Page\Page::getPageDraftTargetParentPageID();
            Concrete\Core\Page\Page::getPageDraftTargetParentPageID();
        }

        public static function setPageDraftTargetParentPageID($cParentID)
        {
            // Concrete\Core\Page\Page::setPageDraftTargetParentPageID();
            Concrete\Core\Page\Page::setPageDraftTargetParentPageID($cParentID);
        }

        /**
         * Gets a pages statistics
         */
        public static function getPageStatistics($limit = 20)
        {
            // Concrete\Core\Page\Page::getPageStatistics();
            Concrete\Core\Page\Page::getPageStatistics($limit);
        }

        public static function loadVersionObject($cvID = "ACTIVE")
        {
            // Concrete\Core\Page\Collection\Collection::loadVersionObject();
            Concrete\Core\Page\Collection\Collection::loadVersionObject($cvID);
        }

        public static function getVersionToModify()
        {
            // Concrete\Core\Page\Collection\Collection::getVersionToModify();
            Concrete\Core\Page\Collection\Collection::getVersionToModify();
        }

        public static function getNextVersionComments()
        {
            // Concrete\Core\Page\Collection\Collection::getNextVersionComments();
            Concrete\Core\Page\Collection\Collection::getNextVersionComments();
        }

        public static function cloneVersion($versionComments)
        {
            // Concrete\Core\Page\Collection\Collection::cloneVersion();
            Concrete\Core\Page\Collection\Collection::cloneVersion($versionComments);
        }

        public static function getFeatureAssignments()
        {
            // Concrete\Core\Page\Collection\Collection::getFeatureAssignments();
            Concrete\Core\Page\Collection\Collection::getFeatureAssignments();
        }

        /**
         * Returns the value of the attribute with the handle $ak
         * of the current object.
         *
         * $displayMode makes it possible to get the correct output
         * value. When you need the raw attribute value or object, use
         * this:
         * <code>
         * $c = Page::getCurrentPage();
         * $attributeValue = $c->getAttribute('attribute_handle');
         * </code>
         *
         * But if you need the formatted output supported by some
         * attribute, use this:
         * <code>
         * $c = Page::getCurrentPage();
         * $attributeValue = $c->getAttribute('attribute_handle', 'display');
         * </code>
         *
         * An attribute type like "date" will then return the date in
         * the correct format just like other attributes will show
         * you a nicely formatted output and not just a simple value
         * or object.
         *
         *
         * @param string|object $akHandle
         * @param boolean $displayMode
         * @return type
         */
        public static function getAttribute($akHandle, $displayMode = null)
        {
            // Concrete\Core\Page\Collection\Collection::getAttribute();
            Concrete\Core\Page\Collection\Collection::getAttribute($akHandle, $displayMode);
        }

        public static function getCollectionAttributeValue($ak)
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionAttributeValue();
            Concrete\Core\Page\Collection\Collection::getCollectionAttributeValue($ak);
        }

        public static function clearCollectionAttributes($retainAKIDs = null)
        {
            // Concrete\Core\Page\Collection\Collection::clearCollectionAttributes();
            Concrete\Core\Page\Collection\Collection::clearCollectionAttributes($retainAKIDs);
        }

        public static function reindexPendingPages()
        {
            // Concrete\Core\Page\Collection\Collection::reindexPendingPages();
            Concrete\Core\Page\Collection\Collection::reindexPendingPages();
        }

        public static function reindex($index = null, $actuallyDoReindex = "1")
        {
            // Concrete\Core\Page\Collection\Collection::reindex();
            Concrete\Core\Page\Collection\Collection::reindex($index, $actuallyDoReindex);
        }

        public static function getAttributeValueObject($ak, $createIfNotFound = null)
        {
            // Concrete\Core\Page\Collection\Collection::getAttributeValueObject();
            Concrete\Core\Page\Collection\Collection::getAttributeValueObject($ak, $createIfNotFound);
        }

        public static function setAttribute($ak, $value)
        {
            // Concrete\Core\Page\Collection\Collection::setAttribute();
            Concrete\Core\Page\Collection\Collection::setAttribute($ak, $value);
        }

        public static function clearAttribute($ak)
        {
            // Concrete\Core\Page\Collection\Collection::clearAttribute();
            Concrete\Core\Page\Collection\Collection::clearAttribute($ak);
        }

        public static function getSetCollectionAttributes()
        {
            // Concrete\Core\Page\Collection\Collection::getSetCollectionAttributes();
            Concrete\Core\Page\Collection\Collection::getSetCollectionAttributes();
        }

        public static function addAttribute($ak, $value)
        {
            // Concrete\Core\Page\Collection\Collection::addAttribute();
            Concrete\Core\Page\Collection\Collection::addAttribute($ak, $value);
        }

        public static function getArea($arHandle)
        {
            // Concrete\Core\Page\Collection\Collection::getArea();
            Concrete\Core\Page\Collection\Collection::getArea($arHandle);
        }

        public static function hasAliasedContent()
        {
            // Concrete\Core\Page\Collection\Collection::hasAliasedContent();
            Concrete\Core\Page\Collection\Collection::hasAliasedContent();
        }

        public static function getCollectionID()
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionID();
            Concrete\Core\Page\Collection\Collection::getCollectionID();
        }

        public static function getCollectionDateLastModified($mask = null, $type = "system")
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionDateLastModified();
            Concrete\Core\Page\Collection\Collection::getCollectionDateLastModified($mask, $type);
        }

        public static function getVersionObject()
        {
            // Concrete\Core\Page\Collection\Collection::getVersionObject();
            Concrete\Core\Page\Collection\Collection::getVersionObject();
        }

        public static function getCollectionDateAdded($mask = null, $type = "system")
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionDateAdded();
            Concrete\Core\Page\Collection\Collection::getCollectionDateAdded($mask, $type);
        }

        public static function getVersionID()
        {
            // Concrete\Core\Page\Collection\Collection::getVersionID();
            Concrete\Core\Page\Collection\Collection::getVersionID();
        }

        public static function getCollectionAreaDisplayOrder($arHandle, $ignoreVersions = null)
        {
            // Concrete\Core\Page\Collection\Collection::getCollectionAreaDisplayOrder();
            Concrete\Core\Page\Collection\Collection::getCollectionAreaDisplayOrder($arHandle, $ignoreVersions);
        }

        /**
         * Retrieves all custom style rules that should be inserted into the header on a page, whether they are defined in areas
         * or blocks
         */
        public static function outputCustomStyleHeaderItems($return = null)
        {
            // Concrete\Core\Page\Collection\Collection::outputCustomStyleHeaderItems();
            Concrete\Core\Page\Collection\Collection::outputCustomStyleHeaderItems($return);
        }

        public static function getAreaCustomStyleRule($area)
        {
            // Concrete\Core\Page\Collection\Collection::getAreaCustomStyleRule();
            Concrete\Core\Page\Collection\Collection::getAreaCustomStyleRule($area);
        }

        public static function resetAreaCustomStyle($area)
        {
            // Concrete\Core\Page\Collection\Collection::resetAreaCustomStyle();
            Concrete\Core\Page\Collection\Collection::resetAreaCustomStyle($area);
        }

        public static function setAreaCustomStyle($area, $csr)
        {
            // Concrete\Core\Page\Collection\Collection::setAreaCustomStyle();
            Concrete\Core\Page\Collection\Collection::setAreaCustomStyle($area, $csr);
        }

        public static function relateVersionEdits($oc)
        {
            // Concrete\Core\Page\Collection\Collection::relateVersionEdits();
            Concrete\Core\Page\Collection\Collection::relateVersionEdits($oc);
        }

        public static function rescanDisplayOrder($areaName)
        {
            // Concrete\Core\Page\Collection\Collection::rescanDisplayOrder();
            Concrete\Core\Page\Collection\Collection::rescanDisplayOrder($areaName);
        }

        public static function getByHandle($handle)
        {
            // Concrete\Core\Page\Collection\Collection::getByHandle();
            Concrete\Core\Page\Collection\Collection::getByHandle($handle);
        }

        public static function refreshCache()
        {
            // Concrete\Core\Page\Collection\Collection::refreshCache();
            Concrete\Core\Page\Collection\Collection::refreshCache();
        }

        public static function getGlobalBlocks()
        {
            // Concrete\Core\Page\Collection\Collection::getGlobalBlocks();
            Concrete\Core\Page\Collection\Collection::getGlobalBlocks();
        }

        /**
         * List the block IDs in a collection or area within a collection
         * @param string $arHandle. If specified, returns just the blocks in an area
         * @return array
         */
        public static function getBlockIDs($arHandle = null)
        {
            // Concrete\Core\Page\Collection\Collection::getBlockIDs();
            Concrete\Core\Page\Collection\Collection::getBlockIDs($arHandle);
        }

        /**
         * List the blocks in a collection or area within a collection
         * @param string $arHandle. If specified, returns just the blocks in an area
         * @return array
         */
        public static function getBlocks($arHandle = null)
        {
            // Concrete\Core\Page\Collection\Collection::getBlocks();
            Concrete\Core\Page\Collection\Collection::getBlocks($arHandle);
        }

        public static function addBlock($bt, $a, $data)
        {
            // Concrete\Core\Page\Collection\Collection::addBlock();
            Concrete\Core\Page\Collection\Collection::addBlock($bt, $a, $data);
        }

        public static function addFeature(Concrete\Core\Feature\Feature $fe)
        {
            // Concrete\Core\Page\Collection\Collection::addFeature();
            Concrete\Core\Page\Collection\Collection::addFeature($fe);
        }

        public static function addCollection($data)
        {
            // Concrete\Core\Page\Collection\Collection::addCollection();
            Concrete\Core\Page\Collection\Collection::addCollection($data);
        }

        public static function markModified()
        {
            // Concrete\Core\Page\Collection\Collection::markModified();
            Concrete\Core\Page\Collection\Collection::markModified();
        }

        public static function duplicateCollection()
        {
            // Concrete\Core\Page\Collection\Collection::duplicateCollection();
            Concrete\Core\Page\Collection\Collection::duplicateCollection();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class StackList extends \Concrete\Core\Page\Stack\StackList
    {

        public static function __construct()
        {
            // Concrete\Core\Page\Stack\StackList::__construct();
            Concrete\Core\Page\Stack\StackList::__construct();
        }

        public static function filterByGlobalAreas()
        {
            // Concrete\Core\Page\Stack\StackList::filterByGlobalAreas();
            Concrete\Core\Page\Stack\StackList::filterByGlobalAreas();
        }

        public static function filterByUserAdded()
        {
            // Concrete\Core\Page\Stack\StackList::filterByUserAdded();
            Concrete\Core\Page\Stack\StackList::filterByUserAdded();
        }

        public static function export(SimpleXMLElement $x)
        {
            // Concrete\Core\Page\Stack\StackList::export();
            Concrete\Core\Page\Stack\StackList::export($x);
        }

        public static function get($itemsToGet = null, $offset = null)
        {
            // Concrete\Core\Page\Stack\StackList::get();
            Concrete\Core\Page\Stack\StackList::get($itemsToGet, $offset);
        }

        public static function __call($nm, $a)
        {
            // Concrete\Core\Page\PageList::__call();
            Concrete\Core\Page\PageList::__call($nm, $a);
        }

        public static function setViewPagePermissionKeyHandle($pkHandle)
        {
            // Concrete\Core\Page\PageList::setViewPagePermissionKeyHandle();
            Concrete\Core\Page\PageList::setViewPagePermissionKeyHandle($pkHandle);
        }

        public static function includeInactivePages()
        {
            // Concrete\Core\Page\PageList::includeInactivePages();
            Concrete\Core\Page\PageList::includeInactivePages();
        }

        public static function ignorePermissions()
        {
            // Concrete\Core\Page\PageList::ignorePermissions();
            Concrete\Core\Page\PageList::ignorePermissions();
        }

        public static function ignoreAliases()
        {
            // Concrete\Core\Page\PageList::ignoreAliases();
            Concrete\Core\Page\PageList::ignoreAliases();
        }

        public static function includeSystemPages()
        {
            // Concrete\Core\Page\PageList::includeSystemPages();
            Concrete\Core\Page\PageList::includeSystemPages();
        }

        public static function displayUnapprovedPages()
        {
            // Concrete\Core\Page\PageList::displayUnapprovedPages();
            Concrete\Core\Page\PageList::displayUnapprovedPages();
        }

        public static function isIndexedSearch()
        {
            // Concrete\Core\Page\PageList::isIndexedSearch();
            Concrete\Core\Page\PageList::isIndexedSearch();
        }

        /**
         * Filters by "keywords" (which searches everything including filenames, title, tags, users who uploaded the file, tags)
         */
        public static function filterByKeywords($keywords, $simple = null)
        {
            // Concrete\Core\Page\PageList::filterByKeywords();
            Concrete\Core\Page\PageList::filterByKeywords($keywords, $simple);
        }

        public static function filterByName($name, $exact = null)
        {
            // Concrete\Core\Page\PageList::filterByName();
            Concrete\Core\Page\PageList::filterByName($name, $exact);
        }

        public static function filterByPath($path, $includeAllChildren = "1")
        {
            // Concrete\Core\Page\PageList::filterByPath();
            Concrete\Core\Page\PageList::filterByPath($path, $includeAllChildren);
        }

        /**
         * Sets up a list to only return items the proper user can access
         */
        public static function setupPermissions()
        {
            // Concrete\Core\Page\PageList::setupPermissions();
            Concrete\Core\Page\PageList::setupPermissions();
        }

        public static function sortByRelevance()
        {
            // Concrete\Core\Page\PageList::sortByRelevance();
            Concrete\Core\Page\PageList::sortByRelevance();
        }

        /**
         * Sorts this list by display order
         */
        public static function sortByDisplayOrder()
        {
            // Concrete\Core\Page\PageList::sortByDisplayOrder();
            Concrete\Core\Page\PageList::sortByDisplayOrder();
        }

        /**
         * Sorts this list by display order descending
         */
        public static function sortByDisplayOrderDescending()
        {
            // Concrete\Core\Page\PageList::sortByDisplayOrderDescending();
            Concrete\Core\Page\PageList::sortByDisplayOrderDescending();
        }

        public static function sortByCollectionIDAscending()
        {
            // Concrete\Core\Page\PageList::sortByCollectionIDAscending();
            Concrete\Core\Page\PageList::sortByCollectionIDAscending();
        }

        /**
         * Sorts this list by public date ascending order
         */
        public static function sortByPublicDate()
        {
            // Concrete\Core\Page\PageList::sortByPublicDate();
            Concrete\Core\Page\PageList::sortByPublicDate();
        }

        /**
         * Sorts this list by name
         */
        public static function sortByName()
        {
            // Concrete\Core\Page\PageList::sortByName();
            Concrete\Core\Page\PageList::sortByName();
        }

        /**
         * Sorts this list by name descending order
         */
        public static function sortByNameDescending()
        {
            // Concrete\Core\Page\PageList::sortByNameDescending();
            Concrete\Core\Page\PageList::sortByNameDescending();
        }

        /**
         * Sorts this list by public date descending order
         */
        public static function sortByPublicDateDescending()
        {
            // Concrete\Core\Page\PageList::sortByPublicDateDescending();
            Concrete\Core\Page\PageList::sortByPublicDateDescending();
        }

        /**
         * Sets the parent ID that we will grab pages from.
         * @param mixed $cParentID
         */
        public static function filterByParentID($cParentID)
        {
            // Concrete\Core\Page\PageList::filterByParentID();
            Concrete\Core\Page\PageList::filterByParentID($cParentID);
        }

        /**
         * Filters by type of collection (using the ID field)
         * @param mixed $ptID
         */
        public static function filterByPageTypeID($ptID)
        {
            // Concrete\Core\Page\PageList::filterByPageTypeID();
            Concrete\Core\Page\PageList::filterByPageTypeID($ptID);
        }

        /**
         * @deprecated
         */
        public static function filterByCollectionTypeID($ctID)
        {
            // Concrete\Core\Page\PageList::filterByCollectionTypeID();
            Concrete\Core\Page\PageList::filterByCollectionTypeID($ctID);
        }

        /**
         * Filters by user ID of collection (using the uID field)
         * @param mixed $uID
         */
        public static function filterByUserID($uID)
        {
            // Concrete\Core\Page\PageList::filterByUserID();
            Concrete\Core\Page\PageList::filterByUserID($uID);
        }

        public static function filterByIsApproved($cvIsApproved)
        {
            // Concrete\Core\Page\PageList::filterByIsApproved();
            Concrete\Core\Page\PageList::filterByIsApproved($cvIsApproved);
        }

        public static function filterByIsAlias($ia)
        {
            // Concrete\Core\Page\PageList::filterByIsAlias();
            Concrete\Core\Page\PageList::filterByIsAlias($ia);
        }

        /**
         * Filters by type of collection (using the handle field)
         * @param mixed $ptHandle
         */
        public static function filterByPageTypeHandle($ptHandle)
        {
            // Concrete\Core\Page\PageList::filterByPageTypeHandle();
            Concrete\Core\Page\PageList::filterByPageTypeHandle($ptHandle);
        }

        public static function filterByCollectionTypeHandle($ctHandle)
        {
            // Concrete\Core\Page\PageList::filterByCollectionTypeHandle();
            Concrete\Core\Page\PageList::filterByCollectionTypeHandle($ctHandle);
        }

        /**
         * Filters by date added
         * @param string $date
         */
        public static function filterByDateAdded($date, $comparison = "=")
        {
            // Concrete\Core\Page\PageList::filterByDateAdded();
            Concrete\Core\Page\PageList::filterByDateAdded($date, $comparison);
        }

        public static function filterByNumberOfChildren($num, $comparison = ">")
        {
            // Concrete\Core\Page\PageList::filterByNumberOfChildren();
            Concrete\Core\Page\PageList::filterByNumberOfChildren($num, $comparison);
        }

        public static function filterByDateLastModified($date, $comparison = "=")
        {
            // Concrete\Core\Page\PageList::filterByDateLastModified();
            Concrete\Core\Page\PageList::filterByDateLastModified($date, $comparison);
        }

        /**
         * Filters by public date
         * @param string $date
         */
        public static function filterByPublicDate($date, $comparison = "=")
        {
            // Concrete\Core\Page\PageList::filterByPublicDate();
            Concrete\Core\Page\PageList::filterByPublicDate($date, $comparison);
        }

        public static function filterBySelectAttribute($akHandle, $value)
        {
            // Concrete\Core\Page\PageList::filterBySelectAttribute();
            Concrete\Core\Page\PageList::filterBySelectAttribute($akHandle, $value);
        }

        /**
         * If true, pages will be checked for permissions prior to being returned
         * @param bool $checkForPermissions
         */
        public static function displayOnlyPermittedPages($checkForPermissions)
        {
            // Concrete\Core\Page\PageList::displayOnlyPermittedPages();
            Concrete\Core\Page\PageList::displayOnlyPermittedPages($checkForPermissions);
        }

        protected static function setBaseQuery($additionalFields = null)
        {
            // Concrete\Core\Page\PageList::setBaseQuery();
            Concrete\Core\Page\PageList::setBaseQuery($additionalFields);
        }

        protected static function setupSystemPagesToExclude()
        {
            // Concrete\Core\Page\PageList::setupSystemPagesToExclude();
            Concrete\Core\Page\PageList::setupSystemPagesToExclude();
        }

        protected static function loadPageID($cID, $versionOrig = "RECENT")
        {
            // Concrete\Core\Page\PageList::loadPageID();
            Concrete\Core\Page\PageList::loadPageID($cID, $versionOrig);
        }

        public static function getTotal()
        {
            // Concrete\Core\Page\PageList::getTotal();
            Concrete\Core\Page\PageList::getTotal();
        }

        public static function debug($dbg = "1")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug($dbg);
        }

        protected static function setQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery($query);
        }

        protected static function getQuery()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
        }

        public static function addToQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery($query);
        }

        protected static function setupAutoSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
        }

        protected static function executeBase()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
        }

        protected static function setupSortByString()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
        }

        protected static function setupAttributeSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
        }

        /**
         * Adds a filter to this item list
         */
        public static function filter($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter($column, $value, $comparison);
        }

        public static function getSearchResultsClass($field)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass($field);
        }

        public static function sortBy($key, $dir = "asc")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy($key, $dir);
        }

        public static function groupBy($key)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy($key);
        }

        public static function having($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having($column, $value, $comparison);
        }

        public static function getSortByURL($column, $dir = "asc", $baseURL = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL($column, $dir, $baseURL, $additionalVars);
        }

        protected static function setupAttributeFilters($join)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters($join);
        }

        public static function filterByAttribute($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute($column, $value, $comparison);
        }

        public static function enableStickySearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest($namespace);
        }

        public static function getQueryStringPagingVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
        }

        public static function getQueryStringSortVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
        }

        public static function getQueryStringSortDirectionVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
        }

        protected static function getStickySearchNameSpace()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
        }

        public static function resetSearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest($namespace);
        }

        public static function addToSearchRequest($key, $value)
        {
            // Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest($key, $value);
        }

        public static function getSearchRequest()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
        }

        public static function setItemsPerPage($num)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage($num);
        }

        public static function getItemsPerPage()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
        }

        public static function setItems($items)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItems();
            Concrete\Core\Foundation\Collection\ItemList::setItems($items);
        }

        public static function setNameSpace($ns)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::setNameSpace($ns);
        }

        /**
         * Returns an array of object by "page"
         */
        public static function getPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPage();
            Concrete\Core\Foundation\Collection\ItemList::getPage($page);
        }

        protected static function setCurrentPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setCurrentPage();
            Concrete\Core\Foundation\Collection\ItemList::setCurrentPage($page);
        }

        /**
         * Displays summary text about a list
         */
        public static function displaySummary($right_content = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displaySummary();
            Concrete\Core\Foundation\Collection\ItemList::displaySummary($right_content);
        }

        public static function isActiveSortColumn($column)
        {
            // Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn($column);
        }

        public static function getActiveSortColumn()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
        }

        public static function getActiveSortDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
        }

        public static function requiresPaging()
        {
            // Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
            Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
        }

        public static function getPagination($url = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPagination();
            Concrete\Core\Foundation\Collection\ItemList::getPagination($url, $additionalVars);
        }

        /**
         * Gets paging that works in our new format */
        public static function displayPagingV2($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPagingV2();
            Concrete\Core\Foundation\Collection\ItemList::displayPagingV2($script, $return, $additionalVars);
        }

        /**
         * Gets standard HTML to display paging */
        public static function displayPaging($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPaging();
            Concrete\Core\Foundation\Collection\ItemList::displayPaging($script, $return, $additionalVars);
        }

        /**
         * Returns an object with properties useful for paging
         */
        public static function getSummary()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSummary();
            Concrete\Core\Foundation\Collection\ItemList::getSummary();
        }

        public static function getSortBy()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortBy();
            Concrete\Core\Foundation\Collection\ItemList::getSortBy();
        }

        public static function getSortByDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
            Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
        }

        /**
         * Sets up a multiple columns to search by. Each argument is taken "as-is" (including asc or desc) and concatenated with commas
         * Note that this is overrides any previous sortByMultiple() call, and all sortBy() calls. Alternatively, you can pass a single
         * array with multiple columns to sort by as its values.
         * e.g. $list->sortByMultiple('columna desc', 'columnb asc');
         * or $list->sortByMultiple(array('columna desc', 'columnb asc'));
         */
        public static function sortByMultiple()
        {
            // Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
            Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
        }

    }

    class View extends \Concrete\Core\View\View
    {

        protected static function constructView($path = null)
        {
            // Concrete\Core\View\View::constructView();
            Concrete\Core\View\View::constructView($path);
        }

        public static function getThemeDirectory()
        {
            // Concrete\Core\View\View::getThemeDirectory();
            Concrete\Core\View\View::getThemeDirectory();
        }

        public static function getViewPath()
        {
            // Concrete\Core\View\View::getViewPath();
            Concrete\Core\View\View::getViewPath();
        }

        /**
         * gets the relative theme path for use in templates
         * @access public
         * @return string $themePath
         */
        public static function getThemePath()
        {
            // Concrete\Core\View\View::getThemePath();
            Concrete\Core\View\View::getThemePath();
        }

        public static function getThemeHandle()
        {
            // Concrete\Core\View\View::getThemeHandle();
            Concrete\Core\View\View::getThemeHandle();
        }

        public static function setInnerContentFile($innerContentFile)
        {
            // Concrete\Core\View\View::setInnerContentFile();
            Concrete\Core\View\View::setInnerContentFile($innerContentFile);
        }

        public static function setViewRootDirectoryName($directory)
        {
            // Concrete\Core\View\View::setViewRootDirectoryName();
            Concrete\Core\View\View::setViewRootDirectoryName($directory);
        }

        public static function inc($file, $args = null)
        {
            // Concrete\Core\View\View::inc();
            Concrete\Core\View\View::inc($file, $args);
        }

        /**
         * A shortcut to posting back to the current page with a task and optional parameters. Only works in the context of
         * @param string $action
         * @param string $task
         * @return string $url
         */
        public static function action($action)
        {
            // Concrete\Core\View\View::action();
            Concrete\Core\View\View::action($action);
        }

        public static function setViewTheme($theme)
        {
            // Concrete\Core\View\View::setViewTheme();
            Concrete\Core\View\View::setViewTheme($theme);
        }

        /**
         * Load all the theme-related variables for which theme to use for this request.
         */
        protected static function loadViewThemeObject()
        {
            // Concrete\Core\View\View::loadViewThemeObject();
            Concrete\Core\View\View::loadViewThemeObject();
        }

        /**
         * Begin the render
         */
        public static function start($state)
        {
            // Concrete\Core\View\View::start();
            Concrete\Core\View\View::start($state);
        }

        public static function setupRender()
        {
            // Concrete\Core\View\View::setupRender();
            Concrete\Core\View\View::setupRender();
        }

        public static function startRender()
        {
            // Concrete\Core\View\View::startRender();
            Concrete\Core\View\View::startRender();
        }

        protected static function onBeforeGetContents()
        {
            // Concrete\Core\View\View::onBeforeGetContents();
            Concrete\Core\View\View::onBeforeGetContents();
        }

        public static function renderViewContents($scopeItems)
        {
            // Concrete\Core\View\View::renderViewContents();
            Concrete\Core\View\View::renderViewContents($scopeItems);
        }

        public static function finishRender($contents)
        {
            // Concrete\Core\View\View::finishRender();
            Concrete\Core\View\View::finishRender($contents);
        }

        /**
         * Function responsible for outputting header items
         * @access private
         */
        public static function markHeaderAssetPosition()
        {
            // Concrete\Core\View\View::markHeaderAssetPosition();
            Concrete\Core\View\View::markHeaderAssetPosition();
        }

        /**
         * Function responsible for outputting footer items
         * @access private
         */
        public static function markFooterAssetPosition()
        {
            // Concrete\Core\View\View::markFooterAssetPosition();
            Concrete\Core\View\View::markFooterAssetPosition();
        }

        public static function postProcessViewContents($contents)
        {
            // Concrete\Core\View\View::postProcessViewContents();
            Concrete\Core\View\View::postProcessViewContents($contents);
        }

        protected static function postProcessAssets($assets)
        {
            // Concrete\Core\View\View::postProcessAssets();
            Concrete\Core\View\View::postProcessAssets($assets);
        }

        protected static function replaceEmptyAssetPlaceholders($pageContent)
        {
            // Concrete\Core\View\View::replaceEmptyAssetPlaceholders();
            Concrete\Core\View\View::replaceEmptyAssetPlaceholders($pageContent);
        }

        protected static function replaceAssetPlaceholders($outputAssets, $pageContent)
        {
            // Concrete\Core\View\View::replaceAssetPlaceholders();
            Concrete\Core\View\View::replaceAssetPlaceholders($outputAssets, $pageContent);
        }

        protected static function outputAssetIntoView($item)
        {
            // Concrete\Core\View\View::outputAssetIntoView();
            Concrete\Core\View\View::outputAssetIntoView($item);
        }

        public static function element($_file, $args = null, $_pkgHandle = null)
        {
            // Concrete\Core\View\View::element();
            Concrete\Core\View\View::element($_file, $args, $_pkgHandle);
        }

        public static function addScopeItems($items)
        {
            // Concrete\Core\View\AbstractView::addScopeItems();
            Concrete\Core\View\AbstractView::addScopeItems($items);
        }

        public static function getRequestInstance()
        {
            // Concrete\Core\View\AbstractView::getRequestInstance();
            Concrete\Core\View\AbstractView::getRequestInstance();
        }

        public static function __construct($mixed = null)
        {
            // Concrete\Core\View\AbstractView::__construct();
            Concrete\Core\View\AbstractView::__construct($mixed);
        }

        protected static function setRequestInstance(Concrete\Core\View\View $v)
        {
            // Concrete\Core\View\AbstractView::setRequestInstance();
            Concrete\Core\View\AbstractView::setRequestInstance($v);
        }

        protected static function revertRequestInstance()
        {
            // Concrete\Core\View\AbstractView::revertRequestInstance();
            Concrete\Core\View\AbstractView::revertRequestInstance();
        }

        public static function addHeaderAsset($asset)
        {
            // Concrete\Core\View\AbstractView::addHeaderAsset();
            Concrete\Core\View\AbstractView::addHeaderAsset($asset);
        }

        public static function addFooterAsset($asset)
        {
            // Concrete\Core\View\AbstractView::addFooterAsset();
            Concrete\Core\View\AbstractView::addFooterAsset($asset);
        }

        public static function addOutputAsset($asset)
        {
            // Concrete\Core\View\AbstractView::addOutputAsset();
            Concrete\Core\View\AbstractView::addOutputAsset($asset);
        }

        public static function requireAsset($assetType, $assetHandle = null)
        {
            // Concrete\Core\View\AbstractView::requireAsset();
            Concrete\Core\View\AbstractView::requireAsset($assetType, $assetHandle);
        }

        public static function setController($controller)
        {
            // Concrete\Core\View\AbstractView::setController();
            Concrete\Core\View\AbstractView::setController($controller);
        }

        public static function setViewTemplate($template)
        {
            // Concrete\Core\View\AbstractView::setViewTemplate();
            Concrete\Core\View\AbstractView::setViewTemplate($template);
        }

        /**
         * Returns the value of the item in the POST array.
         * @access public
         * @param $key
         * @return void
         */
        public static function post($key)
        {
            // Concrete\Core\View\AbstractView::post();
            Concrete\Core\View\AbstractView::post($key);
        }

        protected static function onAfterGetContents()
        {
            // Concrete\Core\View\AbstractView::onAfterGetContents();
            Concrete\Core\View\AbstractView::onAfterGetContents();
        }

        public static function getScopeItems()
        {
            // Concrete\Core\View\AbstractView::getScopeItems();
            Concrete\Core\View\AbstractView::getScopeItems();
        }

        public static function render($state = null)
        {
            // Concrete\Core\View\AbstractView::render();
            Concrete\Core\View\AbstractView::render($state);
        }

        /**
         * url is a utility function that is used inside a view to setup urls w/tasks and parameters
         * @deprecated
         * @param string $action
         * @param string $task
         * @return string $url
         */
        public static function url($action, $task = null)
        {
            // Concrete\Core\View\AbstractView::url();
            Concrete\Core\View\AbstractView::url($action, $task);
        }

        public static function setThemeByPath($path, $theme = null, $wrapper = "view.php")
        {
            // Concrete\Core\View\AbstractView::setThemeByPath();
            Concrete\Core\View\AbstractView::setThemeByPath($path, $theme, $wrapper);
        }

        public static function renderError($title, $error, $errorObj = null)
        {
            // Concrete\Core\View\AbstractView::renderError();
            Concrete\Core\View\AbstractView::renderError($title, $error, $errorObj);
        }

        /**
         * @access private
         */
        public static function addHeaderItem($item)
        {
            // Concrete\Core\View\AbstractView::addHeaderItem();
            Concrete\Core\View\AbstractView::addHeaderItem($item);
        }

        /**
         * @access private
         */
        public static function addFooterItem($item)
        {
            // Concrete\Core\View\AbstractView::addFooterItem();
            Concrete\Core\View\AbstractView::addFooterItem($item);
        }

        /**
         * @access private
         */
        public static function getInstance()
        {
            // Concrete\Core\View\AbstractView::getInstance();
            Concrete\Core\View\AbstractView::getInstance();
        }

    }

    class Job extends \Concrete\Core\Job\Job
    {

        public static function getJobHandle()
        {
            // Concrete\Core\Job\Job::getJobHandle();
            Concrete\Core\Job\Job::getJobHandle();
        }

        public static function getJobID()
        {
            // Concrete\Core\Job\Job::getJobID();
            Concrete\Core\Job\Job::getJobID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Job\Job::getPackageHandle();
            Concrete\Core\Job\Job::getPackageHandle();
        }

        public static function getJobLastStatusCode()
        {
            // Concrete\Core\Job\Job::getJobLastStatusCode();
            Concrete\Core\Job\Job::getJobLastStatusCode();
        }

        public static function didFail()
        {
            // Concrete\Core\Job\Job::didFail();
            Concrete\Core\Job\Job::didFail();
        }

        public static function canUninstall()
        {
            // Concrete\Core\Job\Job::canUninstall();
            Concrete\Core\Job\Job::canUninstall();
        }

        public static function supportsQueue()
        {
            // Concrete\Core\Job\Job::supportsQueue();
            Concrete\Core\Job\Job::supportsQueue();
        }

        public static function jobClassLocations()
        {
            // Concrete\Core\Job\Job::jobClassLocations();
            Concrete\Core\Job\Job::jobClassLocations();
        }

        public static function getJobDateLastRun()
        {
            // Concrete\Core\Job\Job::getJobDateLastRun();
            Concrete\Core\Job\Job::getJobDateLastRun();
        }

        public static function getJobStatus()
        {
            // Concrete\Core\Job\Job::getJobStatus();
            Concrete\Core\Job\Job::getJobStatus();
        }

        public static function getJobLastStatusText()
        {
            // Concrete\Core\Job\Job::getJobLastStatusText();
            Concrete\Core\Job\Job::getJobLastStatusText();
        }

        public static function authenticateRequest($auth)
        {
            // Concrete\Core\Job\Job::authenticateRequest();
            Concrete\Core\Job\Job::authenticateRequest($auth);
        }

        public static function generateAuth()
        {
            // Concrete\Core\Job\Job::generateAuth();
            Concrete\Core\Job\Job::generateAuth();
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Job\Job::exportList();
            Concrete\Core\Job\Job::exportList($xml);
        }

        public static function getList($scheduledOnly = null)
        {
            // Concrete\Core\Job\Job::getList();
            Concrete\Core\Job\Job::getList($scheduledOnly);
        }

        public static function reset()
        {
            // Concrete\Core\Job\Job::reset();
            Concrete\Core\Job\Job::reset();
        }

        public static function markStarted()
        {
            // Concrete\Core\Job\Job::markStarted();
            Concrete\Core\Job\Job::markStarted();
        }

        public static function markCompleted($resultCode = null, $resultMsg = null)
        {
            // Concrete\Core\Job\Job::markCompleted();
            Concrete\Core\Job\Job::markCompleted($resultCode, $resultMsg);
        }

        public static function getByID($jID = null)
        {
            // Concrete\Core\Job\Job::getByID();
            Concrete\Core\Job\Job::getByID($jID);
        }

        public static function getByHandle($jHandle = null)
        {
            // Concrete\Core\Job\Job::getByHandle();
            Concrete\Core\Job\Job::getByHandle($jHandle);
        }

        public static function getJobObjByHandle($jHandle = null, $jobData = null)
        {
            // Concrete\Core\Job\Job::getJobObjByHandle();
            Concrete\Core\Job\Job::getJobObjByHandle($jHandle, $jobData);
        }

        protected static function getClassName($jHandle)
        {
            // Concrete\Core\Job\Job::getClassName();
            Concrete\Core\Job\Job::getClassName($jHandle);
        }

        public static function getAvailableList($includeConcreteDirJobs = 1)
        {
            // Concrete\Core\Job\Job::getAvailableList();
            Concrete\Core\Job\Job::getAvailableList($includeConcreteDirJobs);
        }

        public static function executeJob()
        {
            // Concrete\Core\Job\Job::executeJob();
            Concrete\Core\Job\Job::executeJob();
        }

        public static function setJobStatus($jStatus = "ENABLED")
        {
            // Concrete\Core\Job\Job::setJobStatus();
            Concrete\Core\Job\Job::setJobStatus($jStatus);
        }

        public static function installByHandle($jHandle = null)
        {
            // Concrete\Core\Job\Job::installByHandle();
            Concrete\Core\Job\Job::installByHandle($jHandle);
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Job\Job::getListByPackage();
            Concrete\Core\Job\Job::getListByPackage($pkg);
        }

        public static function installByPackage($jHandle, $pkg)
        {
            // Concrete\Core\Job\Job::installByPackage();
            Concrete\Core\Job\Job::installByPackage($jHandle, $pkg);
        }

        public static function install()
        {
            // Concrete\Core\Job\Job::install();
            Concrete\Core\Job\Job::install();
        }

        public static function uninstall()
        {
            // Concrete\Core\Job\Job::uninstall();
            Concrete\Core\Job\Job::uninstall();
        }

        /**
         * Removes Job log entries
         */
        public static function clearLog()
        {
            // Concrete\Core\Job\Job::clearLog();
            Concrete\Core\Job\Job::clearLog();
        }

        public static function isScheduledForNow()
        {
            // Concrete\Core\Job\Job::isScheduledForNow();
            Concrete\Core\Job\Job::isScheduledForNow();
        }

        public static function setSchedule($scheduled, $interval, $value)
        {
            // Concrete\Core\Job\Job::setSchedule();
            Concrete\Core\Job\Job::setSchedule($scheduled, $interval, $value);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    /**
     * @package Workflow
     * @author Andrew Embler <andrew@concrete5.org>
     * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
     * @license    http://www.concrete5.org/license/     MIT License
     *
     */
    class Workflow extends \Concrete\Core\Workflow\Workflow
    {

        public static function getAllowedTasks()
        {
            // Concrete\Core\Workflow\Workflow::getAllowedTasks();
            Concrete\Core\Workflow\Workflow::getAllowedTasks();
        }

        public static function getWorkflowID()
        {
            // Concrete\Core\Workflow\Workflow::getWorkflowID();
            Concrete\Core\Workflow\Workflow::getWorkflowID();
        }

        public static function getWorkflowName()
        {
            // Concrete\Core\Workflow\Workflow::getWorkflowName();
            Concrete\Core\Workflow\Workflow::getWorkflowName();
        }

        public static function getWorkflowTypeObject()
        {
            // Concrete\Core\Workflow\Workflow::getWorkflowTypeObject();
            Concrete\Core\Workflow\Workflow::getWorkflowTypeObject();
        }

        public static function getRestrictedToPermissionKeyHandles()
        {
            // Concrete\Core\Workflow\Workflow::getRestrictedToPermissionKeyHandles();
            Concrete\Core\Workflow\Workflow::getRestrictedToPermissionKeyHandles();
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\Workflow\Workflow::getPermissionResponseClassName();
            Concrete\Core\Workflow\Workflow::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\Workflow\Workflow::getPermissionAssignmentClassName();
            Concrete\Core\Workflow\Workflow::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\Workflow\Workflow::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\Workflow\Workflow::getPermissionObjectKeyCategoryHandle();
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\Workflow\Workflow::getPermissionObjectIdentifier();
            Concrete\Core\Workflow\Workflow::getPermissionObjectIdentifier();
        }

        public static function delete()
        {
            // Concrete\Core\Workflow\Workflow::delete();
            Concrete\Core\Workflow\Workflow::delete();
        }

        public static function getWorkflowProgressCurrentStatusNum(Concrete\Core\Workflow\Progress\Progress $wp)
        {
            // Concrete\Core\Workflow\Workflow::getWorkflowProgressCurrentStatusNum();
            Concrete\Core\Workflow\Workflow::getWorkflowProgressCurrentStatusNum($wp);
        }

        public static function getList()
        {
            // Concrete\Core\Workflow\Workflow::getList();
            Concrete\Core\Workflow\Workflow::getList();
        }

        public static function add(Concrete\Core\Workflow\Type $wt, $name)
        {
            // Concrete\Core\Workflow\Workflow::add();
            Concrete\Core\Workflow\Workflow::add($wt, $name);
        }

        protected static function load($wfID)
        {
            // Concrete\Core\Workflow\Workflow::load();
            Concrete\Core\Workflow\Workflow::load($wfID);
        }

        public static function getByID($wfID)
        {
            // Concrete\Core\Workflow\Workflow::getByID();
            Concrete\Core\Workflow\Workflow::getByID($wfID);
        }

        public static function getWorkflowToolsURL($task)
        {
            // Concrete\Core\Workflow\Workflow::getWorkflowToolsURL();
            Concrete\Core\Workflow\Workflow::getWorkflowToolsURL($task);
        }

        public static function updateName($wfName)
        {
            // Concrete\Core\Workflow\Workflow::updateName();
            Concrete\Core\Workflow\Workflow::updateName($wfName);
        }

        public static function getPermissionAccessObject()
        {
            // Concrete\Core\Workflow\Workflow::getPermissionAccessObject();
            Concrete\Core\Workflow\Workflow::getPermissionAccessObject();
        }

        public static function validateTrigger(Concrete\Core\Workflow\Request\Request $req)
        {
            // Concrete\Core\Workflow\Workflow::validateTrigger();
            Concrete\Core\Workflow\Workflow::validateTrigger($req);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class JobSet extends \Concrete\Core\Job\Set
    {

        public static function getList()
        {
            // Concrete\Core\Job\Set::getList();
            Concrete\Core\Job\Set::getList();
        }

        public static function getByID($jsID)
        {
            // Concrete\Core\Job\Set::getByID();
            Concrete\Core\Job\Set::getByID($jsID);
        }

        public static function getByName($jsName)
        {
            // Concrete\Core\Job\Set::getByName();
            Concrete\Core\Job\Set::getByName($jsName);
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Job\Set::getListByPackage();
            Concrete\Core\Job\Set::getListByPackage($pkg);
        }

        public static function getDefault()
        {
            // Concrete\Core\Job\Set::getDefault();
            Concrete\Core\Job\Set::getDefault();
        }

        public static function getJobSetID()
        {
            // Concrete\Core\Job\Set::getJobSetID();
            Concrete\Core\Job\Set::getJobSetID();
        }

        public static function getJobSetName()
        {
            // Concrete\Core\Job\Set::getJobSetName();
            Concrete\Core\Job\Set::getJobSetName();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Job\Set::getPackageID();
            Concrete\Core\Job\Set::getPackageID();
        }

        /** Returns the display name for this job set (localized and escaped accordingly to $format)
         * @param string $format = 'html'
         *	Escape the result in html format (if $format is 'html').
         *	If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getJobSetDisplayName($format = "html")
        {
            // Concrete\Core\Job\Set::getJobSetDisplayName();
            Concrete\Core\Job\Set::getJobSetDisplayName($format);
        }

        public static function updateJobSetName($jsName)
        {
            // Concrete\Core\Job\Set::updateJobSetName();
            Concrete\Core\Job\Set::updateJobSetName($jsName);
        }

        public static function addJob(Concrete\Core\Job\Job $j)
        {
            // Concrete\Core\Job\Set::addJob();
            Concrete\Core\Job\Set::addJob($j);
        }

        public static function add($jsName, $pkg = null)
        {
            // Concrete\Core\Job\Set::add();
            Concrete\Core\Job\Set::add($jsName, $pkg);
        }

        public static function clearJobs()
        {
            // Concrete\Core\Job\Set::clearJobs();
            Concrete\Core\Job\Set::clearJobs();
        }

        public static function getJobs()
        {
            // Concrete\Core\Job\Set::getJobs();
            Concrete\Core\Job\Set::getJobs();
        }

        public static function markStarted()
        {
            // Concrete\Core\Job\Set::markStarted();
            Concrete\Core\Job\Set::markStarted();
        }

        public static function contains(Concrete\Core\Job\Job $j)
        {
            // Concrete\Core\Job\Set::contains();
            Concrete\Core\Job\Set::contains($j);
        }

        public static function delete()
        {
            // Concrete\Core\Job\Set::delete();
            Concrete\Core\Job\Set::delete();
        }

        public static function canDelete()
        {
            // Concrete\Core\Job\Set::canDelete();
            Concrete\Core\Job\Set::canDelete();
        }

        public static function removeJob(Concrete\Core\Job\Job $j)
        {
            // Concrete\Core\Job\Set::removeJob();
            Concrete\Core\Job\Set::removeJob($j);
        }

        public static function isScheduledForNow()
        {
            // Concrete\Core\Job\Set::isScheduledForNow();
            Concrete\Core\Job\Set::isScheduledForNow();
        }

        public static function setSchedule($scheduled, $interval, $value)
        {
            // Concrete\Core\Job\Set::setSchedule();
            Concrete\Core\Job\Set::setSchedule($scheduled, $interval, $value);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class File extends \Concrete\Core\File\File
    {

        /**
         * returns a file object for the given file ID
         * @param int $fID
         * @return File
         */
        public static function getByID($fID)
        {
            // Concrete\Core\File\File::getByID();
            Concrete\Core\File\File::getByID($fID);
        }

        /**
         * For all methods that file does not implement, we pass through to the currently active file version object
         */
        public static function __call($nm, $a)
        {
            // Concrete\Core\File\File::__call();
            Concrete\Core\File\File::__call($nm, $a);
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\File\File::getPermissionResponseClassName();
            Concrete\Core\File\File::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\File\File::getPermissionAssignmentClassName();
            Concrete\Core\File\File::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\File\File::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\File\File::getPermissionObjectKeyCategoryHandle();
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\File\File::getPermissionObjectIdentifier();
            Concrete\Core\File\File::getPermissionObjectIdentifier();
        }

        public static function getPath()
        {
            // Concrete\Core\File\File::getPath();
            Concrete\Core\File\File::getPath();
        }

        public static function getPassword()
        {
            // Concrete\Core\File\File::getPassword();
            Concrete\Core\File\File::getPassword();
        }

        public static function getStorageLocationID()
        {
            // Concrete\Core\File\File::getStorageLocationID();
            Concrete\Core\File\File::getStorageLocationID();
        }

        public static function refreshCache()
        {
            // Concrete\Core\File\File::refreshCache();
            Concrete\Core\File\File::refreshCache();
        }

        public static function reindex()
        {
            // Concrete\Core\File\File::reindex();
            Concrete\Core\File\File::reindex();
        }

        public static function getRelativePathFromID($fID)
        {
            // Concrete\Core\File\File::getRelativePathFromID();
            Concrete\Core\File\File::getRelativePathFromID($fID);
        }

        public static function setStorageLocation($item)
        {
            // Concrete\Core\File\File::setStorageLocation();
            Concrete\Core\File\File::setStorageLocation($item);
        }

        public static function setPassword($pw)
        {
            // Concrete\Core\File\File::setPassword();
            Concrete\Core\File\File::setPassword($pw);
        }

        public static function setOriginalPage($ocID)
        {
            // Concrete\Core\File\File::setOriginalPage();
            Concrete\Core\File\File::setOriginalPage($ocID);
        }

        public static function getOriginalPageObject()
        {
            // Concrete\Core\File\File::getOriginalPageObject();
            Concrete\Core\File\File::getOriginalPageObject();
        }

        public static function overrideFileSetPermissions()
        {
            // Concrete\Core\File\File::overrideFileSetPermissions();
            Concrete\Core\File\File::overrideFileSetPermissions();
        }

        public static function resetPermissions($fOverrideSetPermissions = null)
        {
            // Concrete\Core\File\File::resetPermissions();
            Concrete\Core\File\File::resetPermissions($fOverrideSetPermissions);
        }

        public static function getUserID()
        {
            // Concrete\Core\File\File::getUserID();
            Concrete\Core\File\File::getUserID();
        }

        public static function setUserID($uID)
        {
            // Concrete\Core\File\File::setUserID();
            Concrete\Core\File\File::setUserID($uID);
        }

        public static function getFileSets()
        {
            // Concrete\Core\File\File::getFileSets();
            Concrete\Core\File\File::getFileSets();
        }

        public static function isStarred($u = null)
        {
            // Concrete\Core\File\File::isStarred();
            Concrete\Core\File\File::isStarred($u);
        }

        public static function getDateAdded()
        {
            // Concrete\Core\File\File::getDateAdded();
            Concrete\Core\File\File::getDateAdded();
        }

        /**
         * Returns a file version object that is to be written to. Computes whether we can use the current most recent version, OR a new one should be created
         */
        public static function getVersionToModify($forceCreateNew = null)
        {
            // Concrete\Core\File\File::getVersionToModify();
            Concrete\Core\File\File::getVersionToModify($forceCreateNew);
        }

        public static function getFileID()
        {
            // Concrete\Core\File\File::getFileID();
            Concrete\Core\File\File::getFileID();
        }

        public static function duplicate()
        {
            // Concrete\Core\File\File::duplicate();
            Concrete\Core\File\File::duplicate();
        }

        public static function add($filename, $prefix, $data = null)
        {
            // Concrete\Core\File\File::add();
            Concrete\Core\File\File::add($filename, $prefix, $data);
        }

        public static function addVersion($filename, $prefix, $data = null)
        {
            // Concrete\Core\File\File::addVersion();
            Concrete\Core\File\File::addVersion($filename, $prefix, $data);
        }

        public static function getApprovedVersion()
        {
            // Concrete\Core\File\File::getApprovedVersion();
            Concrete\Core\File\File::getApprovedVersion();
        }

        public static function inFileSet($fs)
        {
            // Concrete\Core\File\File::inFileSet();
            Concrete\Core\File\File::inFileSet($fs);
        }

        /**
         * Removes a file, including all of its versions
         */
        public static function delete()
        {
            // Concrete\Core\File\File::delete();
            Concrete\Core\File\File::delete();
        }

        /**
         * returns the most recent FileVersion object
         * @return FileVersion
         */
        public static function getRecentVersion()
        {
            // Concrete\Core\File\File::getRecentVersion();
            Concrete\Core\File\File::getRecentVersion();
        }

        /**
         * returns the FileVersion object for the provided fvID
         * if none provided returns the approved version
         * @param int $fvID
         * @return FileVersion
         */
        public static function getVersion($fvID = null)
        {
            // Concrete\Core\File\File::getVersion();
            Concrete\Core\File\File::getVersion($fvID);
        }

        /**
         * Returns an array of all FileVersion objects owned by this file
         */
        public static function getVersionList()
        {
            // Concrete\Core\File\File::getVersionList();
            Concrete\Core\File\File::getVersionList();
        }

        public static function getTotalDownloads()
        {
            // Concrete\Core\File\File::getTotalDownloads();
            Concrete\Core\File\File::getTotalDownloads();
        }

        public static function getDownloadStatistics($limit = 20)
        {
            // Concrete\Core\File\File::getDownloadStatistics();
            Concrete\Core\File\File::getDownloadStatistics($limit);
        }

        /**
         * Tracks File Download, takes the cID of the page that the file was downloaded from
         * @param int $rcID
         * @return void
         */
        public static function trackDownload($rcID = null)
        {
            // Concrete\Core\File\File::trackDownload();
            Concrete\Core\File\File::trackDownload($rcID);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class FileVersion extends \Concrete\Core\File\Version
    {

        public static function getFileID()
        {
            // Concrete\Core\File\Version::getFileID();
            Concrete\Core\File\Version::getFileID();
        }

        public static function getFileVersionID()
        {
            // Concrete\Core\File\Version::getFileVersionID();
            Concrete\Core\File\Version::getFileVersionID();
        }

        public static function getPrefix()
        {
            // Concrete\Core\File\Version::getPrefix();
            Concrete\Core\File\Version::getPrefix();
        }

        public static function getFileName()
        {
            // Concrete\Core\File\Version::getFileName();
            Concrete\Core\File\Version::getFileName();
        }

        public static function getTitle()
        {
            // Concrete\Core\File\Version::getTitle();
            Concrete\Core\File\Version::getTitle();
        }

        public static function getTags()
        {
            // Concrete\Core\File\Version::getTags();
            Concrete\Core\File\Version::getTags();
        }

        public static function getDescription()
        {
            // Concrete\Core\File\Version::getDescription();
            Concrete\Core\File\Version::getDescription();
        }

        public static function isApproved()
        {
            // Concrete\Core\File\Version::isApproved();
            Concrete\Core\File\Version::isApproved();
        }

        public static function getGenericTypeText()
        {
            // Concrete\Core\File\Version::getGenericTypeText();
            Concrete\Core\File\Version::getGenericTypeText();
        }

        /**
         * returns the File object associated with this FileVersion object
         * @return File
         */
        public static function getFile()
        {
            // Concrete\Core\File\Version::getFile();
            Concrete\Core\File\Version::getFile();
        }

        public static function getTagsList()
        {
            // Concrete\Core\File\Version::getTagsList();
            Concrete\Core\File\Version::getTagsList();
        }

        /**
         * Gets an associative array of all attributes for a file version
         */
        public static function getAttributeList()
        {
            // Concrete\Core\File\Version::getAttributeList();
            Concrete\Core\File\Version::getAttributeList();
        }

        /**
         * Gets an attribute for the file. If "nice mode" is set, we display it nicely
         * for use in the file attributes table
         */
        public static function getAttribute($ak, $mode = null)
        {
            // Concrete\Core\File\Version::getAttribute();
            Concrete\Core\File\Version::getAttribute($ak, $mode);
        }

        public static function getMimeType()
        {
            // Concrete\Core\File\Version::getMimeType();
            Concrete\Core\File\Version::getMimeType();
        }

        public static function getSize()
        {
            // Concrete\Core\File\Version::getSize();
            Concrete\Core\File\Version::getSize();
        }

        public static function getFullSize()
        {
            // Concrete\Core\File\Version::getFullSize();
            Concrete\Core\File\Version::getFullSize();
        }

        public static function getAuthorName()
        {
            // Concrete\Core\File\Version::getAuthorName();
            Concrete\Core\File\Version::getAuthorName();
        }

        public static function getAuthorUserID()
        {
            // Concrete\Core\File\Version::getAuthorUserID();
            Concrete\Core\File\Version::getAuthorUserID();
        }

        /**
         * Gets the date a file version was added
         * if user is specified, returns in the current user's timezone
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getDateAdded($type = "system")
        {
            // Concrete\Core\File\Version::getDateAdded();
            Concrete\Core\File\Version::getDateAdded($type);
        }

        public static function getExtension()
        {
            // Concrete\Core\File\Version::getExtension();
            Concrete\Core\File\Version::getExtension();
        }

        public static function logVersionUpdate($updateTypeID, $updateTypeAttributeID = null)
        {
            // Concrete\Core\File\Version::logVersionUpdate();
            Concrete\Core\File\Version::logVersionUpdate($updateTypeID, $updateTypeAttributeID);
        }

        /**
         * Takes the current value of the file version and makes a new one with the same values
         */
        public static function duplicate()
        {
            // Concrete\Core\File\Version::duplicate();
            Concrete\Core\File\Version::duplicate();
        }

        public static function getType()
        {
            // Concrete\Core\File\Version::getType();
            Concrete\Core\File\Version::getType();
        }

        public static function getTypeObject()
        {
            // Concrete\Core\File\Version::getTypeObject();
            Concrete\Core\File\Version::getTypeObject();
        }

        /**
         * Returns an array containing human-readable descriptions of everything that happened in this version
         */
        public static function getVersionLogComments()
        {
            // Concrete\Core\File\Version::getVersionLogComments();
            Concrete\Core\File\Version::getVersionLogComments();
        }

        public static function updateTitle($title)
        {
            // Concrete\Core\File\Version::updateTitle();
            Concrete\Core\File\Version::updateTitle($title);
        }

        public static function updateTags($tags)
        {
            // Concrete\Core\File\Version::updateTags();
            Concrete\Core\File\Version::updateTags($tags);
        }

        public static function updateDescription($descr)
        {
            // Concrete\Core\File\Version::updateDescription();
            Concrete\Core\File\Version::updateDescription($descr);
        }

        public static function updateFile($filename, $prefix)
        {
            // Concrete\Core\File\Version::updateFile();
            Concrete\Core\File\Version::updateFile($filename, $prefix);
        }

        public static function approve()
        {
            // Concrete\Core\File\Version::approve();
            Concrete\Core\File\Version::approve();
        }

        public static function deny()
        {
            // Concrete\Core\File\Version::deny();
            Concrete\Core\File\Version::deny();
        }

        public static function setAttribute($ak, $value)
        {
            // Concrete\Core\File\Version::setAttribute();
            Concrete\Core\File\Version::setAttribute($ak, $value);
        }

        /**
         * Removes a version of a file. Note, does NOT remove the file because we don't know where the file might elsewhere be used/referenced.
         */
        public static function delete()
        {
            // Concrete\Core\File\Version::delete();
            Concrete\Core\File\Version::delete();
        }

        /**
         * Returns a full filesystem path to the file on disk.
         */
        public static function getPath()
        {
            // Concrete\Core\File\Version::getPath();
            Concrete\Core\File\Version::getPath();
        }

        /**
         * Returns a full URL to the file on disk
         */
        public static function getURL()
        {
            // Concrete\Core\File\Version::getURL();
            Concrete\Core\File\Version::getURL();
        }

        /**
         * Returns a URL that can be used to download the file. This passes through the download_file single page.
         */
        public static function getDownloadURL()
        {
            // Concrete\Core\File\Version::getDownloadURL();
            Concrete\Core\File\Version::getDownloadURL();
        }

        /**
         * Returns a url that can be used to download a file, will force the download of all file types, even if your browser can display them.
         */
        public static function getForceDownloadURL()
        {
            // Concrete\Core\File\Version::getForceDownloadURL();
            Concrete\Core\File\Version::getForceDownloadURL();
        }

        public static function getRelativePath($fullurl = null)
        {
            // Concrete\Core\File\Version::getRelativePath();
            Concrete\Core\File\Version::getRelativePath($fullurl);
        }

        public static function getThumbnailPath($level)
        {
            // Concrete\Core\File\Version::getThumbnailPath();
            Concrete\Core\File\Version::getThumbnailPath($level);
        }

        public static function getThumbnailSRC($level)
        {
            // Concrete\Core\File\Version::getThumbnailSRC();
            Concrete\Core\File\Version::getThumbnailSRC($level);
        }

        public static function hasThumbnail($level)
        {
            // Concrete\Core\File\Version::hasThumbnail();
            Concrete\Core\File\Version::hasThumbnail($level);
        }

        public static function getThumbnail($level, $fullImageTag = "1")
        {
            // Concrete\Core\File\Version::getThumbnail();
            Concrete\Core\File\Version::getThumbnail($level, $fullImageTag);
        }

        public static function refreshThumbnails($refreshCache = "1")
        {
            // Concrete\Core\File\Version::refreshThumbnails();
            Concrete\Core\File\Version::refreshThumbnails($refreshCache);
        }

        /**
         * Responsible for taking a particular version of a file and rescanning all its attributes
         * This will run any type-based import routines, and store those attributes, generate thumbnails,
         * etc...
         */
        public static function refreshAttributes($firstRun = null)
        {
            // Concrete\Core\File\Version::refreshAttributes();
            Concrete\Core\File\Version::refreshAttributes($firstRun);
        }

        public static function createThumbnailDirectories()
        {
            // Concrete\Core\File\Version::createThumbnailDirectories();
            Concrete\Core\File\Version::createThumbnailDirectories();
        }

        /**
         * Checks current viewers for this type and returns true if there is a viewer for this type, false if not
         */
        public static function canView()
        {
            // Concrete\Core\File\Version::canView();
            Concrete\Core\File\Version::canView();
        }

        public static function canEdit()
        {
            // Concrete\Core\File\Version::canEdit();
            Concrete\Core\File\Version::canEdit();
        }

        public static function clearAttribute($ak)
        {
            // Concrete\Core\File\Version::clearAttribute();
            Concrete\Core\File\Version::clearAttribute($ak);
        }

        public static function getAttributeValueObject($ak, $createIfNotFound = null)
        {
            // Concrete\Core\File\Version::getAttributeValueObject();
            Concrete\Core\File\Version::getAttributeValueObject($ak, $createIfNotFound);
        }

        public static function cleanTags($tagsStr)
        {
            // Concrete\Core\File\Version::cleanTags();
            Concrete\Core\File\Version::cleanTags($tagsStr);
        }

        /**
         * Return a representation of the current FileVersion object as something easily serializable.
         */
        public static function getJSONObject()
        {
            // Concrete\Core\File\Version::getJSONObject();
            Concrete\Core\File\Version::getJSONObject();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class FileSet extends \Concrete\Core\File\Set\Set
    {

        /**
         * Returns an object mapping to the global file set, fsID = 0.
         * This is really only used for permissions mapping
         */
        public static function getGlobal()
        {
            // Concrete\Core\File\Set\Set::getGlobal();
            Concrete\Core\File\Set\Set::getGlobal();
        }

        public static function getFileSetUserID()
        {
            // Concrete\Core\File\Set\Set::getFileSetUserID();
            Concrete\Core\File\Set\Set::getFileSetUserID();
        }

        public static function getFileSetType()
        {
            // Concrete\Core\File\Set\Set::getFileSetType();
            Concrete\Core\File\Set\Set::getFileSetType();
        }

        public static function getSavedSearches()
        {
            // Concrete\Core\File\Set\Set::getSavedSearches();
            Concrete\Core\File\Set\Set::getSavedSearches();
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\File\Set\Set::getPermissionResponseClassName();
            Concrete\Core\File\Set\Set::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\File\Set\Set::getPermissionAssignmentClassName();
            Concrete\Core\File\Set\Set::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\File\Set\Set::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\File\Set\Set::getPermissionObjectKeyCategoryHandle();
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\File\Set\Set::getPermissionObjectIdentifier();
            Concrete\Core\File\Set\Set::getPermissionObjectIdentifier();
        }

        public static function getMySets($u = null)
        {
            // Concrete\Core\File\Set\Set::getMySets();
            Concrete\Core\File\Set\Set::getMySets($u);
        }

        public static function updateFileSetDisplayOrder($files)
        {
            // Concrete\Core\File\Set\Set::updateFileSetDisplayOrder();
            Concrete\Core\File\Set\Set::updateFileSetDisplayOrder($files);
        }

        /**
         * Get a file set object by a file set's id
         * @param int $fsID
         * @return FileSet
         */
        public static function getByID($fsID)
        {
            // Concrete\Core\File\Set\Set::getByID();
            Concrete\Core\File\Set\Set::getByID($fsID);
        }

        /**
         * Get a file set object by a file name
         * @param string $fsName
         * @return FileSet
         */
        public static function getByName($fsName)
        {
            // Concrete\Core\File\Set\Set::getByName();
            Concrete\Core\File\Set\Set::getByName($fsName);
        }

        public static function getFileSetID()
        {
            // Concrete\Core\File\Set\Set::getFileSetID();
            Concrete\Core\File\Set\Set::getFileSetID();
        }

        public static function overrideGlobalPermissions()
        {
            // Concrete\Core\File\Set\Set::overrideGlobalPermissions();
            Concrete\Core\File\Set\Set::overrideGlobalPermissions();
        }

        public static function getFileSetName()
        {
            // Concrete\Core\File\Set\Set::getFileSetName();
            Concrete\Core\File\Set\Set::getFileSetName();
        }

        /**
         * Creats a new fileset if set doesn't exists
         *
         * If we find a multiple groups with the same properties,
         * we return an array containing each group
         * @param string $fs_name
         * @param int $fs_type
         * @param int $fs_uid
         * @return Mixed
         *
         * Dev Note: This will create duplicate sets with the same name if a set exists owned by another user!!!
         */
        public static function createAndGetSet($fs_name, $fs_type, $fs_uid = null)
        {
            // Concrete\Core\File\Set\Set::createAndGetSet();
            Concrete\Core\File\Set\Set::createAndGetSet($fs_name, $fs_type, $fs_uid);
        }

        /**
         * Adds a file set
         */
        public static function add($setName, $fsOverrideGlobalPermissions = null, $u = null, $type = 1)
        {
            // Concrete\Core\File\Set\Set::add();
            Concrete\Core\File\Set\Set::add($setName, $fsOverrideGlobalPermissions, $u, $type);
        }

        /**
         * Updates a file set.
         */
        public static function update($setName, $fsOverrideGlobalPermissions = null)
        {
            // Concrete\Core\File\Set\Set::update();
            Concrete\Core\File\Set\Set::update($setName, $fsOverrideGlobalPermissions);
        }

        /**
         * Adds the file to the set
         * @param type $fID  //accepts an ID or a File object
         * @return object
         */
        public static function addFileToSet($f_id)
        {
            // Concrete\Core\File\Set\Set::addFileToSet();
            Concrete\Core\File\Set\Set::addFileToSet($f_id);
        }

        public static function getSavedSearchRequest()
        {
            // Concrete\Core\File\Set\Set::getSavedSearchRequest();
            Concrete\Core\File\Set\Set::getSavedSearchRequest();
        }

        public static function getSavedSearchColumns()
        {
            // Concrete\Core\File\Set\Set::getSavedSearchColumns();
            Concrete\Core\File\Set\Set::getSavedSearchColumns();
        }

        public static function removeFileFromSet($f_id)
        {
            // Concrete\Core\File\Set\Set::removeFileFromSet();
            Concrete\Core\File\Set\Set::removeFileFromSet($f_id);
        }

        public static function hasFileID($f_id)
        {
            // Concrete\Core\File\Set\Set::hasFileID();
            Concrete\Core\File\Set\Set::hasFileID($f_id);
        }

        /**
         * Returns an array of File objects from the current set
         * @return array
         */
        public static function getFiles()
        {
            // Concrete\Core\File\Set\Set::getFiles();
            Concrete\Core\File\Set\Set::getFiles();
        }

        /**
         * Static method to return an array of File objects by the set id
         * @param  int $fsID
         * @return array
         */
        public static function getFilesBySetID($fsID)
        {
            // Concrete\Core\File\Set\Set::getFilesBySetID();
            Concrete\Core\File\Set\Set::getFilesBySetID($fsID);
        }

        /**
         * Static method to return an array of File objects by the set name
         * @param  string $fsName
         * @return array
         */
        public static function getFilesBySetName($fsName)
        {
            // Concrete\Core\File\Set\Set::getFilesBySetName();
            Concrete\Core\File\Set\Set::getFilesBySetName($fsName);
        }

        public static function delete()
        {
            // Concrete\Core\File\Set\Set::delete();
            Concrete\Core\File\Set\Set::delete();
        }

        public static function resetPermissions()
        {
            // Concrete\Core\File\Set\Set::resetPermissions();
            Concrete\Core\File\Set\Set::resetPermissions();
        }

        public static function acquireBaseFileSetPermissions()
        {
            // Concrete\Core\File\Set\Set::acquireBaseFileSetPermissions();
            Concrete\Core\File\Set\Set::acquireBaseFileSetPermissions();
        }

        public static function assignPermissions($userOrGroup, $permissions = null, $accessType = 10)
        {
            // Concrete\Core\File\Set\Set::assignPermissions();
            Concrete\Core\File\Set\Set::assignPermissions($userOrGroup, $permissions, $accessType);
        }

    }

    class FileImporter extends \Concrete\Core\File\Importer
    {

        /**
         * Returns a text string explaining the error that was passed
         */
        public static function getErrorMessage($code)
        {
            // Concrete\Core\File\Importer::getErrorMessage();
            Concrete\Core\File\Importer::getErrorMessage($code);
        }

        protected static function generatePrefix()
        {
            // Concrete\Core\File\Importer::generatePrefix();
            Concrete\Core\File\Importer::generatePrefix();
        }

        protected static function storeFile($prefix, $pointer, $filename, $fr = null)
        {
            // Concrete\Core\File\Importer::storeFile();
            Concrete\Core\File\Importer::storeFile($prefix, $pointer, $filename, $fr);
        }

        /**
         * Imports a local file into the system. The file must be added to this path
         * somehow. That's what happens in tools/files/importers/.
         * If a $fr (FileRecord) object is passed, we assign the newly imported FileVersion
         * object to that File. If not, we make a new filerecord.
         * @param string $pointer path to file
         * @param string $filename
         * @param FileRecord $fr
         * @return number Error Code | FileVersion
         */
        public static function import($pointer, $filename = null, $fr = null)
        {
            // Concrete\Core\File\Importer::import();
            Concrete\Core\File\Importer::import($pointer, $filename, $fr);
        }

    }

    class Group extends \Concrete\Core\User\Group\Group
    {

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\User\Group\Group::getPermissionObjectIdentifier();
            Concrete\Core\User\Group\Group::getPermissionObjectIdentifier();
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\User\Group\Group::getPermissionResponseClassName();
            Concrete\Core\User\Group\Group::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\User\Group\Group::getPermissionAssignmentClassName();
            Concrete\Core\User\Group\Group::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\User\Group\Group::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\User\Group\Group::getPermissionObjectKeyCategoryHandle();
        }

        public static function getByID($gID)
        {
            // Concrete\Core\User\Group\Group::getByID();
            Concrete\Core\User\Group\Group::getByID($gID);
        }

        public static function getByName($gName)
        {
            // Concrete\Core\User\Group\Group::getByName();
            Concrete\Core\User\Group\Group::getByName($gName);
        }

        public static function getByPath($gPath)
        {
            // Concrete\Core\User\Group\Group::getByPath();
            Concrete\Core\User\Group\Group::getByPath($gPath);
        }

        public static function getGroupMembers()
        {
            // Concrete\Core\User\Group\Group::getGroupMembers();
            Concrete\Core\User\Group\Group::getGroupMembers();
        }

        public static function getGroupMemberIDs()
        {
            // Concrete\Core\User\Group\Group::getGroupMemberIDs();
            Concrete\Core\User\Group\Group::getGroupMemberIDs();
        }

        public static function setPermissionsForObject($obj)
        {
            // Concrete\Core\User\Group\Group::setPermissionsForObject();
            Concrete\Core\User\Group\Group::setPermissionsForObject($obj);
        }

        public static function getGroupMembersNum()
        {
            // Concrete\Core\User\Group\Group::getGroupMembersNum();
            Concrete\Core\User\Group\Group::getGroupMembersNum();
        }

        /**
         * Deletes a group
         * @return void
         */
        public static function delete()
        {
            // Concrete\Core\User\Group\Group::delete();
            Concrete\Core\User\Group\Group::delete();
        }

        public static function rescanGroupPath()
        {
            // Concrete\Core\User\Group\Group::rescanGroupPath();
            Concrete\Core\User\Group\Group::rescanGroupPath();
        }

        public static function rescanGroupPathRecursive()
        {
            // Concrete\Core\User\Group\Group::rescanGroupPathRecursive();
            Concrete\Core\User\Group\Group::rescanGroupPathRecursive();
        }

        public static function inGroup()
        {
            // Concrete\Core\User\Group\Group::inGroup();
            Concrete\Core\User\Group\Group::inGroup();
        }

        public static function getGroupDateTimeEntered($user)
        {
            // Concrete\Core\User\Group\Group::getGroupDateTimeEntered();
            Concrete\Core\User\Group\Group::getGroupDateTimeEntered($user);
        }

        public static function getGroupID()
        {
            // Concrete\Core\User\Group\Group::getGroupID();
            Concrete\Core\User\Group\Group::getGroupID();
        }

        public static function getGroupName()
        {
            // Concrete\Core\User\Group\Group::getGroupName();
            Concrete\Core\User\Group\Group::getGroupName();
        }

        public static function getGroupPath()
        {
            // Concrete\Core\User\Group\Group::getGroupPath();
            Concrete\Core\User\Group\Group::getGroupPath();
        }

        public static function getParentGroups()
        {
            // Concrete\Core\User\Group\Group::getParentGroups();
            Concrete\Core\User\Group\Group::getParentGroups();
        }

        public static function getChildGroups()
        {
            // Concrete\Core\User\Group\Group::getChildGroups();
            Concrete\Core\User\Group\Group::getChildGroups();
        }

        public static function getParentGroup()
        {
            // Concrete\Core\User\Group\Group::getParentGroup();
            Concrete\Core\User\Group\Group::getParentGroup();
        }

        public static function getGroupDisplayName($includeHTML = "1")
        {
            // Concrete\Core\User\Group\Group::getGroupDisplayName();
            Concrete\Core\User\Group\Group::getGroupDisplayName($includeHTML);
        }

        public static function getGroupDescription()
        {
            // Concrete\Core\User\Group\Group::getGroupDescription();
            Concrete\Core\User\Group\Group::getGroupDescription();
        }

        /**
         * Gets the group start date
         * if user is specified, returns in the current user's timezone
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getGroupStartDate($type = "system")
        {
            // Concrete\Core\User\Group\Group::getGroupStartDate();
            Concrete\Core\User\Group\Group::getGroupStartDate($type);
        }

        /**
         * Gets the group end date
         * if user is specified, returns in the current user's timezone
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getGroupEndDate($type = "system")
        {
            // Concrete\Core\User\Group\Group::getGroupEndDate();
            Concrete\Core\User\Group\Group::getGroupEndDate($type);
        }

        public static function isGroupBadge()
        {
            // Concrete\Core\User\Group\Group::isGroupBadge();
            Concrete\Core\User\Group\Group::isGroupBadge();
        }

        public static function getGroupBadgeDescription()
        {
            // Concrete\Core\User\Group\Group::getGroupBadgeDescription();
            Concrete\Core\User\Group\Group::getGroupBadgeDescription();
        }

        public static function getGroupBadgeCommunityPointValue()
        {
            // Concrete\Core\User\Group\Group::getGroupBadgeCommunityPointValue();
            Concrete\Core\User\Group\Group::getGroupBadgeCommunityPointValue();
        }

        public static function getGroupBadgeImageID()
        {
            // Concrete\Core\User\Group\Group::getGroupBadgeImageID();
            Concrete\Core\User\Group\Group::getGroupBadgeImageID();
        }

        public static function isGroupAutomated()
        {
            // Concrete\Core\User\Group\Group::isGroupAutomated();
            Concrete\Core\User\Group\Group::isGroupAutomated();
        }

        public static function checkGroupAutomationOnRegister()
        {
            // Concrete\Core\User\Group\Group::checkGroupAutomationOnRegister();
            Concrete\Core\User\Group\Group::checkGroupAutomationOnRegister();
        }

        public static function checkGroupAutomationOnLogin()
        {
            // Concrete\Core\User\Group\Group::checkGroupAutomationOnLogin();
            Concrete\Core\User\Group\Group::checkGroupAutomationOnLogin();
        }

        public static function checkGroupAutomationOnJobRun()
        {
            // Concrete\Core\User\Group\Group::checkGroupAutomationOnJobRun();
            Concrete\Core\User\Group\Group::checkGroupAutomationOnJobRun();
        }

        public static function getGroupAutomationController()
        {
            // Concrete\Core\User\Group\Group::getGroupAutomationController();
            Concrete\Core\User\Group\Group::getGroupAutomationController();
        }

        public static function getGroupAutomationControllerFile()
        {
            // Concrete\Core\User\Group\Group::getGroupAutomationControllerFile();
            Concrete\Core\User\Group\Group::getGroupAutomationControllerFile();
        }

        public static function getGroupBadgeImageObject()
        {
            // Concrete\Core\User\Group\Group::getGroupBadgeImageObject();
            Concrete\Core\User\Group\Group::getGroupBadgeImageObject();
        }

        public static function isGroupExpirationEnabled()
        {
            // Concrete\Core\User\Group\Group::isGroupExpirationEnabled();
            Concrete\Core\User\Group\Group::isGroupExpirationEnabled();
        }

        public static function getGroupExpirationMethod()
        {
            // Concrete\Core\User\Group\Group::getGroupExpirationMethod();
            Concrete\Core\User\Group\Group::getGroupExpirationMethod();
        }

        public static function getGroupExpirationDateTime()
        {
            // Concrete\Core\User\Group\Group::getGroupExpirationDateTime();
            Concrete\Core\User\Group\Group::getGroupExpirationDateTime();
        }

        public static function getGroupExpirationAction()
        {
            // Concrete\Core\User\Group\Group::getGroupExpirationAction();
            Concrete\Core\User\Group\Group::getGroupExpirationAction();
        }

        public static function getGroupExpirationIntervalDays()
        {
            // Concrete\Core\User\Group\Group::getGroupExpirationIntervalDays();
            Concrete\Core\User\Group\Group::getGroupExpirationIntervalDays();
        }

        public static function getGroupExpirationIntervalHours()
        {
            // Concrete\Core\User\Group\Group::getGroupExpirationIntervalHours();
            Concrete\Core\User\Group\Group::getGroupExpirationIntervalHours();
        }

        public static function getGroupExpirationIntervalMinutes()
        {
            // Concrete\Core\User\Group\Group::getGroupExpirationIntervalMinutes();
            Concrete\Core\User\Group\Group::getGroupExpirationIntervalMinutes();
        }

        public static function getPackageID()
        {
            // Concrete\Core\User\Group\Group::getPackageID();
            Concrete\Core\User\Group\Group::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\User\Group\Group::getPackageHandle();
            Concrete\Core\User\Group\Group::getPackageHandle();
        }

        public static function update($gName, $gDescription)
        {
            // Concrete\Core\User\Group\Group::update();
            Concrete\Core\User\Group\Group::update($gName, $gDescription);
        }

        /** Creates a new user group.
         * @param string $gName
         * @param string $gDescription
         * @return Group
         */
        public static function add($gName, $gDescription, $parentGroup = null, $pkg = null, $gID = null)
        {
            // Concrete\Core\User\Group\Group::add();
            Concrete\Core\User\Group\Group::add($gName, $gDescription, $parentGroup, $pkg, $gID);
        }

        public static function getBadges()
        {
            // Concrete\Core\User\Group\Group::getBadges();
            Concrete\Core\User\Group\Group::getBadges();
        }

        protected static function getAutomationControllers($column, $excludeUser = null)
        {
            // Concrete\Core\User\Group\Group::getAutomationControllers();
            Concrete\Core\User\Group\Group::getAutomationControllers($column, $excludeUser);
        }

        public static function getAutomatedOnRegisterGroupControllers($u = null)
        {
            // Concrete\Core\User\Group\Group::getAutomatedOnRegisterGroupControllers();
            Concrete\Core\User\Group\Group::getAutomatedOnRegisterGroupControllers($u);
        }

        public static function getAutomatedOnLoginGroupControllers($u = null)
        {
            // Concrete\Core\User\Group\Group::getAutomatedOnLoginGroupControllers();
            Concrete\Core\User\Group\Group::getAutomatedOnLoginGroupControllers($u);
        }

        public static function getAutomatedOnJobRunGroupControllers()
        {
            // Concrete\Core\User\Group\Group::getAutomatedOnJobRunGroupControllers();
            Concrete\Core\User\Group\Group::getAutomatedOnJobRunGroupControllers();
        }

        public static function clearBadgeOptions()
        {
            // Concrete\Core\User\Group\Group::clearBadgeOptions();
            Concrete\Core\User\Group\Group::clearBadgeOptions();
        }

        public static function clearAutomationOptions()
        {
            // Concrete\Core\User\Group\Group::clearAutomationOptions();
            Concrete\Core\User\Group\Group::clearAutomationOptions();
        }

        public static function removeGroupExpiration()
        {
            // Concrete\Core\User\Group\Group::removeGroupExpiration();
            Concrete\Core\User\Group\Group::removeGroupExpiration();
        }

        public static function setBadgeOptions($gBadgeFID, $gBadgeDescription, $gBadgeCommunityPointValue)
        {
            // Concrete\Core\User\Group\Group::setBadgeOptions();
            Concrete\Core\User\Group\Group::setBadgeOptions($gBadgeFID, $gBadgeDescription, $gBadgeCommunityPointValue);
        }

        public static function setAutomationOptions($gCheckAutomationOnRegister, $gCheckAutomationOnLogin, $gCheckAutomationOnJobRun)
        {
            // Concrete\Core\User\Group\Group::setAutomationOptions();
            Concrete\Core\User\Group\Group::setAutomationOptions($gCheckAutomationOnRegister, $gCheckAutomationOnLogin, $gCheckAutomationOnJobRun);
        }

        public static function setGroupExpirationByDateTime($datetime, $action)
        {
            // Concrete\Core\User\Group\Group::setGroupExpirationByDateTime();
            Concrete\Core\User\Group\Group::setGroupExpirationByDateTime($datetime, $action);
        }

        public static function setGroupExpirationByInterval($days, $hours, $minutes, $action)
        {
            // Concrete\Core\User\Group\Group::setGroupExpirationByInterval();
            Concrete\Core\User\Group\Group::setGroupExpirationByInterval($days, $hours, $minutes, $action);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class GroupSet extends \Concrete\Core\User\Group\GroupSet
    {

        public static function getList()
        {
            // Concrete\Core\User\Group\GroupSet::getList();
            Concrete\Core\User\Group\GroupSet::getList();
        }

        public static function getByID($gsID)
        {
            // Concrete\Core\User\Group\GroupSet::getByID();
            Concrete\Core\User\Group\GroupSet::getByID($gsID);
        }

        public static function getByName($gsName)
        {
            // Concrete\Core\User\Group\GroupSet::getByName();
            Concrete\Core\User\Group\GroupSet::getByName($gsName);
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\User\Group\GroupSet::getListByPackage();
            Concrete\Core\User\Group\GroupSet::getListByPackage($pkg);
        }

        public static function getGroupSetID()
        {
            // Concrete\Core\User\Group\GroupSet::getGroupSetID();
            Concrete\Core\User\Group\GroupSet::getGroupSetID();
        }

        public static function getGroupSetName()
        {
            // Concrete\Core\User\Group\GroupSet::getGroupSetName();
            Concrete\Core\User\Group\GroupSet::getGroupSetName();
        }

        public static function getPackageID()
        {
            // Concrete\Core\User\Group\GroupSet::getPackageID();
            Concrete\Core\User\Group\GroupSet::getPackageID();
        }

        /** Returns the display name for this group set (localized and escaped accordingly to $format)
         * @param string $format = 'html'
         *	Escape the result in html format (if $format is 'html').
         *	If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getGroupSetDisplayName($format = "html")
        {
            // Concrete\Core\User\Group\GroupSet::getGroupSetDisplayName();
            Concrete\Core\User\Group\GroupSet::getGroupSetDisplayName($format);
        }

        public static function updateGroupSetName($gsName)
        {
            // Concrete\Core\User\Group\GroupSet::updateGroupSetName();
            Concrete\Core\User\Group\GroupSet::updateGroupSetName($gsName);
        }

        public static function addGroup(Concrete\Core\User\Group\Group $g)
        {
            // Concrete\Core\User\Group\GroupSet::addGroup();
            Concrete\Core\User\Group\GroupSet::addGroup($g);
        }

        public static function add($gsName, $pkg = null)
        {
            // Concrete\Core\User\Group\GroupSet::add();
            Concrete\Core\User\Group\GroupSet::add($gsName, $pkg);
        }

        public static function clearGroups()
        {
            // Concrete\Core\User\Group\GroupSet::clearGroups();
            Concrete\Core\User\Group\GroupSet::clearGroups();
        }

        public static function getGroups()
        {
            // Concrete\Core\User\Group\GroupSet::getGroups();
            Concrete\Core\User\Group\GroupSet::getGroups();
        }

        public static function contains(Concrete\Core\User\Group\Group $g)
        {
            // Concrete\Core\User\Group\GroupSet::contains();
            Concrete\Core\User\Group\GroupSet::contains($g);
        }

        public static function delete()
        {
            // Concrete\Core\User\Group\GroupSet::delete();
            Concrete\Core\User\Group\GroupSet::delete();
        }

        public static function removeGroup(Concrete\Core\User\Group\Group $g)
        {
            // Concrete\Core\User\Group\GroupSet::removeGroup();
            Concrete\Core\User\Group\GroupSet::removeGroup($g);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class GroupSetList extends \Concrete\Core\User\Group\GroupSetList
    {

        public static function __construct()
        {
            // Concrete\Core\User\Group\GroupSetList::__construct();
            Concrete\Core\User\Group\GroupSetList::__construct();
        }

        public static function get()
        {
            // Concrete\Core\User\Group\GroupSetList::get();
            Concrete\Core\User\Group\GroupSetList::get();
        }

        public static function getTotal()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getTotal();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getTotal();
        }

        public static function debug($dbg = "1")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug($dbg);
        }

        protected static function setQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery($query);
        }

        protected static function getQuery()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
        }

        public static function addToQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery($query);
        }

        protected static function setupAutoSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
        }

        protected static function executeBase()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
        }

        protected static function setupSortByString()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
        }

        protected static function setupAttributeSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
        }

        /**
         * Adds a filter to this item list
         */
        public static function filter($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter($column, $value, $comparison);
        }

        public static function getSearchResultsClass($field)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass($field);
        }

        public static function sortBy($key, $dir = "asc")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy($key, $dir);
        }

        public static function groupBy($key)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy($key);
        }

        public static function having($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having($column, $value, $comparison);
        }

        public static function getSortByURL($column, $dir = "asc", $baseURL = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL($column, $dir, $baseURL, $additionalVars);
        }

        protected static function setupAttributeFilters($join)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters($join);
        }

        public static function filterByAttribute($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute($column, $value, $comparison);
        }

        public static function enableStickySearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest($namespace);
        }

        public static function getQueryStringPagingVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
        }

        public static function getQueryStringSortVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
        }

        public static function getQueryStringSortDirectionVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
        }

        protected static function getStickySearchNameSpace()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
        }

        public static function resetSearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest($namespace);
        }

        public static function addToSearchRequest($key, $value)
        {
            // Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest($key, $value);
        }

        public static function getSearchRequest()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
        }

        public static function setItemsPerPage($num)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage($num);
        }

        public static function getItemsPerPage()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
        }

        public static function setItems($items)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItems();
            Concrete\Core\Foundation\Collection\ItemList::setItems($items);
        }

        public static function setNameSpace($ns)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::setNameSpace($ns);
        }

        /**
         * Returns an array of object by "page"
         */
        public static function getPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPage();
            Concrete\Core\Foundation\Collection\ItemList::getPage($page);
        }

        protected static function setCurrentPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setCurrentPage();
            Concrete\Core\Foundation\Collection\ItemList::setCurrentPage($page);
        }

        /**
         * Displays summary text about a list
         */
        public static function displaySummary($right_content = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displaySummary();
            Concrete\Core\Foundation\Collection\ItemList::displaySummary($right_content);
        }

        public static function isActiveSortColumn($column)
        {
            // Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn($column);
        }

        public static function getActiveSortColumn()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
        }

        public static function getActiveSortDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
        }

        public static function requiresPaging()
        {
            // Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
            Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
        }

        public static function getPagination($url = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPagination();
            Concrete\Core\Foundation\Collection\ItemList::getPagination($url, $additionalVars);
        }

        /**
         * Gets paging that works in our new format */
        public static function displayPagingV2($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPagingV2();
            Concrete\Core\Foundation\Collection\ItemList::displayPagingV2($script, $return, $additionalVars);
        }

        /**
         * Gets standard HTML to display paging */
        public static function displayPaging($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPaging();
            Concrete\Core\Foundation\Collection\ItemList::displayPaging($script, $return, $additionalVars);
        }

        /**
         * Returns an object with properties useful for paging
         */
        public static function getSummary()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSummary();
            Concrete\Core\Foundation\Collection\ItemList::getSummary();
        }

        public static function getSortBy()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortBy();
            Concrete\Core\Foundation\Collection\ItemList::getSortBy();
        }

        public static function getSortByDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
            Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
        }

        /**
         * Sets up a multiple columns to search by. Each argument is taken "as-is" (including asc or desc) and concatenated with commas
         * Note that this is overrides any previous sortByMultiple() call, and all sortBy() calls. Alternatively, you can pass a single
         * array with multiple columns to sort by as its values.
         * e.g. $list->sortByMultiple('columna desc', 'columnb asc');
         * or $list->sortByMultiple(array('columna desc', 'columnb asc'));
         */
        public static function sortByMultiple()
        {
            // Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
            Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
        }

    }

    class GroupList extends \Concrete\Core\User\Group\GroupList
    {

        public static function includeAllGroups()
        {
            // Concrete\Core\User\Group\GroupList::includeAllGroups();
            Concrete\Core\User\Group\GroupList::includeAllGroups();
        }

        public static function filterByKeywords($kw)
        {
            // Concrete\Core\User\Group\GroupList::filterByKeywords();
            Concrete\Core\User\Group\GroupList::filterByKeywords($kw);
        }

        public static function filterByAssignable()
        {
            // Concrete\Core\User\Group\GroupList::filterByAssignable();
            Concrete\Core\User\Group\GroupList::filterByAssignable();
        }

        public static function filterByUserID($uID)
        {
            // Concrete\Core\User\Group\GroupList::filterByUserID();
            Concrete\Core\User\Group\GroupList::filterByUserID($uID);
        }

        public static function updateItemsPerPage($num)
        {
            // Concrete\Core\User\Group\GroupList::updateItemsPerPage();
            Concrete\Core\User\Group\GroupList::updateItemsPerPage($num);
        }

        public static function __construct()
        {
            // Concrete\Core\User\Group\GroupList::__construct();
            Concrete\Core\User\Group\GroupList::__construct();
        }

        public static function get($itemsToGet = 100, $offset = null)
        {
            // Concrete\Core\User\Group\GroupList::get();
            Concrete\Core\User\Group\GroupList::get($itemsToGet, $offset);
        }

        public static function getTotal()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getTotal();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getTotal();
        }

        public static function debug($dbg = "1")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug($dbg);
        }

        protected static function setQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery($query);
        }

        protected static function getQuery()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
        }

        public static function addToQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery($query);
        }

        protected static function setupAutoSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
        }

        protected static function executeBase()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
        }

        protected static function setupSortByString()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
        }

        protected static function setupAttributeSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
        }

        /**
         * Adds a filter to this item list
         */
        public static function filter($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter($column, $value, $comparison);
        }

        public static function getSearchResultsClass($field)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass($field);
        }

        public static function sortBy($key, $dir = "asc")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy($key, $dir);
        }

        public static function groupBy($key)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy($key);
        }

        public static function having($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having($column, $value, $comparison);
        }

        public static function getSortByURL($column, $dir = "asc", $baseURL = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL($column, $dir, $baseURL, $additionalVars);
        }

        protected static function setupAttributeFilters($join)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters($join);
        }

        public static function filterByAttribute($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute($column, $value, $comparison);
        }

        public static function enableStickySearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest($namespace);
        }

        public static function getQueryStringPagingVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
        }

        public static function getQueryStringSortVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
        }

        public static function getQueryStringSortDirectionVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
        }

        protected static function getStickySearchNameSpace()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
        }

        public static function resetSearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest($namespace);
        }

        public static function addToSearchRequest($key, $value)
        {
            // Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest($key, $value);
        }

        public static function getSearchRequest()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
        }

        public static function setItemsPerPage($num)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage($num);
        }

        public static function getItemsPerPage()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
        }

        public static function setItems($items)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItems();
            Concrete\Core\Foundation\Collection\ItemList::setItems($items);
        }

        public static function setNameSpace($ns)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::setNameSpace($ns);
        }

        /**
         * Returns an array of object by "page"
         */
        public static function getPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPage();
            Concrete\Core\Foundation\Collection\ItemList::getPage($page);
        }

        protected static function setCurrentPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setCurrentPage();
            Concrete\Core\Foundation\Collection\ItemList::setCurrentPage($page);
        }

        /**
         * Displays summary text about a list
         */
        public static function displaySummary($right_content = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displaySummary();
            Concrete\Core\Foundation\Collection\ItemList::displaySummary($right_content);
        }

        public static function isActiveSortColumn($column)
        {
            // Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn($column);
        }

        public static function getActiveSortColumn()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
        }

        public static function getActiveSortDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
        }

        public static function requiresPaging()
        {
            // Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
            Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
        }

        public static function getPagination($url = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPagination();
            Concrete\Core\Foundation\Collection\ItemList::getPagination($url, $additionalVars);
        }

        /**
         * Gets paging that works in our new format */
        public static function displayPagingV2($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPagingV2();
            Concrete\Core\Foundation\Collection\ItemList::displayPagingV2($script, $return, $additionalVars);
        }

        /**
         * Gets standard HTML to display paging */
        public static function displayPaging($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPaging();
            Concrete\Core\Foundation\Collection\ItemList::displayPaging($script, $return, $additionalVars);
        }

        /**
         * Returns an object with properties useful for paging
         */
        public static function getSummary()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSummary();
            Concrete\Core\Foundation\Collection\ItemList::getSummary();
        }

        public static function getSortBy()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortBy();
            Concrete\Core\Foundation\Collection\ItemList::getSortBy();
        }

        public static function getSortByDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
            Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
        }

        /**
         * Sets up a multiple columns to search by. Each argument is taken "as-is" (including asc or desc) and concatenated with commas
         * Note that this is overrides any previous sortByMultiple() call, and all sortBy() calls. Alternatively, you can pass a single
         * array with multiple columns to sort by as its values.
         * e.g. $list->sortByMultiple('columna desc', 'columnb asc');
         * or $list->sortByMultiple(array('columna desc', 'columnb asc'));
         */
        public static function sortByMultiple()
        {
            // Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
            Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
        }

    }

    /**
     *
     * An object that allows a filtered list of files to be returned.
     * @package Files
     *
     */
    class FileList extends \Concrete\Core\File\FileList
    {

        public static function __call($nm, $a)
        {
            // Concrete\Core\File\FileList::__call();
            Concrete\Core\File\FileList::__call($nm, $a);
        }

        /**
         * Filters by file extension
         * @param mixed $extension
         */
        public static function filterByExtension($ext)
        {
            // Concrete\Core\File\FileList::filterByExtension();
            Concrete\Core\File\FileList::filterByExtension($ext);
        }

        /**
         * Filters by type of file
         * @param mixed $type
         */
        public static function filterByType($type)
        {
            // Concrete\Core\File\FileList::filterByType();
            Concrete\Core\File\FileList::filterByType($type);
        }

        /**
         * Filters by "keywords" (which searches everything including filenames, title, tags, users who uploaded the file, tags)
         */
        public static function filterByKeywords($keywords)
        {
            // Concrete\Core\File\FileList::filterByKeywords();
            Concrete\Core\File\FileList::filterByKeywords($keywords);
        }

        public static function filterBySet($fs)
        {
            // Concrete\Core\File\FileList::filterBySet();
            Concrete\Core\File\FileList::filterBySet($fs);
        }

        public static function export($xml)
        {
            // Concrete\Core\File\FileList::export();
            Concrete\Core\File\FileList::export($xml);
        }

        public static function exportArchive($archive)
        {
            // Concrete\Core\File\FileList::exportArchive();
            Concrete\Core\File\FileList::exportArchive($archive);
        }

        protected static function setupFileSetFilters()
        {
            // Concrete\Core\File\FileList::setupFileSetFilters();
            Concrete\Core\File\FileList::setupFileSetFilters();
        }

        /**
         * Filters the file list by file size (in kilobytes)
         */
        public static function filterBySize($from, $to)
        {
            // Concrete\Core\File\FileList::filterBySize();
            Concrete\Core\File\FileList::filterBySize($from, $to);
        }

        /**
         * Filters by public date
         * @param string $date
         */
        public static function filterByDateAdded($date, $comparison = "=")
        {
            // Concrete\Core\File\FileList::filterByDateAdded();
            Concrete\Core\File\FileList::filterByDateAdded($date, $comparison);
        }

        public static function filterByOriginalPageID($ocID)
        {
            // Concrete\Core\File\FileList::filterByOriginalPageID();
            Concrete\Core\File\FileList::filterByOriginalPageID($ocID);
        }

        /**
         * filters a FileList by the uID of the approving User
         * @param int $uID
         * @return void
         * @since 5.4.1.1+
         */
        public static function filterByApproverUID($uID)
        {
            // Concrete\Core\File\FileList::filterByApproverUID();
            Concrete\Core\File\FileList::filterByApproverUID($uID);
        }

        /**
         * filters a FileList by the uID of the owning User
         * @param int $uID
         * @return void
         * @since 5.4.1.1+
         */
        public static function filterByAuthorUID($uID)
        {
            // Concrete\Core\File\FileList::filterByAuthorUID();
            Concrete\Core\File\FileList::filterByAuthorUID($uID);
        }

        public static function setPermissionLevel($plevel)
        {
            // Concrete\Core\File\FileList::setPermissionLevel();
            Concrete\Core\File\FileList::setPermissionLevel($plevel);
        }

        /**
         * Filters by tag
         * @param string $tag
         */
        public static function filterByTag($tag = null)
        {
            // Concrete\Core\File\FileList::filterByTag();
            Concrete\Core\File\FileList::filterByTag($tag);
        }

        protected static function setBaseQuery()
        {
            // Concrete\Core\File\FileList::setBaseQuery();
            Concrete\Core\File\FileList::setBaseQuery();
        }

        protected static function setupFilePermissions()
        {
            // Concrete\Core\File\FileList::setupFilePermissions();
            Concrete\Core\File\FileList::setupFilePermissions();
        }

        /**
         * Returns an array of file objects based on current settings
         */
        public static function get($itemsToGet = null, $offset = null)
        {
            // Concrete\Core\File\FileList::get();
            Concrete\Core\File\FileList::get($itemsToGet, $offset);
        }

        public static function getTotal()
        {
            // Concrete\Core\File\FileList::getTotal();
            Concrete\Core\File\FileList::getTotal();
        }

        protected static function createQuery()
        {
            // Concrete\Core\File\FileList::createQuery();
            Concrete\Core\File\FileList::createQuery();
        }

        public static function sortByAttributeKey($key, $order = "asc")
        {
            // Concrete\Core\File\FileList::sortByAttributeKey();
            Concrete\Core\File\FileList::sortByAttributeKey($key, $order);
        }

        public static function sortByFileSetDisplayOrder()
        {
            // Concrete\Core\File\FileList::sortByFileSetDisplayOrder();
            Concrete\Core\File\FileList::sortByFileSetDisplayOrder();
        }

        public static function getExtensionList()
        {
            // Concrete\Core\File\FileList::getExtensionList();
            Concrete\Core\File\FileList::getExtensionList();
        }

        public static function getTypeList()
        {
            // Concrete\Core\File\FileList::getTypeList();
            Concrete\Core\File\FileList::getTypeList();
        }

        public static function debug($dbg = "1")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug($dbg);
        }

        protected static function setQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery($query);
        }

        protected static function getQuery()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
        }

        public static function addToQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery($query);
        }

        protected static function setupAutoSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
        }

        protected static function executeBase()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
        }

        protected static function setupSortByString()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
        }

        protected static function setupAttributeSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
        }

        /**
         * Adds a filter to this item list
         */
        public static function filter($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter($column, $value, $comparison);
        }

        public static function getSearchResultsClass($field)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass($field);
        }

        public static function sortBy($key, $dir = "asc")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy($key, $dir);
        }

        public static function groupBy($key)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy($key);
        }

        public static function having($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having($column, $value, $comparison);
        }

        public static function getSortByURL($column, $dir = "asc", $baseURL = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL($column, $dir, $baseURL, $additionalVars);
        }

        protected static function setupAttributeFilters($join)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters($join);
        }

        public static function filterByAttribute($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute($column, $value, $comparison);
        }

        public static function enableStickySearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest($namespace);
        }

        public static function getQueryStringPagingVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
        }

        public static function getQueryStringSortVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
        }

        public static function getQueryStringSortDirectionVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
        }

        protected static function getStickySearchNameSpace()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
        }

        public static function resetSearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest($namespace);
        }

        public static function addToSearchRequest($key, $value)
        {
            // Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest($key, $value);
        }

        public static function getSearchRequest()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
        }

        public static function setItemsPerPage($num)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage($num);
        }

        public static function getItemsPerPage()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
        }

        public static function setItems($items)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItems();
            Concrete\Core\Foundation\Collection\ItemList::setItems($items);
        }

        public static function setNameSpace($ns)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::setNameSpace($ns);
        }

        /**
         * Returns an array of object by "page"
         */
        public static function getPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPage();
            Concrete\Core\Foundation\Collection\ItemList::getPage($page);
        }

        protected static function setCurrentPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setCurrentPage();
            Concrete\Core\Foundation\Collection\ItemList::setCurrentPage($page);
        }

        /**
         * Displays summary text about a list
         */
        public static function displaySummary($right_content = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displaySummary();
            Concrete\Core\Foundation\Collection\ItemList::displaySummary($right_content);
        }

        public static function isActiveSortColumn($column)
        {
            // Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn($column);
        }

        public static function getActiveSortColumn()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
        }

        public static function getActiveSortDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
        }

        public static function requiresPaging()
        {
            // Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
            Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
        }

        public static function getPagination($url = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPagination();
            Concrete\Core\Foundation\Collection\ItemList::getPagination($url, $additionalVars);
        }

        /**
         * Gets paging that works in our new format */
        public static function displayPagingV2($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPagingV2();
            Concrete\Core\Foundation\Collection\ItemList::displayPagingV2($script, $return, $additionalVars);
        }

        /**
         * Gets standard HTML to display paging */
        public static function displayPaging($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPaging();
            Concrete\Core\Foundation\Collection\ItemList::displayPaging($script, $return, $additionalVars);
        }

        /**
         * Returns an object with properties useful for paging
         */
        public static function getSummary()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSummary();
            Concrete\Core\Foundation\Collection\ItemList::getSummary();
        }

        public static function getSortBy()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortBy();
            Concrete\Core\Foundation\Collection\ItemList::getSortBy();
        }

        public static function getSortByDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
            Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
        }

        /**
         * Sets up a multiple columns to search by. Each argument is taken "as-is" (including asc or desc) and concatenated with commas
         * Note that this is overrides any previous sortByMultiple() call, and all sortBy() calls. Alternatively, you can pass a single
         * array with multiple columns to sort by as its values.
         * e.g. $list->sortByMultiple('columna desc', 'columnb asc');
         * or $list->sortByMultiple(array('columna desc', 'columnb asc'));
         */
        public static function sortByMultiple()
        {
            // Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
            Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
        }

    }

    /**
     *
     * The job class is essentially sub-dispatcher for certain maintenance tasks that need to be run at specified intervals. Examples include indexing a search engine or generating a sitemap page.
     * @package Utilities
     * @author Andrew Embler <andrew@concrete5.org>
     * @author Tony Trupp <tony@concrete5.org>
     * @link http://www.concrete5.org
     * @license http://www.opensource.org/licenses/mit-license.php MIT
     *
     */
    class QueueableJob extends \Concrete\Core\Job\QueueableJob
    {

        public static function getJobQueueBatchSize()
        {
            // Concrete\Core\Job\QueueableJob::getJobQueueBatchSize();
            Concrete\Core\Job\QueueableJob::getJobQueueBatchSize();
        }

        public static function run()
        {
            // Concrete\Core\Job\QueueableJob::run();
            Concrete\Core\Job\QueueableJob::run();
        }

        public static function getQueueObject()
        {
            // Concrete\Core\Job\QueueableJob::getQueueObject();
            Concrete\Core\Job\QueueableJob::getQueueObject();
        }

        public static function reset()
        {
            // Concrete\Core\Job\QueueableJob::reset();
            Concrete\Core\Job\QueueableJob::reset();
        }

        public static function markStarted()
        {
            // Concrete\Core\Job\QueueableJob::markStarted();
            Concrete\Core\Job\QueueableJob::markStarted();
        }

        public static function markCompleted($code = null, $message = null)
        {
            // Concrete\Core\Job\QueueableJob::markCompleted();
            Concrete\Core\Job\QueueableJob::markCompleted($code, $message);
        }

        /**
         * Executejob for queueable jobs actually starts the queue, runs, and ends all in one function. This happens if we run a job in legacy mode.
         */
        public static function executeJob()
        {
            // Concrete\Core\Job\QueueableJob::executeJob();
            Concrete\Core\Job\QueueableJob::executeJob();
        }

        public static function getJobHandle()
        {
            // Concrete\Core\Job\Job::getJobHandle();
            Concrete\Core\Job\Job::getJobHandle();
        }

        public static function getJobID()
        {
            // Concrete\Core\Job\Job::getJobID();
            Concrete\Core\Job\Job::getJobID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Job\Job::getPackageHandle();
            Concrete\Core\Job\Job::getPackageHandle();
        }

        public static function getJobLastStatusCode()
        {
            // Concrete\Core\Job\Job::getJobLastStatusCode();
            Concrete\Core\Job\Job::getJobLastStatusCode();
        }

        public static function didFail()
        {
            // Concrete\Core\Job\Job::didFail();
            Concrete\Core\Job\Job::didFail();
        }

        public static function canUninstall()
        {
            // Concrete\Core\Job\Job::canUninstall();
            Concrete\Core\Job\Job::canUninstall();
        }

        public static function supportsQueue()
        {
            // Concrete\Core\Job\Job::supportsQueue();
            Concrete\Core\Job\Job::supportsQueue();
        }

        public static function jobClassLocations()
        {
            // Concrete\Core\Job\Job::jobClassLocations();
            Concrete\Core\Job\Job::jobClassLocations();
        }

        public static function getJobDateLastRun()
        {
            // Concrete\Core\Job\Job::getJobDateLastRun();
            Concrete\Core\Job\Job::getJobDateLastRun();
        }

        public static function getJobStatus()
        {
            // Concrete\Core\Job\Job::getJobStatus();
            Concrete\Core\Job\Job::getJobStatus();
        }

        public static function getJobLastStatusText()
        {
            // Concrete\Core\Job\Job::getJobLastStatusText();
            Concrete\Core\Job\Job::getJobLastStatusText();
        }

        public static function authenticateRequest($auth)
        {
            // Concrete\Core\Job\Job::authenticateRequest();
            Concrete\Core\Job\Job::authenticateRequest($auth);
        }

        public static function generateAuth()
        {
            // Concrete\Core\Job\Job::generateAuth();
            Concrete\Core\Job\Job::generateAuth();
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Job\Job::exportList();
            Concrete\Core\Job\Job::exportList($xml);
        }

        public static function getList($scheduledOnly = null)
        {
            // Concrete\Core\Job\Job::getList();
            Concrete\Core\Job\Job::getList($scheduledOnly);
        }

        public static function getByID($jID = null)
        {
            // Concrete\Core\Job\Job::getByID();
            Concrete\Core\Job\Job::getByID($jID);
        }

        public static function getByHandle($jHandle = null)
        {
            // Concrete\Core\Job\Job::getByHandle();
            Concrete\Core\Job\Job::getByHandle($jHandle);
        }

        public static function getJobObjByHandle($jHandle = null, $jobData = null)
        {
            // Concrete\Core\Job\Job::getJobObjByHandle();
            Concrete\Core\Job\Job::getJobObjByHandle($jHandle, $jobData);
        }

        protected static function getClassName($jHandle)
        {
            // Concrete\Core\Job\Job::getClassName();
            Concrete\Core\Job\Job::getClassName($jHandle);
        }

        public static function getAvailableList($includeConcreteDirJobs = 1)
        {
            // Concrete\Core\Job\Job::getAvailableList();
            Concrete\Core\Job\Job::getAvailableList($includeConcreteDirJobs);
        }

        public static function setJobStatus($jStatus = "ENABLED")
        {
            // Concrete\Core\Job\Job::setJobStatus();
            Concrete\Core\Job\Job::setJobStatus($jStatus);
        }

        public static function installByHandle($jHandle = null)
        {
            // Concrete\Core\Job\Job::installByHandle();
            Concrete\Core\Job\Job::installByHandle($jHandle);
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Job\Job::getListByPackage();
            Concrete\Core\Job\Job::getListByPackage($pkg);
        }

        public static function installByPackage($jHandle, $pkg)
        {
            // Concrete\Core\Job\Job::installByPackage();
            Concrete\Core\Job\Job::installByPackage($jHandle, $pkg);
        }

        public static function install()
        {
            // Concrete\Core\Job\Job::install();
            Concrete\Core\Job\Job::install();
        }

        public static function uninstall()
        {
            // Concrete\Core\Job\Job::uninstall();
            Concrete\Core\Job\Job::uninstall();
        }

        /**
         * Removes Job log entries
         */
        public static function clearLog()
        {
            // Concrete\Core\Job\Job::clearLog();
            Concrete\Core\Job\Job::clearLog();
        }

        public static function isScheduledForNow()
        {
            // Concrete\Core\Job\Job::isScheduledForNow();
            Concrete\Core\Job\Job::isScheduledForNow();
        }

        public static function setSchedule($scheduled, $interval, $value)
        {
            // Concrete\Core\Job\Job::setSchedule();
            Concrete\Core\Job\Job::setSchedule($scheduled, $interval, $value);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class Permissions extends \Concrete\Core\Permission\Checker
    {

        /**
         * Checks to see if there is a fatal error with this particular permission call.
         */
        public static function isError()
        {
            // Concrete\Core\Permission\Checker::isError();
            Concrete\Core\Permission\Checker::isError();
        }

        /**
         * Returns the error code if there is one
         */
        public static function getError()
        {
            // Concrete\Core\Permission\Checker::getError();
            Concrete\Core\Permission\Checker::getError();
        }

        /**
         * Legacy
         * @private
         */
        public static function getOriginalObject()
        {
            // Concrete\Core\Permission\Checker::getOriginalObject();
            Concrete\Core\Permission\Checker::getOriginalObject();
        }

        public static function __construct($object = null)
        {
            // Concrete\Core\Permission\Checker::__construct();
            Concrete\Core\Permission\Checker::__construct($object);
        }

        public static function getResponseObject()
        {
            // Concrete\Core\Permission\Checker::getResponseObject();
            Concrete\Core\Permission\Checker::getResponseObject();
        }

        /**
         * We take any permissions function run on the permissions class and send it into the category
         * object
         */
        public static function __call($f, $a)
        {
            // Concrete\Core\Permission\Checker::__call();
            Concrete\Core\Permission\Checker::__call($f, $a);
        }

    }

    class PermissionKey extends \Concrete\Core\Permission\Key\Key
    {

        public static function getSupportedAccessTypes()
        {
            // Concrete\Core\Permission\Key\Key::getSupportedAccessTypes();
            Concrete\Core\Permission\Key\Key::getSupportedAccessTypes();
        }

        /**
         * Returns whether a permission key can start a workflow
         */
        public static function canPermissionKeyTriggerWorkflow()
        {
            // Concrete\Core\Permission\Key\Key::canPermissionKeyTriggerWorkflow();
            Concrete\Core\Permission\Key\Key::canPermissionKeyTriggerWorkflow();
        }

        /**
         * Returns whether a permission key has a custom class.
         */
        public static function permissionKeyHasCustomClass()
        {
            // Concrete\Core\Permission\Key\Key::permissionKeyHasCustomClass();
            Concrete\Core\Permission\Key\Key::permissionKeyHasCustomClass();
        }

        /**
         * Returns the name for this permission key
         */
        public static function getPermissionKeyName()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionKeyName();
            Concrete\Core\Permission\Key\Key::getPermissionKeyName();
        }

        /** Returns the display name for this permission key (localized and escaped accordingly to $format)
         * @param string $format = 'html'
         *	Escape the result in html format (if $format is 'html').
         *	If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getPermissionKeyDisplayName($format = "html")
        {
            // Concrete\Core\Permission\Key\Key::getPermissionKeyDisplayName();
            Concrete\Core\Permission\Key\Key::getPermissionKeyDisplayName($format);
        }

        /**
         * Returns the handle for this permission key
         */
        public static function getPermissionKeyHandle()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionKeyHandle();
            Concrete\Core\Permission\Key\Key::getPermissionKeyHandle();
        }

        /**
         * Returns the description for this permission key
         */
        public static function getPermissionKeyDescription()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionKeyDescription();
            Concrete\Core\Permission\Key\Key::getPermissionKeyDescription();
        }

        /** Returns the display description for this permission key (localized and escaped accordingly to $format)
         * @param string $format = 'html'
         *	Escape the result in html format (if $format is 'html').
         *	If $format is 'text' or any other value, the display description won't be escaped.
         * @return string
         */
        public static function getPermissionKeyDisplayDescription($format = "html")
        {
            // Concrete\Core\Permission\Key\Key::getPermissionKeyDisplayDescription();
            Concrete\Core\Permission\Key\Key::getPermissionKeyDisplayDescription($format);
        }

        /**
         * Returns the ID for this permission key
         */
        public static function getPermissionKeyID()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionKeyID();
            Concrete\Core\Permission\Key\Key::getPermissionKeyID();
        }

        public static function getPermissionKeyCategoryID()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionKeyCategoryID();
            Concrete\Core\Permission\Key\Key::getPermissionKeyCategoryID();
        }

        public static function getPermissionKeyCategoryHandle()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionKeyCategoryHandle();
            Concrete\Core\Permission\Key\Key::getPermissionKeyCategoryHandle();
        }

        public static function setPermissionObject($object)
        {
            // Concrete\Core\Permission\Key\Key::setPermissionObject();
            Concrete\Core\Permission\Key\Key::setPermissionObject($object);
        }

        public static function getPermissionObjectToCheck()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionObjectToCheck();
            Concrete\Core\Permission\Key\Key::getPermissionObjectToCheck();
        }

        public static function getPermissionObject()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionObject();
            Concrete\Core\Permission\Key\Key::getPermissionObject();
        }

        public static function loadAll()
        {
            // Concrete\Core\Permission\Key\Key::loadAll();
            Concrete\Core\Permission\Key\Key::loadAll();
        }

        protected static function load($key, $loadBy = "pkID")
        {
            // Concrete\Core\Permission\Key\Key::load();
            Concrete\Core\Permission\Key\Key::load($key, $loadBy);
        }

        public static function hasCustomOptionsForm()
        {
            // Concrete\Core\Permission\Key\Key::hasCustomOptionsForm();
            Concrete\Core\Permission\Key\Key::hasCustomOptionsForm();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Permission\Key\Key::getPackageID();
            Concrete\Core\Permission\Key\Key::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Permission\Key\Key::getPackageHandle();
            Concrete\Core\Permission\Key\Key::getPackageHandle();
        }

        /**
         * Returns a list of all permissions of this category
         */
        public static function getList($pkCategoryHandle, $filters = null)
        {
            // Concrete\Core\Permission\Key\Key::getList();
            Concrete\Core\Permission\Key\Key::getList($pkCategoryHandle, $filters);
        }

        public static function export($axml)
        {
            // Concrete\Core\Permission\Key\Key::export();
            Concrete\Core\Permission\Key\Key::export($axml);
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Permission\Key\Key::exportList();
            Concrete\Core\Permission\Key\Key::exportList($xml);
        }

        /**
         * Note, this queries both the pkgID found on the PermissionKeys table AND any permission keys of a special type
         * installed by that package, and any in categories by that package.
         */
        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Permission\Key\Key::getListByPackage();
            Concrete\Core\Permission\Key\Key::getListByPackage($pkg);
        }

        public static function import(SimpleXMLElement $pk)
        {
            // Concrete\Core\Permission\Key\Key::import();
            Concrete\Core\Permission\Key\Key::import($pk);
        }

        public static function getByID($pkID)
        {
            // Concrete\Core\Permission\Key\Key::getByID();
            Concrete\Core\Permission\Key\Key::getByID($pkID);
        }

        public static function getByHandle($pkHandle)
        {
            // Concrete\Core\Permission\Key\Key::getByHandle();
            Concrete\Core\Permission\Key\Key::getByHandle($pkHandle);
        }

        /**
         * Adds an permission key.
         */
        public static function add($pkCategoryHandle, $pkHandle, $pkName, $pkDescription, $pkCanTriggerWorkflow, $pkHasCustomClass, $pkg = null)
        {
            // Concrete\Core\Permission\Key\Key::add();
            Concrete\Core\Permission\Key\Key::add($pkCategoryHandle, $pkHandle, $pkName, $pkDescription, $pkCanTriggerWorkflow, $pkHasCustomClass, $pkg);
        }

        /**
         * @access private
         * legacy support
         */
        public static function can()
        {
            // Concrete\Core\Permission\Key\Key::can();
            Concrete\Core\Permission\Key\Key::can();
        }

        public static function validate()
        {
            // Concrete\Core\Permission\Key\Key::validate();
            Concrete\Core\Permission\Key\Key::validate();
        }

        public static function delete()
        {
            // Concrete\Core\Permission\Key\Key::delete();
            Concrete\Core\Permission\Key\Key::delete();
        }

        /**
         * A shortcut for grabbing the current assignment and passing into that object
         */
        public static function getAccessListItems()
        {
            // Concrete\Core\Permission\Key\Key::getAccessListItems();
            Concrete\Core\Permission\Key\Key::getAccessListItems();
        }

        public static function getPermissionAssignmentObject()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionAssignmentObject();
            Concrete\Core\Permission\Key\Key::getPermissionAssignmentObject();
        }

        public static function getPermissionAccessObject()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionAccessObject();
            Concrete\Core\Permission\Key\Key::getPermissionAccessObject();
        }

        public static function getPermissionAccessID()
        {
            // Concrete\Core\Permission\Key\Key::getPermissionAccessID();
            Concrete\Core\Permission\Key\Key::getPermissionAccessID();
        }

        public static function exportAccess($pxml)
        {
            // Concrete\Core\Permission\Key\Key::exportAccess();
            Concrete\Core\Permission\Key\Key::exportAccess($pxml);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class PermissionKeyCategory extends \Concrete\Core\Permission\Category
    {

        public static function getByID($pkCategoryID)
        {
            // Concrete\Core\Permission\Category::getByID();
            Concrete\Core\Permission\Category::getByID($pkCategoryID);
        }

        protected static function populateCategories()
        {
            // Concrete\Core\Permission\Category::populateCategories();
            Concrete\Core\Permission\Category::populateCategories();
        }

        public static function getByHandle($pkCategoryHandle)
        {
            // Concrete\Core\Permission\Category::getByHandle();
            Concrete\Core\Permission\Category::getByHandle($pkCategoryHandle);
        }

        public static function handleExists($pkHandle)
        {
            // Concrete\Core\Permission\Category::handleExists();
            Concrete\Core\Permission\Category::handleExists($pkHandle);
        }

        public static function exportList($xml)
        {
            // Concrete\Core\Permission\Category::exportList();
            Concrete\Core\Permission\Category::exportList($xml);
        }

        public static function getListByPackage($pkg)
        {
            // Concrete\Core\Permission\Category::getListByPackage();
            Concrete\Core\Permission\Category::getListByPackage($pkg);
        }

        public static function getPermissionKeyByHandle($pkHandle)
        {
            // Concrete\Core\Permission\Category::getPermissionKeyByHandle();
            Concrete\Core\Permission\Category::getPermissionKeyByHandle($pkHandle);
        }

        public static function getPermissionKeyByID($pkID)
        {
            // Concrete\Core\Permission\Category::getPermissionKeyByID();
            Concrete\Core\Permission\Category::getPermissionKeyByID($pkID);
        }

        public static function getToolsURL($task = null)
        {
            // Concrete\Core\Permission\Category::getToolsURL();
            Concrete\Core\Permission\Category::getToolsURL($task);
        }

        public static function getPermissionKeyCategoryID()
        {
            // Concrete\Core\Permission\Category::getPermissionKeyCategoryID();
            Concrete\Core\Permission\Category::getPermissionKeyCategoryID();
        }

        public static function getPermissionKeyCategoryHandle()
        {
            // Concrete\Core\Permission\Category::getPermissionKeyCategoryHandle();
            Concrete\Core\Permission\Category::getPermissionKeyCategoryHandle();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Permission\Category::getPackageID();
            Concrete\Core\Permission\Category::getPackageID();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Permission\Category::getPackageHandle();
            Concrete\Core\Permission\Category::getPackageHandle();
        }

        public static function delete()
        {
            // Concrete\Core\Permission\Category::delete();
            Concrete\Core\Permission\Category::delete();
        }

        public static function associateAccessEntityType(Concrete\Core\Permission\Access\Entity\Type $pt)
        {
            // Concrete\Core\Permission\Category::associateAccessEntityType();
            Concrete\Core\Permission\Category::associateAccessEntityType($pt);
        }

        public static function clearAccessEntityTypeCategories()
        {
            // Concrete\Core\Permission\Category::clearAccessEntityTypeCategories();
            Concrete\Core\Permission\Category::clearAccessEntityTypeCategories();
        }

        public static function getList()
        {
            // Concrete\Core\Permission\Category::getList();
            Concrete\Core\Permission\Category::getList();
        }

        public static function add($pkCategoryHandle, $pkg = null)
        {
            // Concrete\Core\Permission\Category::add();
            Concrete\Core\Permission\Category::add($pkCategoryHandle, $pkg);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class PermissionAccess extends \Concrete\Core\Permission\Access\Access
    {

        public static function setPermissionKey($permissionKey)
        {
            // Concrete\Core\Permission\Access\Access::setPermissionKey();
            Concrete\Core\Permission\Access\Access::setPermissionKey($permissionKey);
        }

        public static function getPermissionObject()
        {
            // Concrete\Core\Permission\Access\Access::getPermissionObject();
            Concrete\Core\Permission\Access\Access::getPermissionObject();
        }

        public static function getPermissionObjectToCheck()
        {
            // Concrete\Core\Permission\Access\Access::getPermissionObjectToCheck();
            Concrete\Core\Permission\Access\Access::getPermissionObjectToCheck();
        }

        public static function getPermissionAccessID()
        {
            // Concrete\Core\Permission\Access\Access::getPermissionAccessID();
            Concrete\Core\Permission\Access\Access::getPermissionAccessID();
        }

        public static function isPermissionAccessInUse()
        {
            // Concrete\Core\Permission\Access\Access::isPermissionAccessInUse();
            Concrete\Core\Permission\Access\Access::isPermissionAccessInUse();
        }

        protected static function deliverAccessListItems($q, $accessType, $filterEntities)
        {
            // Concrete\Core\Permission\Access\Access::deliverAccessListItems();
            Concrete\Core\Permission\Access\Access::deliverAccessListItems($q, $accessType, $filterEntities);
        }

        public static function validateAndFilterAccessEntities($accessEntities)
        {
            // Concrete\Core\Permission\Access\Access::validateAndFilterAccessEntities();
            Concrete\Core\Permission\Access\Access::validateAndFilterAccessEntities($accessEntities);
        }

        public static function validateAccessEntities($accessEntities)
        {
            // Concrete\Core\Permission\Access\Access::validateAccessEntities();
            Concrete\Core\Permission\Access\Access::validateAccessEntities($accessEntities);
        }

        public static function validate()
        {
            // Concrete\Core\Permission\Access\Access::validate();
            Concrete\Core\Permission\Access\Access::validate();
        }

        public static function createByMerge($permissions)
        {
            // Concrete\Core\Permission\Access\Access::createByMerge();
            Concrete\Core\Permission\Access\Access::createByMerge($permissions);
        }

        public static function getAccessListItems($accessType = 10, $filterEntities = null)
        {
            // Concrete\Core\Permission\Access\Access::getAccessListItems();
            Concrete\Core\Permission\Access\Access::getAccessListItems($accessType, $filterEntities);
        }

        protected static function buildAssignmentFilterString($accessType, $filterEntities)
        {
            // Concrete\Core\Permission\Access\Access::buildAssignmentFilterString();
            Concrete\Core\Permission\Access\Access::buildAssignmentFilterString($accessType, $filterEntities);
        }

        public static function clearWorkflows()
        {
            // Concrete\Core\Permission\Access\Access::clearWorkflows();
            Concrete\Core\Permission\Access\Access::clearWorkflows();
        }

        public static function attachWorkflow(Concrete\Core\Workflow\Workflow $wf)
        {
            // Concrete\Core\Permission\Access\Access::attachWorkflow();
            Concrete\Core\Permission\Access\Access::attachWorkflow($wf);
        }

        public static function getWorkflows()
        {
            // Concrete\Core\Permission\Access\Access::getWorkflows();
            Concrete\Core\Permission\Access\Access::getWorkflows();
        }

        public static function duplicate($newPA = null)
        {
            // Concrete\Core\Permission\Access\Access::duplicate();
            Concrete\Core\Permission\Access\Access::duplicate($newPA);
        }

        public static function markAsInUse()
        {
            // Concrete\Core\Permission\Access\Access::markAsInUse();
            Concrete\Core\Permission\Access\Access::markAsInUse();
        }

        public static function addListItem(Concrete\Core\Permission\Access\Entity\Entity $pae, $durationObject = null, $accessType = 10)
        {
            // Concrete\Core\Permission\Access\Access::addListItem();
            Concrete\Core\Permission\Access\Access::addListItem($pae, $durationObject, $accessType);
        }

        public static function removeListItem(Concrete\Core\Permission\Access\Entity\Entity $pe)
        {
            // Concrete\Core\Permission\Access\Access::removeListItem();
            Concrete\Core\Permission\Access\Access::removeListItem($pe);
        }

        public static function save()
        {
            // Concrete\Core\Permission\Access\Access::save();
            Concrete\Core\Permission\Access\Access::save();
        }

        public static function create(Concrete\Core\Permission\Key\Key $pk)
        {
            // Concrete\Core\Permission\Access\Access::create();
            Concrete\Core\Permission\Access\Access::create($pk);
        }

        public static function getByID($paID, Concrete\Core\Permission\Key\Key $pk, $checkPA = "1")
        {
            // Concrete\Core\Permission\Access\Access::getByID();
            Concrete\Core\Permission\Access\Access::getByID($paID, $pk, $checkPA);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class User extends \Concrete\Core\User\User
    {

        /** Return an User instance given its id (or null if it's not found)
         * @param int $uID The id of the user
         * @param boolean $login = false Set to true to make the user the current one
         * @param boolean $cacheItemsOnLogin = false Set to true to cache some items when $login is true
         * @return User|null
         */
        public static function getByUserID($uID, $login = null, $cacheItemsOnLogin = "1")
        {
            // Concrete\Core\User\User::getByUserID();
            Concrete\Core\User\User::getByUserID($uID, $login, $cacheItemsOnLogin);
        }

        /**
         * @param int $uID
         * @return User
         */
        public static function loginByUserID($uID)
        {
            // Concrete\Core\User\User::loginByUserID();
            Concrete\Core\User\User::loginByUserID($uID);
        }

        public static function isLoggedIn()
        {
            // Concrete\Core\User\User::isLoggedIn();
            Concrete\Core\User\User::isLoggedIn();
        }

        public static function checkLogin()
        {
            // Concrete\Core\User\User::checkLogin();
            Concrete\Core\User\User::checkLogin();
        }

        public static function __construct()
        {
            // Concrete\Core\User\User::__construct();
            Concrete\Core\User\User::__construct();
        }

        public static function recordLogin()
        {
            // Concrete\Core\User\User::recordLogin();
            Concrete\Core\User\User::recordLogin();
        }

        public static function recordView($c)
        {
            // Concrete\Core\User\User::recordView();
            Concrete\Core\User\User::recordView($c);
        }

        public static function encryptPassword($uPassword, $salt = null)
        {
            // Concrete\Core\User\User::encryptPassword();
            Concrete\Core\User\User::encryptPassword($uPassword, $salt);
        }

        public static function legacyEncryptPassword($uPassword)
        {
            // Concrete\Core\User\User::legacyEncryptPassword();
            Concrete\Core\User\User::legacyEncryptPassword($uPassword);
        }

        public static function isActive()
        {
            // Concrete\Core\User\User::isActive();
            Concrete\Core\User\User::isActive();
        }

        public static function isSuperUser()
        {
            // Concrete\Core\User\User::isSuperUser();
            Concrete\Core\User\User::isSuperUser();
        }

        public static function getLastOnline()
        {
            // Concrete\Core\User\User::getLastOnline();
            Concrete\Core\User\User::getLastOnline();
        }

        public static function getUserName()
        {
            // Concrete\Core\User\User::getUserName();
            Concrete\Core\User\User::getUserName();
        }

        public static function isRegistered()
        {
            // Concrete\Core\User\User::isRegistered();
            Concrete\Core\User\User::isRegistered();
        }

        public static function getUserID()
        {
            // Concrete\Core\User\User::getUserID();
            Concrete\Core\User\User::getUserID();
        }

        public static function getUserTimezone()
        {
            // Concrete\Core\User\User::getUserTimezone();
            Concrete\Core\User\User::getUserTimezone();
        }

        public static function setAuthTypeCookie($authType)
        {
            // Concrete\Core\User\User::setAuthTypeCookie();
            Concrete\Core\User\User::setAuthTypeCookie($authType);
        }

        public static function setLastAuthType(Concrete\Core\Authentication\AuthenticationType $at)
        {
            // Concrete\Core\User\User::setLastAuthType();
            Concrete\Core\User\User::setLastAuthType($at);
        }

        public static function getLastAuthType()
        {
            // Concrete\Core\User\User::getLastAuthType();
            Concrete\Core\User\User::getLastAuthType();
        }

        public static function unloadAuthenticationTypes()
        {
            // Concrete\Core\User\User::unloadAuthenticationTypes();
            Concrete\Core\User\User::unloadAuthenticationTypes();
        }

        public static function logout($hard = "1")
        {
            // Concrete\Core\User\User::logout();
            Concrete\Core\User\User::logout($hard);
        }

        public static function checkUserForeverCookie()
        {
            // Concrete\Core\User\User::checkUserForeverCookie();
            Concrete\Core\User\User::checkUserForeverCookie();
        }

        public static function verifyAuthTypeCookie()
        {
            // Concrete\Core\User\User::verifyAuthTypeCookie();
            Concrete\Core\User\User::verifyAuthTypeCookie();
        }

        /**
         * authenticatiion types will handle this
         * @depricated since before 5.6.3
         */
        public static function setUserForeverCookie()
        {
            // Concrete\Core\User\User::setUserForeverCookie();
            Concrete\Core\User\User::setUserForeverCookie();
        }

        public static function getUserGroupObjects()
        {
            // Concrete\Core\User\User::getUserGroupObjects();
            Concrete\Core\User\User::getUserGroupObjects();
        }

        public static function getUserGroups()
        {
            // Concrete\Core\User\User::getUserGroups();
            Concrete\Core\User\User::getUserGroups();
        }

        /**
         * Sets a default language for a user record
         */
        public static function setUserDefaultLanguage($lang)
        {
            // Concrete\Core\User\User::setUserDefaultLanguage();
            Concrete\Core\User\User::setUserDefaultLanguage($lang);
        }

        /**
         * Gets the default language for the logged-in user
         */
        public static function getUserDefaultLanguage()
        {
            // Concrete\Core\User\User::getUserDefaultLanguage();
            Concrete\Core\User\User::getUserDefaultLanguage();
        }

        /**
         * Checks to see if the current user object is registered. If so, it queries that records
         * default language. Otherwise, it falls back to sitewide settings.
         */
        public static function getUserLanguageToDisplay()
        {
            // Concrete\Core\User\User::getUserLanguageToDisplay();
            Concrete\Core\User\User::getUserLanguageToDisplay();
        }

        public static function refreshUserGroups()
        {
            // Concrete\Core\User\User::refreshUserGroups();
            Concrete\Core\User\User::refreshUserGroups();
        }

        public static function getUserAccessEntityObjects()
        {
            // Concrete\Core\User\User::getUserAccessEntityObjects();
            Concrete\Core\User\User::getUserAccessEntityObjects();
        }

        public static function _getUserGroups($disableLogin = null)
        {
            // Concrete\Core\User\User::_getUserGroups();
            Concrete\Core\User\User::_getUserGroups($disableLogin);
        }

        public static function enterGroup($g)
        {
            // Concrete\Core\User\User::enterGroup();
            Concrete\Core\User\User::enterGroup($g);
        }

        public static function exitGroup($g)
        {
            // Concrete\Core\User\User::exitGroup();
            Concrete\Core\User\User::exitGroup($g);
        }

        public static function inGroup($g)
        {
            // Concrete\Core\User\User::inGroup();
            Concrete\Core\User\User::inGroup($g);
        }

        public static function loadMasterCollectionEdit($mcID, $ocID)
        {
            // Concrete\Core\User\User::loadMasterCollectionEdit();
            Concrete\Core\User\User::loadMasterCollectionEdit($mcID, $ocID);
        }

        public static function loadCollectionEdit(&$c)
        {
            // Concrete\Core\User\User::loadCollectionEdit();
            Concrete\Core\User\User::loadCollectionEdit($c);
        }

        public static function unloadCollectionEdit($removeCache = "1")
        {
            // Concrete\Core\User\User::unloadCollectionEdit();
            Concrete\Core\User\User::unloadCollectionEdit($removeCache);
        }

        public static function config($cfKey)
        {
            // Concrete\Core\User\User::config();
            Concrete\Core\User\User::config($cfKey);
        }

        public static function markPreviousFrontendPage(Concrete\Core\Page\Page $c)
        {
            // Concrete\Core\User\User::markPreviousFrontendPage();
            Concrete\Core\User\User::markPreviousFrontendPage($c);
        }

        public static function getPreviousFrontendPageID()
        {
            // Concrete\Core\User\User::getPreviousFrontendPageID();
            Concrete\Core\User\User::getPreviousFrontendPageID();
        }

        public static function saveConfig($cfKey, $cfValue)
        {
            // Concrete\Core\User\User::saveConfig();
            Concrete\Core\User\User::saveConfig($cfKey, $cfValue);
        }

        public static function refreshCollectionEdit(&$c)
        {
            // Concrete\Core\User\User::refreshCollectionEdit();
            Concrete\Core\User\User::refreshCollectionEdit($c);
        }

        public static function forceCollectionCheckInAll()
        {
            // Concrete\Core\User\User::forceCollectionCheckInAll();
            Concrete\Core\User\User::forceCollectionCheckInAll();
        }

        /**
         * @see PasswordHash
         *
         * @return PasswordHash
         */
        public static function getUserPasswordHasher()
        {
            // Concrete\Core\User\User::getUserPasswordHasher();
            Concrete\Core\User\User::getUserPasswordHasher();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class UserInfo extends \Concrete\Core\User\UserInfo
    {

        public static function __toString()
        {
            // Concrete\Core\User\UserInfo::__toString();
            Concrete\Core\User\UserInfo::__toString();
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\User\UserInfo::getPermissionObjectIdentifier();
            Concrete\Core\User\UserInfo::getPermissionObjectIdentifier();
        }

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\User\UserInfo::getPermissionResponseClassName();
            Concrete\Core\User\UserInfo::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\User\UserInfo::getPermissionAssignmentClassName();
            Concrete\Core\User\UserInfo::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\User\UserInfo::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\User\UserInfo::getPermissionObjectKeyCategoryHandle();
        }

        public static function __call($nm, $a)
        {
            // Concrete\Core\User\UserInfo::__call();
            Concrete\Core\User\UserInfo::__call($nm, $a);
        }

        /**
         * returns the UserInfo object for a give user's uID
         * @param int $uID
         * @return UserInfo
         */
        public static function getByID($uID)
        {
            // Concrete\Core\User\UserInfo::getByID();
            Concrete\Core\User\UserInfo::getByID($uID);
        }

        /**
         * returns the UserInfo object for a give user's username
         * @param string $uName
         * @return UserInfo
         */
        public static function getByUserName($uName)
        {
            // Concrete\Core\User\UserInfo::getByUserName();
            Concrete\Core\User\UserInfo::getByUserName($uName);
        }

        /**
         * returns the UserInfo object for a give user's email address
         * @param string $uEmail
         * @return UserInfo
         */
        public static function getByEmail($uEmail)
        {
            // Concrete\Core\User\UserInfo::getByEmail();
            Concrete\Core\User\UserInfo::getByEmail($uEmail);
        }

        /**
         * Returns a user object by open ID. Does not log a user in.
         * @param string $uOpenID
         * @return UserInfo
         */
        public static function getByOpenID($uOpenID)
        {
            // Concrete\Core\User\UserInfo::getByOpenID();
            Concrete\Core\User\UserInfo::getByOpenID($uOpenID);
        }

        /**
         * @param string $uHash
         * @param boolean $unredeemedHashesOnly
         * @return UserInfo
         */
        public static function getByValidationHash($uHash, $unredeemedHashesOnly = "1")
        {
            // Concrete\Core\User\UserInfo::getByValidationHash();
            Concrete\Core\User\UserInfo::getByValidationHash($uHash, $unredeemedHashesOnly);
        }

        public static function getUserBadges()
        {
            // Concrete\Core\User\UserInfo::getUserBadges();
            Concrete\Core\User\UserInfo::getUserBadges();
        }

        /**
         * @param array $data
         * @param array | false $options
         * @return UserInfo
         */
        public static function add($data, $options = null)
        {
            // Concrete\Core\User\UserInfo::add();
            Concrete\Core\User\UserInfo::add($data, $options);
        }

        public static function addSuperUser($uPasswordEncrypted, $uEmail)
        {
            // Concrete\Core\User\UserInfo::addSuperUser();
            Concrete\Core\User\UserInfo::addSuperUser($uPasswordEncrypted, $uEmail);
        }

        /**
         * Deletes a user
         * @return void
         */
        public static function delete()
        {
            // Concrete\Core\User\UserInfo::delete();
            Concrete\Core\User\UserInfo::delete();
        }

        /**
         * Called only by the getGroupMembers function it sets the "type" of member for this group. Typically only used programmatically
         * @param string $type
         * @return void
         */
        public static function setGroupMemberType($type)
        {
            // Concrete\Core\User\UserInfo::setGroupMemberType();
            Concrete\Core\User\UserInfo::setGroupMemberType($type);
        }

        public static function getGroupMemberType()
        {
            // Concrete\Core\User\UserInfo::getGroupMemberType();
            Concrete\Core\User\UserInfo::getGroupMemberType();
        }

        public static function canReadPrivateMessage($msg)
        {
            // Concrete\Core\User\UserInfo::canReadPrivateMessage();
            Concrete\Core\User\UserInfo::canReadPrivateMessage($msg);
        }

        public static function sendPrivateMessage($recipient, $subject, $text, $inReplyTo = null)
        {
            // Concrete\Core\User\UserInfo::sendPrivateMessage();
            Concrete\Core\User\UserInfo::sendPrivateMessage($recipient, $subject, $text, $inReplyTo);
        }

        /**
         * gets the user object of the current UserInfo object ($this)
         * @return User
         */
        public static function getUserObject()
        {
            // Concrete\Core\User\UserInfo::getUserObject();
            Concrete\Core\User\UserInfo::getUserObject();
        }

        /**
         * Sets the attribute of a user info object to the specified value, and saves it in the database
         */
        public static function setAttribute($ak, $value)
        {
            // Concrete\Core\User\UserInfo::setAttribute();
            Concrete\Core\User\UserInfo::setAttribute($ak, $value);
        }

        public static function clearAttribute($ak)
        {
            // Concrete\Core\User\UserInfo::clearAttribute();
            Concrete\Core\User\UserInfo::clearAttribute($ak);
        }

        public static function reindex()
        {
            // Concrete\Core\User\UserInfo::reindex();
            Concrete\Core\User\UserInfo::reindex();
        }

        /**
         * Gets the value of the attribute for the user
         */
        public static function getAttribute($ak, $displayMode = null)
        {
            // Concrete\Core\User\UserInfo::getAttribute();
            Concrete\Core\User\UserInfo::getAttribute($ak, $displayMode);
        }

        public static function getAttributeField($ak)
        {
            // Concrete\Core\User\UserInfo::getAttributeField();
            Concrete\Core\User\UserInfo::getAttributeField($ak);
        }

        public static function getAttributeValueObject($ak, $createIfNotFound = null)
        {
            // Concrete\Core\User\UserInfo::getAttributeValueObject();
            Concrete\Core\User\UserInfo::getAttributeValueObject($ak, $createIfNotFound);
        }

        public static function update($data)
        {
            // Concrete\Core\User\UserInfo::update();
            Concrete\Core\User\UserInfo::update($data);
        }

        public static function updateGroups($groupArray)
        {
            // Concrete\Core\User\UserInfo::updateGroups();
            Concrete\Core\User\UserInfo::updateGroups($groupArray);
        }

        /**
         * @param array $data
         * @return UserInfo
         */
        public static function register($data)
        {
            // Concrete\Core\User\UserInfo::register();
            Concrete\Core\User\UserInfo::register($data);
        }

        public static function setupValidation()
        {
            // Concrete\Core\User\UserInfo::setupValidation();
            Concrete\Core\User\UserInfo::setupValidation();
        }

        public static function markValidated()
        {
            // Concrete\Core\User\UserInfo::markValidated();
            Concrete\Core\User\UserInfo::markValidated();
        }

        public static function changePassword($newPassword)
        {
            // Concrete\Core\User\UserInfo::changePassword();
            Concrete\Core\User\UserInfo::changePassword($newPassword);
        }

        public static function activate()
        {
            // Concrete\Core\User\UserInfo::activate();
            Concrete\Core\User\UserInfo::activate();
        }

        public static function deactivate()
        {
            // Concrete\Core\User\UserInfo::deactivate();
            Concrete\Core\User\UserInfo::deactivate();
        }

        public static function resetUserPassword()
        {
            // Concrete\Core\User\UserInfo::resetUserPassword();
            Concrete\Core\User\UserInfo::resetUserPassword();
        }

        public static function hasAvatar()
        {
            // Concrete\Core\User\UserInfo::hasAvatar();
            Concrete\Core\User\UserInfo::hasAvatar();
        }

        public static function getLastLogin()
        {
            // Concrete\Core\User\UserInfo::getLastLogin();
            Concrete\Core\User\UserInfo::getLastLogin();
        }

        public static function getLastIPAddress()
        {
            // Concrete\Core\User\UserInfo::getLastIPAddress();
            Concrete\Core\User\UserInfo::getLastIPAddress();
        }

        public static function getPreviousLogin()
        {
            // Concrete\Core\User\UserInfo::getPreviousLogin();
            Concrete\Core\User\UserInfo::getPreviousLogin();
        }

        public static function isActive()
        {
            // Concrete\Core\User\UserInfo::isActive();
            Concrete\Core\User\UserInfo::isActive();
        }

        public static function isValidated()
        {
            // Concrete\Core\User\UserInfo::isValidated();
            Concrete\Core\User\UserInfo::isValidated();
        }

        public static function isFullRecord()
        {
            // Concrete\Core\User\UserInfo::isFullRecord();
            Concrete\Core\User\UserInfo::isFullRecord();
        }

        public static function getNumLogins()
        {
            // Concrete\Core\User\UserInfo::getNumLogins();
            Concrete\Core\User\UserInfo::getNumLogins();
        }

        public static function getUserID()
        {
            // Concrete\Core\User\UserInfo::getUserID();
            Concrete\Core\User\UserInfo::getUserID();
        }

        public static function getUserName()
        {
            // Concrete\Core\User\UserInfo::getUserName();
            Concrete\Core\User\UserInfo::getUserName();
        }

        public static function getUserDisplayName()
        {
            // Concrete\Core\User\UserInfo::getUserDisplayName();
            Concrete\Core\User\UserInfo::getUserDisplayName();
        }

        public static function getUserPassword()
        {
            // Concrete\Core\User\UserInfo::getUserPassword();
            Concrete\Core\User\UserInfo::getUserPassword();
        }

        public static function getUserEmail()
        {
            // Concrete\Core\User\UserInfo::getUserEmail();
            Concrete\Core\User\UserInfo::getUserEmail();
        }

        public static function getUserTimezone()
        {
            // Concrete\Core\User\UserInfo::getUserTimezone();
            Concrete\Core\User\UserInfo::getUserTimezone();
        }

        public static function getUserDefaultLanguage()
        {
            // Concrete\Core\User\UserInfo::getUserDefaultLanguage();
            Concrete\Core\User\UserInfo::getUserDefaultLanguage();
        }

        /**
         * Gets the date a user was added to the system,
         * if user is specified, returns in the current user's timezone
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getUserDateAdded($type = "system", $datemask = "Y-m-d H:i:s")
        {
            // Concrete\Core\User\UserInfo::getUserDateAdded();
            Concrete\Core\User\UserInfo::getUserDateAdded($type, $datemask);
        }

        public static function getUserStartDate($type = "system")
        {
            // Concrete\Core\User\UserInfo::getUserStartDate();
            Concrete\Core\User\UserInfo::getUserStartDate($type);
        }

        /**
         * Gets the date a user was last active on the site
         * if user is specified, returns in the current user's timezone
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getLastOnline($type = "system")
        {
            // Concrete\Core\User\UserInfo::getLastOnline();
            Concrete\Core\User\UserInfo::getLastOnline($type);
        }

        public static function getUserEndDate($type = "system")
        {
            // Concrete\Core\User\UserInfo::getUserEndDate();
            Concrete\Core\User\UserInfo::getUserEndDate($type);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    /**
     * An object that allows a filtered list of users to be returned.
     * @package Files
     * @author Tony Trupp <tony@concrete5.org>
     * @category Concrete
     * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
     * @license    http://www.concrete5.org/license/     MIT License
     *
     */
    class UserList extends \Concrete\Core\User\UserList
    {

        public static function filterByUserName($username)
        {
            // Concrete\Core\User\UserList::filterByUserName();
            Concrete\Core\User\UserList::filterByUserName($username);
        }

        public static function filterByKeywords($keywords)
        {
            // Concrete\Core\User\UserList::filterByKeywords();
            Concrete\Core\User\UserList::filterByKeywords($keywords);
        }

        /**
         * filters the user list for only users within the provided group.  Accepts an instance of a group object or a string group name
         * @param Group|string $group
         * @param boolean $inGroup
         * @return void
         */
        public static function filterByGroup($group = null, $inGroup = "1")
        {
            // Concrete\Core\User\UserList::filterByGroup();
            Concrete\Core\User\UserList::filterByGroup($group, $inGroup);
        }

        public static function excludeUsers($uo)
        {
            // Concrete\Core\User\UserList::excludeUsers();
            Concrete\Core\User\UserList::excludeUsers($uo);
        }

        public static function filterByGroupID($gID)
        {
            // Concrete\Core\User\UserList::filterByGroupID();
            Concrete\Core\User\UserList::filterByGroupID($gID);
        }

        public static function filterByDateAdded($date, $comparison = "=")
        {
            // Concrete\Core\User\UserList::filterByDateAdded();
            Concrete\Core\User\UserList::filterByDateAdded($date, $comparison);
        }

        /**
         * Returns an array of userInfo objects based on current filter settings
         * @return UserInfo[]
         */
        public static function get($itemsToGet = 100, $offset = null)
        {
            // Concrete\Core\User\UserList::get();
            Concrete\Core\User\UserList::get($itemsToGet, $offset);
        }

        /**
         * similar to get except it returns an array of userIDs
         * much faster than getting a UserInfo object for each result if all you need is the user's id
         * @return array $userIDs
         */
        public static function getUserIDs($itemsToGet = 100, $offset = null)
        {
            // Concrete\Core\User\UserList::getUserIDs();
            Concrete\Core\User\UserList::getUserIDs($itemsToGet, $offset);
        }

        public static function getTotal()
        {
            // Concrete\Core\User\UserList::getTotal();
            Concrete\Core\User\UserList::getTotal();
        }

        public static function filterByIsActive($val)
        {
            // Concrete\Core\User\UserList::filterByIsActive();
            Concrete\Core\User\UserList::filterByIsActive($val);
        }

        protected static function createQuery()
        {
            // Concrete\Core\User\UserList::createQuery();
            Concrete\Core\User\UserList::createQuery();
        }

        protected static function setBaseQuery()
        {
            // Concrete\Core\User\UserList::setBaseQuery();
            Concrete\Core\User\UserList::setBaseQuery();
        }

        public static function __call($nm, $a)
        {
            // Concrete\Core\User\UserList::__call();
            Concrete\Core\User\UserList::__call($nm, $a);
        }

        public static function debug($dbg = "1")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::debug($dbg);
        }

        protected static function setQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setQuery($query);
        }

        protected static function getQuery()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getQuery();
        }

        public static function addToQuery($query)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::addToQuery($query);
        }

        protected static function setupAutoSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAutoSort();
        }

        protected static function executeBase()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::executeBase();
        }

        protected static function setupSortByString()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupSortByString();
        }

        protected static function setupAttributeSort()
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeSort();
        }

        /**
         * Adds a filter to this item list
         */
        public static function filter($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filter($column, $value, $comparison);
        }

        public static function getSearchResultsClass($field)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSearchResultsClass($field);
        }

        public static function sortBy($key, $dir = "asc")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::sortBy($key, $dir);
        }

        public static function groupBy($key)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::groupBy($key);
        }

        public static function having($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::having($column, $value, $comparison);
        }

        public static function getSortByURL($column, $dir = "asc", $baseURL = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::getSortByURL($column, $dir, $baseURL, $additionalVars);
        }

        protected static function setupAttributeFilters($join)
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::setupAttributeFilters($join);
        }

        public static function filterByAttribute($column, $value, $comparison = "=")
        {
            // Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute();
            Concrete\Core\Foundation\Collection\Database\DatabaseItemList::filterByAttribute($column, $value, $comparison);
        }

        public static function enableStickySearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::enableStickySearchRequest($namespace);
        }

        public static function getQueryStringPagingVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringPagingVariable();
        }

        public static function getQueryStringSortVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortVariable();
        }

        public static function getQueryStringSortDirectionVariable()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
            Concrete\Core\Foundation\Collection\ItemList::getQueryStringSortDirectionVariable();
        }

        protected static function getStickySearchNameSpace()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::getStickySearchNameSpace();
        }

        public static function resetSearchRequest($namespace = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::resetSearchRequest($namespace);
        }

        public static function addToSearchRequest($key, $value)
        {
            // Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::addToSearchRequest($key, $value);
        }

        public static function getSearchRequest()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
            Concrete\Core\Foundation\Collection\ItemList::getSearchRequest();
        }

        public static function setItemsPerPage($num)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::setItemsPerPage($num);
        }

        public static function getItemsPerPage()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
            Concrete\Core\Foundation\Collection\ItemList::getItemsPerPage();
        }

        public static function setItems($items)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setItems();
            Concrete\Core\Foundation\Collection\ItemList::setItems($items);
        }

        public static function setNameSpace($ns)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setNameSpace();
            Concrete\Core\Foundation\Collection\ItemList::setNameSpace($ns);
        }

        /**
         * Returns an array of object by "page"
         */
        public static function getPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPage();
            Concrete\Core\Foundation\Collection\ItemList::getPage($page);
        }

        protected static function setCurrentPage($page = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::setCurrentPage();
            Concrete\Core\Foundation\Collection\ItemList::setCurrentPage($page);
        }

        /**
         * Displays summary text about a list
         */
        public static function displaySummary($right_content = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displaySummary();
            Concrete\Core\Foundation\Collection\ItemList::displaySummary($right_content);
        }

        public static function isActiveSortColumn($column)
        {
            // Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::isActiveSortColumn($column);
        }

        public static function getActiveSortColumn()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortColumn();
        }

        public static function getActiveSortDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
            Concrete\Core\Foundation\Collection\ItemList::getActiveSortDirection();
        }

        public static function requiresPaging()
        {
            // Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
            Concrete\Core\Foundation\Collection\ItemList::requiresPaging();
        }

        public static function getPagination($url = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::getPagination();
            Concrete\Core\Foundation\Collection\ItemList::getPagination($url, $additionalVars);
        }

        /**
         * Gets paging that works in our new format */
        public static function displayPagingV2($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPagingV2();
            Concrete\Core\Foundation\Collection\ItemList::displayPagingV2($script, $return, $additionalVars);
        }

        /**
         * Gets standard HTML to display paging */
        public static function displayPaging($script = null, $return = null, $additionalVars = null)
        {
            // Concrete\Core\Foundation\Collection\ItemList::displayPaging();
            Concrete\Core\Foundation\Collection\ItemList::displayPaging($script, $return, $additionalVars);
        }

        /**
         * Returns an object with properties useful for paging
         */
        public static function getSummary()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSummary();
            Concrete\Core\Foundation\Collection\ItemList::getSummary();
        }

        public static function getSortBy()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortBy();
            Concrete\Core\Foundation\Collection\ItemList::getSortBy();
        }

        public static function getSortByDirection()
        {
            // Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
            Concrete\Core\Foundation\Collection\ItemList::getSortByDirection();
        }

        /**
         * Sets up a multiple columns to search by. Each argument is taken "as-is" (including asc or desc) and concatenated with commas
         * Note that this is overrides any previous sortByMultiple() call, and all sortBy() calls. Alternatively, you can pass a single
         * array with multiple columns to sort by as its values.
         * e.g. $list->sortByMultiple('columna desc', 'columnb asc');
         * or $list->sortByMultiple(array('columna desc', 'columnb asc'));
         */
        public static function sortByMultiple()
        {
            // Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
            Concrete\Core\Foundation\Collection\ItemList::sortByMultiple();
        }

    }

    class StartingPointPackage extends \Concrete\Core\Package\StartingPointPackage
    {

        public static function getInstallRoutines()
        {
            // Concrete\Core\Package\StartingPointPackage::getInstallRoutines();
            Concrete\Core\Package\StartingPointPackage::getInstallRoutines();
        }

        public static function __construct()
        {
            // Concrete\Core\Package\StartingPointPackage::__construct();
            Concrete\Core\Package\StartingPointPackage::__construct();
        }

        public static function add_home_page()
        {
            // Concrete\Core\Package\StartingPointPackage::add_home_page();
            Concrete\Core\Package\StartingPointPackage::add_home_page();
        }

        public static function precache()
        {
            // Concrete\Core\Package\StartingPointPackage::precache();
            Concrete\Core\Package\StartingPointPackage::precache();
        }

        public static function install_attributes()
        {
            // Concrete\Core\Package\StartingPointPackage::install_attributes();
            Concrete\Core\Package\StartingPointPackage::install_attributes();
        }

        public static function install_dashboard()
        {
            // Concrete\Core\Package\StartingPointPackage::install_dashboard();
            Concrete\Core\Package\StartingPointPackage::install_dashboard();
        }

        public static function install_gathering()
        {
            // Concrete\Core\Package\StartingPointPackage::install_gathering();
            Concrete\Core\Package\StartingPointPackage::install_gathering();
        }

        public static function install_page_types()
        {
            // Concrete\Core\Package\StartingPointPackage::install_page_types();
            Concrete\Core\Package\StartingPointPackage::install_page_types();
        }

        public static function install_page_templates()
        {
            // Concrete\Core\Package\StartingPointPackage::install_page_templates();
            Concrete\Core\Package\StartingPointPackage::install_page_templates();
        }

        public static function install_required_single_pages()
        {
            // Concrete\Core\Package\StartingPointPackage::install_required_single_pages();
            Concrete\Core\Package\StartingPointPackage::install_required_single_pages();
        }

        public static function install_image_editor()
        {
            // Concrete\Core\Package\StartingPointPackage::install_image_editor();
            Concrete\Core\Package\StartingPointPackage::install_image_editor();
        }

        public static function install_blocktypes()
        {
            // Concrete\Core\Package\StartingPointPackage::install_blocktypes();
            Concrete\Core\Package\StartingPointPackage::install_blocktypes();
        }

        public static function install_themes()
        {
            // Concrete\Core\Package\StartingPointPackage::install_themes();
            Concrete\Core\Package\StartingPointPackage::install_themes();
        }

        public static function install_jobs()
        {
            // Concrete\Core\Package\StartingPointPackage::install_jobs();
            Concrete\Core\Package\StartingPointPackage::install_jobs();
        }

        public static function install_config()
        {
            // Concrete\Core\Package\StartingPointPackage::install_config();
            Concrete\Core\Package\StartingPointPackage::install_config();
        }

        public static function import_files()
        {
            // Concrete\Core\Package\StartingPointPackage::import_files();
            Concrete\Core\Package\StartingPointPackage::import_files();
        }

        public static function install_content()
        {
            // Concrete\Core\Package\StartingPointPackage::install_content();
            Concrete\Core\Package\StartingPointPackage::install_content();
        }

        public static function install_database()
        {
            // Concrete\Core\Package\StartingPointPackage::install_database();
            Concrete\Core\Package\StartingPointPackage::install_database();
        }

        protected static function indexAdditionalDatabaseFields()
        {
            // Concrete\Core\Package\StartingPointPackage::indexAdditionalDatabaseFields();
            Concrete\Core\Package\StartingPointPackage::indexAdditionalDatabaseFields();
        }

        public static function add_users()
        {
            // Concrete\Core\Package\StartingPointPackage::add_users();
            Concrete\Core\Package\StartingPointPackage::add_users();
        }

        public static function make_directories()
        {
            // Concrete\Core\Package\StartingPointPackage::make_directories();
            Concrete\Core\Package\StartingPointPackage::make_directories();
        }

        public static function finish()
        {
            // Concrete\Core\Package\StartingPointPackage::finish();
            Concrete\Core\Package\StartingPointPackage::finish();
        }

        public static function install_permissions()
        {
            // Concrete\Core\Package\StartingPointPackage::install_permissions();
            Concrete\Core\Package\StartingPointPackage::install_permissions();
        }

        public static function set_site_permissions()
        {
            // Concrete\Core\Package\StartingPointPackage::set_site_permissions();
            Concrete\Core\Package\StartingPointPackage::set_site_permissions();
        }

        public static function hasCustomList()
        {
            // Concrete\Core\Package\StartingPointPackage::hasCustomList();
            Concrete\Core\Package\StartingPointPackage::hasCustomList();
        }

        public static function getClass($pkgHandle)
        {
            // Concrete\Core\Package\StartingPointPackage::getClass();
            Concrete\Core\Package\StartingPointPackage::getClass($pkgHandle);
        }

        public static function getAvailableList()
        {
            // Concrete\Core\Package\StartingPointPackage::getAvailableList();
            Concrete\Core\Package\StartingPointPackage::getAvailableList();
        }

        public static function getRelativePath()
        {
            // Concrete\Core\Package\Package::getRelativePath();
            Concrete\Core\Package\Package::getRelativePath();
        }

        public static function getPackageID()
        {
            // Concrete\Core\Package\Package::getPackageID();
            Concrete\Core\Package\Package::getPackageID();
        }

        public static function getPackageName()
        {
            // Concrete\Core\Package\Package::getPackageName();
            Concrete\Core\Package\Package::getPackageName();
        }

        public static function getPackageDescription()
        {
            // Concrete\Core\Package\Package::getPackageDescription();
            Concrete\Core\Package\Package::getPackageDescription();
        }

        public static function getPackageHandle()
        {
            // Concrete\Core\Package\Package::getPackageHandle();
            Concrete\Core\Package\Package::getPackageHandle();
        }

        /**
         * Gets the date the package was added to the system,
         * if user is specified, returns in the current user's timezone
         * @param string $type (system || user)
         * @return string date formated like: 2009-01-01 00:00:00
         */
        public static function getPackageDateInstalled($type = "system")
        {
            // Concrete\Core\Package\Package::getPackageDateInstalled();
            Concrete\Core\Package\Package::getPackageDateInstalled($type);
        }

        public static function getPackageVersion()
        {
            // Concrete\Core\Package\Package::getPackageVersion();
            Concrete\Core\Package\Package::getPackageVersion();
        }

        public static function getPackageVersionUpdateAvailable()
        {
            // Concrete\Core\Package\Package::getPackageVersionUpdateAvailable();
            Concrete\Core\Package\Package::getPackageVersionUpdateAvailable();
        }

        public static function isPackageInstalled()
        {
            // Concrete\Core\Package\Package::isPackageInstalled();
            Concrete\Core\Package\Package::isPackageInstalled();
        }

        public static function getChangelogContents()
        {
            // Concrete\Core\Package\Package::getChangelogContents();
            Concrete\Core\Package\Package::getChangelogContents();
        }

        /**
         * Returns the currently installed package version.
         * NOTE: This function only returns a value if getLocalUpgradeablePackages() has been called first!
         */
        public static function getPackageCurrentlyInstalledVersion()
        {
            // Concrete\Core\Package\Package::getPackageCurrentlyInstalledVersion();
            Concrete\Core\Package\Package::getPackageCurrentlyInstalledVersion();
        }

        public static function getApplicationVersionRequired()
        {
            // Concrete\Core\Package\Package::getApplicationVersionRequired();
            Concrete\Core\Package\Package::getApplicationVersionRequired();
        }

        public static function hasInstallNotes()
        {
            // Concrete\Core\Package\Package::hasInstallNotes();
            Concrete\Core\Package\Package::hasInstallNotes();
        }

        public static function hasInstallPostScreen()
        {
            // Concrete\Core\Package\Package::hasInstallPostScreen();
            Concrete\Core\Package\Package::hasInstallPostScreen();
        }

        public static function allowsFullContentSwap()
        {
            // Concrete\Core\Package\Package::allowsFullContentSwap();
            Concrete\Core\Package\Package::allowsFullContentSwap();
        }

        public static function showInstallOptionsScreen()
        {
            // Concrete\Core\Package\Package::showInstallOptionsScreen();
            Concrete\Core\Package\Package::showInstallOptionsScreen();
        }

        public static function installDB($xmlFile)
        {
            // Concrete\Core\Package\Package::installDB();
            Concrete\Core\Package\Package::installDB($xmlFile);
        }

        /**
         * Loads package translation files into zend translate
         * @param string $locale
         * @param string $key
         * @return void
         */
        public static function setupPackageLocalization($locale = null, $key = null)
        {
            // Concrete\Core\Package\Package::setupPackageLocalization();
            Concrete\Core\Package\Package::setupPackageLocalization($locale, $key);
        }

        /**
         * Returns an array of package items (e.g. blocks, themes)
         */
        public static function getPackageItems()
        {
            // Concrete\Core\Package\Package::getPackageItems();
            Concrete\Core\Package\Package::getPackageItems();
        }

        /** Returns the display name of a category of package items (localized and escaped accordingly to $format)
         * @param string $categoryHandle The category handle
         * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
         * @return string
         */
        public static function getPackageItemsCategoryDisplayName($categoryHandle, $format = "html")
        {
            // Concrete\Core\Package\Package::getPackageItemsCategoryDisplayName();
            Concrete\Core\Package\Package::getPackageItemsCategoryDisplayName($categoryHandle, $format);
        }

        public static function getItemName($item)
        {
            // Concrete\Core\Package\Package::getItemName();
            Concrete\Core\Package\Package::getItemName($item);
        }

        /**
         * Uninstalls the package. Removes any blocks, themes, or pages associated with the package.
         */
        public static function uninstall()
        {
            // Concrete\Core\Package\Package::uninstall();
            Concrete\Core\Package\Package::uninstall();
        }

        protected static function validateClearSiteContents($options)
        {
            // Concrete\Core\Package\Package::validateClearSiteContents();
            Concrete\Core\Package\Package::validateClearSiteContents($options);
        }

        public static function swapContent($options)
        {
            // Concrete\Core\Package\Package::swapContent();
            Concrete\Core\Package\Package::swapContent($options);
        }

        public static function testForInstall($package, $testForAlreadyInstalled = "1")
        {
            // Concrete\Core\Package\Package::testForInstall();
            Concrete\Core\Package\Package::testForInstall($package, $testForAlreadyInstalled);
        }

        public static function mapError($testResults)
        {
            // Concrete\Core\Package\Package::mapError();
            Concrete\Core\Package\Package::mapError($testResults);
        }

        public static function getPackagePath()
        {
            // Concrete\Core\Package\Package::getPackagePath();
            Concrete\Core\Package\Package::getPackagePath();
        }

        /**
         * returns a Package object for the given package id, null if not found
         * @param int $pkgID
         * @return Package
         */
        public static function getByID($pkgID)
        {
            // Concrete\Core\Package\Package::getByID();
            Concrete\Core\Package\Package::getByID($pkgID);
        }

        /**
         * returns a Package object for the given package handle, null if not found
         * @param string $pkgHandle
         * @return Package
         */
        public static function getByHandle($pkgHandle)
        {
            // Concrete\Core\Package\Package::getByHandle();
            Concrete\Core\Package\Package::getByHandle($pkgHandle);
        }

        /**
         * @return Package
         */
        public static function install()
        {
            // Concrete\Core\Package\Package::install();
            Concrete\Core\Package\Package::install();
        }

        public static function updateAvailableVersionNumber($vNum)
        {
            // Concrete\Core\Package\Package::updateAvailableVersionNumber();
            Concrete\Core\Package\Package::updateAvailableVersionNumber($vNum);
        }

        public static function upgradeCoreData()
        {
            // Concrete\Core\Package\Package::upgradeCoreData();
            Concrete\Core\Package\Package::upgradeCoreData();
        }

        public static function upgrade()
        {
            // Concrete\Core\Package\Package::upgrade();
            Concrete\Core\Package\Package::upgrade();
        }

        public static function getInstalledHandles()
        {
            // Concrete\Core\Package\Package::getInstalledHandles();
            Concrete\Core\Package\Package::getInstalledHandles();
        }

        public static function getInstalledList()
        {
            // Concrete\Core\Package\Package::getInstalledList();
            Concrete\Core\Package\Package::getInstalledList();
        }

        /**
         * Returns an array of packages that have newer versions in the local packages directory
         * than those which are in the Packages table. This means they're ready to be upgraded
         */
        public static function getLocalUpgradeablePackages()
        {
            // Concrete\Core\Package\Package::getLocalUpgradeablePackages();
            Concrete\Core\Package\Package::getLocalUpgradeablePackages();
        }

        public static function getRemotelyUpgradeablePackages()
        {
            // Concrete\Core\Package\Package::getRemotelyUpgradeablePackages();
            Concrete\Core\Package\Package::getRemotelyUpgradeablePackages();
        }

        /**
         * moves the current package's directory to the trash directory renamed with the package handle and a date code.
         */
        public static function backup()
        {
            // Concrete\Core\Package\Package::backup();
            Concrete\Core\Package\Package::backup();
        }

        /**
         * if a packate was just backed up by this instance of the package object and the packages/package handle directory doesn't exist, this will restore the
         * package from the trash
         */
        public static function restore()
        {
            // Concrete\Core\Package\Package::restore();
            Concrete\Core\Package\Package::restore();
        }

        public static function config($cfKey, $getFullObject = null)
        {
            // Concrete\Core\Package\Package::config();
            Concrete\Core\Package\Package::config($cfKey, $getFullObject);
        }

        public static function saveConfig($cfKey, $value)
        {
            // Concrete\Core\Package\Package::saveConfig();
            Concrete\Core\Package\Package::saveConfig($cfKey, $value);
        }

        public static function clearConfig($cfKey)
        {
            // Concrete\Core\Package\Package::clearConfig();
            Concrete\Core\Package\Package::clearConfig($cfKey);
        }

        public static function getAvailablePackages($filterInstalled = "1")
        {
            // Concrete\Core\Package\Package::getAvailablePackages();
            Concrete\Core\Package\Package::getAvailablePackages($filterInstalled);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class AuthenticationType extends \Concrete\Core\Authentication\AuthenticationType
    {

        public static function getAuthenticationTypeID()
        {
            // Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeID();
            Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeID();
        }

        public static function getAuthenticationTypeHandle()
        {
            // Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeHandle();
            Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeHandle();
        }

        public static function getAuthenticationTypeName()
        {
            // Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeName();
            Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeName();
        }

        public static function getAuthenticationTypeStatus()
        {
            // Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeStatus();
            Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeStatus();
        }

        public static function getAuthenticationTypeDisplayOrder()
        {
            // Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeDisplayOrder();
            Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeDisplayOrder();
        }

        public static function getAuthenticationTypePackageID()
        {
            // Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypePackageID();
            Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypePackageID();
        }

        public static function isEnabled()
        {
            // Concrete\Core\Authentication\AuthenticationType::isEnabled();
            Concrete\Core\Authentication\AuthenticationType::isEnabled();
        }

        public static function getController()
        {
            // Concrete\Core\Authentication\AuthenticationType::getController();
            Concrete\Core\Authentication\AuthenticationType::getController();
        }

        /**
         * AuthenticationType::setAuthenticationTypeDisplayOrder
         * Update the order for display.
         *
         * @param int $order value from 0-n to signify order.
         */
        public static function setAuthenticationTypeDisplayOrder($order)
        {
            // Concrete\Core\Authentication\AuthenticationType::setAuthenticationTypeDisplayOrder();
            Concrete\Core\Authentication\AuthenticationType::setAuthenticationTypeDisplayOrder($order);
        }

        /**
         * @param int $authTypeID
         * @return Concrete5_Model_AuthenticationType
         */
        public static function getByID($authTypeID)
        {
            // Concrete\Core\Authentication\AuthenticationType::getByID();
            Concrete\Core\Authentication\AuthenticationType::getByID($authTypeID);
        }

        /**
         * AuthenticationType::load
         * Load an AuthenticationType from an array.
         *
         * @param array $arr Array of raw sql data.
         */
        public static function load($arr)
        {
            // Concrete\Core\Authentication\AuthenticationType::load();
            Concrete\Core\Authentication\AuthenticationType::load($arr);
        }

        /**
         * AuthenticationType::getList
         * Return a raw list of authentication types, sorted by either installed order or display order.
         *
         * @param bool $sorted true: Sort by installed order, false: Sort by display order
         */
        public static function getList($sorted = null)
        {
            // Concrete\Core\Authentication\AuthenticationType::getList();
            Concrete\Core\Authentication\AuthenticationType::getList($sorted);
        }

        public static function getListSorted()
        {
            // Concrete\Core\Authentication\AuthenticationType::getListSorted();
            Concrete\Core\Authentication\AuthenticationType::getListSorted();
        }

        /**
         * AuthenticationType::getActiveList
         * Return a raw list of /ACTIVE/ authentication types, sorted by either installed order or display order.
         *
         * @param bool $sorted true: Sort by installed order, false: Sort by display order
         */
        public static function getActiveList($sorted = null)
        {
            // Concrete\Core\Authentication\AuthenticationType::getActiveList();
            Concrete\Core\Authentication\AuthenticationType::getActiveList($sorted);
        }

        public static function getActiveListSorted()
        {
            // Concrete\Core\Authentication\AuthenticationType::getActiveListSorted();
            Concrete\Core\Authentication\AuthenticationType::getActiveListSorted();
        }

        /**
         * AuthenticationType::disable
         * Disable an authentication type.
         */
        public static function disable()
        {
            // Concrete\Core\Authentication\AuthenticationType::disable();
            Concrete\Core\Authentication\AuthenticationType::disable();
        }

        /**
         * AuthenticationType::enable
         * Enable an authentication type.
         */
        public static function enable()
        {
            // Concrete\Core\Authentication\AuthenticationType::enable();
            Concrete\Core\Authentication\AuthenticationType::enable();
        }

        /**
         * AuthenticationType::toggle
         * Toggle the active state of an AuthenticationType
         */
        public static function toggle()
        {
            // Concrete\Core\Authentication\AuthenticationType::toggle();
            Concrete\Core\Authentication\AuthenticationType::toggle();
        }

        /**
         * AuthenticationType::delete
         * Remove an AuthenticationType, this should be used sparingly.
         */
        public static function delete()
        {
            // Concrete\Core\Authentication\AuthenticationType::delete();
            Concrete\Core\Authentication\AuthenticationType::delete();
        }

        /**
         * AuthenticationType::getListByPackage
         * Return a list of AuthenticationTypes that are associated with a specific package.
         *
         * @param Package $pkg
         */
        public static function getListByPackage(Concrete\Core\Package\Package $pkg)
        {
            // Concrete\Core\Authentication\AuthenticationType::getListByPackage();
            Concrete\Core\Authentication\AuthenticationType::getListByPackage($pkg);
        }

        /**
         * AuthenticationType::getPackageHandle
         * Return the package handle.
         */
        public static function getPackageHandle()
        {
            // Concrete\Core\Authentication\AuthenticationType::getPackageHandle();
            Concrete\Core\Authentication\AuthenticationType::getPackageHandle();
        }

        /**
         * AuthenticationType::getByHandle
         * Return loaded AuthenticationType with the given handle.
         *
         * @param string $atHandle AuthenticationType handle.
         */
        public static function getByHandle($atHandle)
        {
            // Concrete\Core\Authentication\AuthenticationType::getByHandle();
            Concrete\Core\Authentication\AuthenticationType::getByHandle($atHandle);
        }

        /**
         * AuthenticationType::add
         *
         * @param	string 	$atHandle	New AuthenticationType handle
         * @param	string	$atName		New AuthenticationType name, expect this to be presented with "%s Authentication Type"
         * @param	int		$order		Order int, used to order the display of AuthenticationTypes
         * @param	Package	$pkg		Package object to which this AuthenticationType is associated.
         * @return	AuthenticationType	Returns a loaded authentication type.
         */
        public static function add($atHandle, $atName, $order = null, $pkg = null)
        {
            // Concrete\Core\Authentication\AuthenticationType::add();
            Concrete\Core\Authentication\AuthenticationType::add($atHandle, $atName, $order, $pkg);
        }

        /**
         * AuthenticationType::getAuthenticationTypeFilePath
         * Return the path to a file, this is always BASE_URL.DIR_REL.FILE
         *
         * @param string $_file the relative path to the file.
         */
        public static function getAuthenticationTypeFilePath($_file)
        {
            // Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeFilePath();
            Concrete\Core\Authentication\AuthenticationType::getAuthenticationTypeFilePath($_file);
        }

        /**
         * AuthenticationType::mapAuthenticationTypeFilePath
         * Return the first existing file path in this order:
         *  - /models/authentication/types/HANDLE
         *  - /packages/PKGHANDLE/authentication/types/HANDLE
         *  - /concrete/models/authentication/types/HANDLE
         *  - /concrete/core/models/authentication/types/HANDLE
         *
         * @param string $_file The filename you want.
         * @return string This will return false if the file is not found.
         */
        protected static function mapAuthenticationTypeFilePath($_file)
        {
            // Concrete\Core\Authentication\AuthenticationType::mapAuthenticationTypeFilePath();
            Concrete\Core\Authentication\AuthenticationType::mapAuthenticationTypeFilePath($_file);
        }

        /**
         * AuthenticationType::renderTypeForm
         * Render the settings form for this type.
         * Settings forms are expected to handle their own submissions and redirect to the appropriate page.
         * Otherwise, if the method exists, all $_REQUEST variables with the arrangement: HANDLE[]
         * in an array to the AuthenticationTypeController::saveTypeForm
         */
        public static function renderTypeForm()
        {
            // Concrete\Core\Authentication\AuthenticationType::renderTypeForm();
            Concrete\Core\Authentication\AuthenticationType::renderTypeForm();
        }

        /**
         * AuthenticationType::renderForm
         * Render the login form for this authentication type.
         */
        public static function renderForm($element = "form")
        {
            // Concrete\Core\Authentication\AuthenticationType::renderForm();
            Concrete\Core\Authentication\AuthenticationType::renderForm($element);
        }

        /**
         * AuthenticationType::renderHook
         * Render the hook form for saving the profile settings.
         * All settings are expected to be saved by each individual authentication type
         */
        public static function renderHook()
        {
            // Concrete\Core\Authentication\AuthenticationType::renderHook();
            Concrete\Core\Authentication\AuthenticationType::renderHook();
        }

        /**
         * AuthenticationType::loadController
         * Load the AuthenticationTypeController into the AuthenticationType
         */
        protected static function loadController()
        {
            // Concrete\Core\Authentication\AuthenticationType::loadController();
            Concrete\Core\Authentication\AuthenticationType::loadController();
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class GroupTree extends \Concrete\Core\Tree\Type\Group
    {

        public static function getTreeDisplayName()
        {
            // Concrete\Core\Tree\Type\Group::getTreeDisplayName();
            Concrete\Core\Tree\Type\Group::getTreeDisplayName();
        }

        public static function get()
        {
            // Concrete\Core\Tree\Type\Group::get();
            Concrete\Core\Tree\Type\Group::get();
        }

        protected static function deleteDetails()
        {
            // Concrete\Core\Tree\Type\Group::deleteDetails();
            Concrete\Core\Tree\Type\Group::deleteDetails();
        }

        public static function add()
        {
            // Concrete\Core\Tree\Type\Group::add();
            Concrete\Core\Tree\Type\Group::add();
        }

        protected static function loadDetails()
        {
            // Concrete\Core\Tree\Type\Group::loadDetails();
            Concrete\Core\Tree\Type\Group::loadDetails();
        }

        public static function ensureGroupNodes()
        {
            // Concrete\Core\Tree\Type\Group::ensureGroupNodes();
            Concrete\Core\Tree\Type\Group::ensureGroupNodes();
        }

        public static function setSelectedTreeNodeID($nodeID)
        {
            // Concrete\Core\Tree\Tree::setSelectedTreeNodeID();
            Concrete\Core\Tree\Tree::setSelectedTreeNodeID($nodeID);
        }

        public static function getSelectedTreeNodeID()
        {
            // Concrete\Core\Tree\Tree::getSelectedTreeNodeID();
            Concrete\Core\Tree\Tree::getSelectedTreeNodeID();
        }

        public static function getTreeTypeID()
        {
            // Concrete\Core\Tree\Tree::getTreeTypeID();
            Concrete\Core\Tree\Tree::getTreeTypeID();
        }

        public static function getTreeTypeObject()
        {
            // Concrete\Core\Tree\Tree::getTreeTypeObject();
            Concrete\Core\Tree\Tree::getTreeTypeObject();
        }

        public static function getTreeTypeHandle()
        {
            // Concrete\Core\Tree\Tree::getTreeTypeHandle();
            Concrete\Core\Tree\Tree::getTreeTypeHandle();
        }

        public static function getTreeID()
        {
            // Concrete\Core\Tree\Tree::getTreeID();
            Concrete\Core\Tree\Tree::getTreeID();
        }

        public static function getRootTreeNodeObject()
        {
            // Concrete\Core\Tree\Tree::getRootTreeNodeObject();
            Concrete\Core\Tree\Tree::getRootTreeNodeObject();
        }

        public static function setRequest($data)
        {
            // Concrete\Core\Tree\Tree::setRequest();
            Concrete\Core\Tree\Tree::setRequest($data);
        }

        public static function delete()
        {
            // Concrete\Core\Tree\Tree::delete();
            Concrete\Core\Tree\Tree::delete();
        }

        public static function duplicate()
        {
            // Concrete\Core\Tree\Tree::duplicate();
            Concrete\Core\Tree\Tree::duplicate();
        }

        public static function getJSON()
        {
            // Concrete\Core\Tree\Tree::getJSON();
            Concrete\Core\Tree\Tree::getJSON();
        }

        final public static function getByID($treeID)
        {
            // Concrete\Core\Tree\Tree::getByID();
            Concrete\Core\Tree\Tree::getByID($treeID);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class GroupTreeNode extends \Concrete\Core\Tree\Node\Type\Group
    {

        public static function getPermissionResponseClassName()
        {
            // Concrete\Core\Tree\Node\Type\Group::getPermissionResponseClassName();
            Concrete\Core\Tree\Node\Type\Group::getPermissionResponseClassName();
        }

        public static function getPermissionAssignmentClassName()
        {
            // Concrete\Core\Tree\Node\Type\Group::getPermissionAssignmentClassName();
            Concrete\Core\Tree\Node\Type\Group::getPermissionAssignmentClassName();
        }

        public static function getPermissionObjectKeyCategoryHandle()
        {
            // Concrete\Core\Tree\Node\Type\Group::getPermissionObjectKeyCategoryHandle();
            Concrete\Core\Tree\Node\Type\Group::getPermissionObjectKeyCategoryHandle();
        }

        public static function getTreeNodeGroupID()
        {
            // Concrete\Core\Tree\Node\Type\Group::getTreeNodeGroupID();
            Concrete\Core\Tree\Node\Type\Group::getTreeNodeGroupID();
        }

        public static function getTreeNodeGroupObject()
        {
            // Concrete\Core\Tree\Node\Type\Group::getTreeNodeGroupObject();
            Concrete\Core\Tree\Node\Type\Group::getTreeNodeGroupObject();
        }

        public static function getTreeNodeDisplayName()
        {
            // Concrete\Core\Tree\Node\Type\Group::getTreeNodeDisplayName();
            Concrete\Core\Tree\Node\Type\Group::getTreeNodeDisplayName();
        }

        public static function loadDetails()
        {
            // Concrete\Core\Tree\Node\Type\Group::loadDetails();
            Concrete\Core\Tree\Node\Type\Group::loadDetails();
        }

        public static function move(Concrete\Core\Tree\Node\Node $newParent)
        {
            // Concrete\Core\Tree\Node\Type\Group::move();
            Concrete\Core\Tree\Node\Type\Group::move($newParent);
        }

        public static function getTreeNodeByGroupID($gID)
        {
            // Concrete\Core\Tree\Node\Type\Group::getTreeNodeByGroupID();
            Concrete\Core\Tree\Node\Type\Group::getTreeNodeByGroupID($gID);
        }

        public static function deleteDetails()
        {
            // Concrete\Core\Tree\Node\Type\Group::deleteDetails();
            Concrete\Core\Tree\Node\Type\Group::deleteDetails();
        }

        public static function getTreeNodeJSON()
        {
            // Concrete\Core\Tree\Node\Type\Group::getTreeNodeJSON();
            Concrete\Core\Tree\Node\Type\Group::getTreeNodeJSON();
        }

        public static function setTreeNodeGroup(Concrete\Core\User\Group\Group $g)
        {
            // Concrete\Core\Tree\Node\Type\Group::setTreeNodeGroup();
            Concrete\Core\Tree\Node\Type\Group::setTreeNodeGroup($g);
        }

        public static function add($group = null, $parent = null)
        {
            // Concrete\Core\Tree\Node\Type\Group::add();
            Concrete\Core\Tree\Node\Type\Group::add($group, $parent);
        }

        public static function getPermissionObjectIdentifier()
        {
            // Concrete\Core\Tree\Node\Node::getPermissionObjectIdentifier();
            Concrete\Core\Tree\Node\Node::getPermissionObjectIdentifier();
        }

        public static function getTreeNodeID()
        {
            // Concrete\Core\Tree\Node\Node::getTreeNodeID();
            Concrete\Core\Tree\Node\Node::getTreeNodeID();
        }

        public static function getTreeNodeParentID()
        {
            // Concrete\Core\Tree\Node\Node::getTreeNodeParentID();
            Concrete\Core\Tree\Node\Node::getTreeNodeParentID();
        }

        public static function getTreeNodeParentObject()
        {
            // Concrete\Core\Tree\Node\Node::getTreeNodeParentObject();
            Concrete\Core\Tree\Node\Node::getTreeNodeParentObject();
        }

        public static function getTreeObject()
        {
            // Concrete\Core\Tree\Node\Node::getTreeObject();
            Concrete\Core\Tree\Node\Node::getTreeObject();
        }

        public static function getTreeID()
        {
            // Concrete\Core\Tree\Node\Node::getTreeID();
            Concrete\Core\Tree\Node\Node::getTreeID();
        }

        public static function getTreeNodeTypeID()
        {
            // Concrete\Core\Tree\Node\Node::getTreeNodeTypeID();
            Concrete\Core\Tree\Node\Node::getTreeNodeTypeID();
        }

        public static function getTreeNodeTypeObject()
        {
            // Concrete\Core\Tree\Node\Node::getTreeNodeTypeObject();
            Concrete\Core\Tree\Node\Node::getTreeNodeTypeObject();
        }

        public static function getTreeNodeTypeHandle()
        {
            // Concrete\Core\Tree\Node\Node::getTreeNodeTypeHandle();
            Concrete\Core\Tree\Node\Node::getTreeNodeTypeHandle();
        }

        public static function getChildNodes()
        {
            // Concrete\Core\Tree\Node\Node::getChildNodes();
            Concrete\Core\Tree\Node\Node::getChildNodes();
        }

        public static function overrideParentTreeNodePermissions()
        {
            // Concrete\Core\Tree\Node\Node::overrideParentTreeNodePermissions();
            Concrete\Core\Tree\Node\Node::overrideParentTreeNodePermissions();
        }

        public static function getTreeNodePermissionsNodeID()
        {
            // Concrete\Core\Tree\Node\Node::getTreeNodePermissionsNodeID();
            Concrete\Core\Tree\Node\Node::getTreeNodePermissionsNodeID();
        }

        public static function getTreeNodeChildCount()
        {
            // Concrete\Core\Tree\Node\Node::getTreeNodeChildCount();
            Concrete\Core\Tree\Node\Node::getTreeNodeChildCount();
        }

        /**
         * Transforms a node to another node
         */
        public static function transformNode($treeNodeType)
        {
            // Concrete\Core\Tree\Node\Node::transformNode();
            Concrete\Core\Tree\Node\Node::transformNode($treeNodeType);
        }

        /**
         * Returns an array of all parents of this tree node
         */
        public static function getTreeNodeParentArray()
        {
            // Concrete\Core\Tree\Node\Node::getTreeNodeParentArray();
            Concrete\Core\Tree\Node\Node::getTreeNodeParentArray();
        }

        public static function selectChildrenNodesByID($nodeID)
        {
            // Concrete\Core\Tree\Node\Node::selectChildrenNodesByID();
            Concrete\Core\Tree\Node\Node::selectChildrenNodesByID($nodeID);
        }

        public static function duplicate($parent = null)
        {
            // Concrete\Core\Tree\Node\Node::duplicate();
            Concrete\Core\Tree\Node\Node::duplicate($parent);
        }

        protected static function duplicateChildren(Concrete\Core\Tree\Node\NodeType $node)
        {
            // Concrete\Core\Tree\Node\Node::duplicateChildren();
            Concrete\Core\Tree\Node\Node::duplicateChildren($node);
        }

        public static function setTreeNodePermissionsToGlobal()
        {
            // Concrete\Core\Tree\Node\Node::setTreeNodePermissionsToGlobal();
            Concrete\Core\Tree\Node\Node::setTreeNodePermissionsToGlobal();
        }

        public static function setTreeNodePermissionsToOverride()
        {
            // Concrete\Core\Tree\Node\Node::setTreeNodePermissionsToOverride();
            Concrete\Core\Tree\Node\Node::setTreeNodePermissionsToOverride();
        }

        public static function getAllChildNodeIDs()
        {
            // Concrete\Core\Tree\Node\Node::getAllChildNodeIDs();
            Concrete\Core\Tree\Node\Node::getAllChildNodeIDs();
        }

        public static function setTreeNodeTreeID($treeID)
        {
            // Concrete\Core\Tree\Node\Node::setTreeNodeTreeID();
            Concrete\Core\Tree\Node\Node::setTreeNodeTreeID($treeID);
        }

        protected static function rescanChildrenDisplayOrder()
        {
            // Concrete\Core\Tree\Node\Node::rescanChildrenDisplayOrder();
            Concrete\Core\Tree\Node\Node::rescanChildrenDisplayOrder();
        }

        public static function saveChildOrder($orderedIDs)
        {
            // Concrete\Core\Tree\Node\Node::saveChildOrder();
            Concrete\Core\Tree\Node\Node::saveChildOrder($orderedIDs);
        }

        public static function populateChildren()
        {
            // Concrete\Core\Tree\Node\Node::populateChildren();
            Concrete\Core\Tree\Node\Node::populateChildren();
        }

        public static function populateDirectChildrenOnly()
        {
            // Concrete\Core\Tree\Node\Node::populateDirectChildrenOnly();
            Concrete\Core\Tree\Node\Node::populateDirectChildrenOnly();
        }

        public static function delete()
        {
            // Concrete\Core\Tree\Node\Node::delete();
            Concrete\Core\Tree\Node\Node::delete();
        }

        public static function getByID($treeNodeID)
        {
            // Concrete\Core\Tree\Node\Node::getByID();
            Concrete\Core\Tree\Node\Node::getByID($treeNodeID);
        }

        public static function loadError($error)
        {
            // Concrete\Core\Foundation\Object::loadError();
            Concrete\Core\Foundation\Object::loadError($error);
        }

        public static function isError()
        {
            // Concrete\Core\Foundation\Object::isError();
            Concrete\Core\Foundation\Object::isError();
        }

        public static function getError()
        {
            // Concrete\Core\Foundation\Object::getError();
            Concrete\Core\Foundation\Object::getError();
        }

        public static function setPropertiesFromArray($arr)
        {
            // Concrete\Core\Foundation\Object::setPropertiesFromArray();
            Concrete\Core\Foundation\Object::setPropertiesFromArray($arr);
        }

        public static function camelcase($file)
        {
            // Concrete\Core\Foundation\Object::camelcase();
            Concrete\Core\Foundation\Object::camelcase($file);
        }

        public static function uncamelcase($string)
        {
            // Concrete\Core\Foundation\Object::uncamelcase();
            Concrete\Core\Foundation\Object::uncamelcase($string);
        }

    }

    class Zend_Queue_Adapter_Concrete5 extends \Concrete\Core\Utility\ZendQueueAdapter
    {

        public static function __construct($options, Zend_Queue $queue = null)
        {
            // Concrete\Core\Utility\ZendQueueAdapter::__construct();
            Concrete\Core\Utility\ZendQueueAdapter::__construct($options, $queue);
        }

        /**
         * Initialize Db adapter using 'driverOptions' section of the _options array
         *
         * Throws an exception if the adapter cannot connect to DB.
         *
         * @return Zend_Db_Adapter_Abstract
         * @throws Zend_Queue_Exception
         */
        protected static function _initDbAdapter()
        {
            // Zend_Queue_Adapter_Db::_initDbAdapter();
            Zend_Queue_Adapter_Db::_initDbAdapter();
        }

        /**
         * Does a queue already exist?
         *
         * Throws an exception if the adapter cannot determine if a queue exists.
         * use isSupported('isExists') to determine if an adapter can test for
         * queue existance.
         *
         * @param  string $name
         * @return boolean
         * @throws Zend_Queue_Exception
         */
        public static function isExists($name)
        {
            // Zend_Queue_Adapter_Db::isExists();
            Zend_Queue_Adapter_Db::isExists($name);
        }

        /**
         * Create a new queue
         *
         * Visibility timeout is how long a message is left in the queue "invisible"
         * to other readers.  If the message is acknowleged (deleted) before the
         * timeout, then the message is deleted.  However, if the timeout expires
         * then the message will be made available to other queue readers.
         *
         * @param  string  $name    queue name
         * @param  integer $timeout default visibility timeout
         * @return boolean
         * @throws Zend_Queue_Exception - database error
         */
        public static function create($name, $timeout = null)
        {
            // Zend_Queue_Adapter_Db::create();
            Zend_Queue_Adapter_Db::create($name, $timeout);
        }

        /**
         * Delete a queue and all of it's messages
         *
         * Returns false if the queue is not found, true if the queue exists
         *
         * @param  string  $name queue name
         * @return boolean
         * @throws Zend_Queue_Exception - database error
         */
        public static function delete($name)
        {
            // Zend_Queue_Adapter_Db::delete();
            Zend_Queue_Adapter_Db::delete($name);
        }

        public static function getQueues()
        {
            // Zend_Queue_Adapter_Db::getQueues();
            Zend_Queue_Adapter_Db::getQueues();
        }

        /**
         * Return the approximate number of messages in the queue
         *
         * @param  Zend_Queue $queue
         * @return integer
         * @throws Zend_Queue_Exception
         */
        public static function count(Zend_Queue $queue = null)
        {
            // Zend_Queue_Adapter_Db::count();
            Zend_Queue_Adapter_Db::count($queue);
        }

        /**
         * Send a message to the queue
         *
         * @param  string     $message Message to send to the active queue
         * @param  Zend_Queue $queue
         * @return Zend_Queue_Message
         * @throws Zend_Queue_Exception - database error
         */
        public static function send($message, Zend_Queue $queue = null)
        {
            // Zend_Queue_Adapter_Db::send();
            Zend_Queue_Adapter_Db::send($message, $queue);
        }

        /**
         * Get messages in the queue
         *
         * @param  integer    $maxMessages  Maximum number of messages to return
         * @param  integer    $timeout      Visibility timeout for these messages
         * @param  Zend_Queue $queue
         * @return Zend_Queue_Message_Iterator
         * @throws Zend_Queue_Exception - database error
         */
        public static function receive($maxMessages = null, $timeout = null, Zend_Queue $queue = null)
        {
            // Zend_Queue_Adapter_Db::receive();
            Zend_Queue_Adapter_Db::receive($maxMessages, $timeout, $queue);
        }

        /**
         * Delete a message from the queue
         *
         * Returns true if the message is deleted, false if the deletion is
         * unsuccessful.
         *
         * @param  Zend_Queue_Message $message
         * @return boolean
         * @throws Zend_Queue_Exception - database error
         */
        public static function deleteMessage(Zend_Queue_Message $message)
        {
            // Zend_Queue_Adapter_Db::deleteMessage();
            Zend_Queue_Adapter_Db::deleteMessage($message);
        }

        /**
         * Return a list of queue capabilities functions
         *
         * $array['function name'] = true or false
         * true is supported, false is not supported.
         *
         * @param  string $name
         * @return array
         */
        public static function getCapabilities()
        {
            // Zend_Queue_Adapter_Db::getCapabilities();
            Zend_Queue_Adapter_Db::getCapabilities();
        }

        /**
         * Get the queue ID
         *
         * Returns the queue's row identifier.
         *
         * @param  string       $name
         * @return integer|null
         * @throws Zend_Queue_Exception
         */
        protected static function getQueueId($name)
        {
            // Zend_Queue_Adapter_Db::getQueueId();
            Zend_Queue_Adapter_Db::getQueueId($name);
        }

        /**
         * get the Zend_Queue class that is attached to this object
         *
         * @return Zend_Queue|null
         */
        public static function getQueue()
        {
            // Zend_Queue_Adapter_AdapterAbstract::getQueue();
            Zend_Queue_Adapter_AdapterAbstract::getQueue();
        }

        /**
         * set the Zend_Queue class for this object
         *
         * @param  Zend_Queue $queue
         * @return Zend_Queue_Adapter_AdapterInterface
         */
        public static function setQueue(Zend_Queue $queue)
        {
            // Zend_Queue_Adapter_AdapterAbstract::setQueue();
            Zend_Queue_Adapter_AdapterAbstract::setQueue($queue);
        }

        /**
         * Returns the configuration options in this adapter.
         *
         * @return array
         */
        public static function getOptions()
        {
            // Zend_Queue_Adapter_AdapterAbstract::getOptions();
            Zend_Queue_Adapter_AdapterAbstract::getOptions();
        }

        /**
         * Indicates if a function is supported or not.
         *
         * @param  string $name
         * @return boolean
         */
        public static function isSupported($name)
        {
            // Zend_Queue_Adapter_AdapterAbstract::isSupported();
            Zend_Queue_Adapter_AdapterAbstract::isSupported($name);
        }

    }

    /**
     * @deprecated
     */
    class Loader extends \Concrete\Core\Legacy\Loader
    {

        public static function db()
        {
            // Concrete\Core\Legacy\Loader::db();
            Concrete\Core\Legacy\Loader::db();
        }

        public static function helper($service, $pkgHandle = null)
        {
            // Concrete\Core\Legacy\Loader::helper();
            Concrete\Core\Legacy\Loader::helper($service, $pkgHandle);
        }

        public static function packageElement($file, $pkgHandle, $args = null)
        {
            // Concrete\Core\Legacy\Loader::packageElement();
            Concrete\Core\Legacy\Loader::packageElement($file, $pkgHandle, $args);
        }

        public static function element($_file, $args = null, $_pkgHandle = null)
        {
            // Concrete\Core\Legacy\Loader::element();
            Concrete\Core\Legacy\Loader::element($_file, $args, $_pkgHandle);
        }

        public static function model($model, $pkgHandle = null)
        {
            // Concrete\Core\Legacy\Loader::model();
            Concrete\Core\Legacy\Loader::model($model, $pkgHandle);
        }

        public static function library($library, $pkgHandle = null)
        {
            // Concrete\Core\Legacy\Loader::library();
            Concrete\Core\Legacy\Loader::library($library, $pkgHandle);
        }

    }

    /**
     * @deprecated
     */
    class TaskPermission extends \Concrete\Core\Legacy\TaskPermission
    {

        public static function getByHandle($handle)
        {
            // Concrete\Core\Legacy\TaskPermission::getByHandle();
            Concrete\Core\Legacy\TaskPermission::getByHandle($handle);
        }

        /**
         * Checks to see if there is a fatal error with this particular permission call.
         */
        public static function isError()
        {
            // Concrete\Core\Permission\Checker::isError();
            Concrete\Core\Permission\Checker::isError();
        }

        /**
         * Returns the error code if there is one
         */
        public static function getError()
        {
            // Concrete\Core\Permission\Checker::getError();
            Concrete\Core\Permission\Checker::getError();
        }

        /**
         * Legacy
         * @private
         */
        public static function getOriginalObject()
        {
            // Concrete\Core\Permission\Checker::getOriginalObject();
            Concrete\Core\Permission\Checker::getOriginalObject();
        }

        public static function __construct($object = null)
        {
            // Concrete\Core\Permission\Checker::__construct();
            Concrete\Core\Permission\Checker::__construct($object);
        }

        public static function getResponseObject()
        {
            // Concrete\Core\Permission\Checker::getResponseObject();
            Concrete\Core\Permission\Checker::getResponseObject();
        }

        /**
         * We take any permissions function run on the permissions class and send it into the category
         * object
         */
        public static function __call($f, $a)
        {
            // Concrete\Core\Permission\Checker::__call();
            Concrete\Core\Permission\Checker::__call($f, $a);
        }

    }

    /**
     * @deprecated
     */
    class FilePermissions extends \Concrete\Core\Legacy\FilePermissions
    {

        public static function getGlobal()
        {
            // Concrete\Core\Legacy\FilePermissions::getGlobal();
            Concrete\Core\Legacy\FilePermissions::getGlobal();
        }

    }

    class Core extends \Concrete\Core\Support\Facade\Application
    {

        public static function getFacadeAccessor()
        {
            // Concrete\Core\Support\Facade\Application::getFacadeAccessor();
            Concrete\Core\Support\Facade\Application::getFacadeAccessor();
        }

        /**
         * Get the root object behind the facade.
         *
         * @return mixed
         */
        public static function getFacadeRoot()
        {
            // Concrete\Core\Support\Facade\Facade::getFacadeRoot();
            Concrete\Core\Support\Facade\Facade::getFacadeRoot();
        }

        /**
         * Resolve the facade root instance from the container.
         *
         * @param  string  $name
         * @return mixed
         */
        protected static function resolveFacadeInstance($name)
        {
            // Concrete\Core\Support\Facade\Facade::resolveFacadeInstance();
            Concrete\Core\Support\Facade\Facade::resolveFacadeInstance($name);
        }

        /**
         * Clear a resolved facade instance.
         *
         * @param  string  $name
         * @return void
         */
        public static function clearResolvedInstance($name)
        {
            // Concrete\Core\Support\Facade\Facade::clearResolvedInstance();
            Concrete\Core\Support\Facade\Facade::clearResolvedInstance($name);
        }

        /**
         * Clear all of the resolved instances.
         *
         * @return void
         */
        public static function clearResolvedInstances()
        {
            // Concrete\Core\Support\Facade\Facade::clearResolvedInstances();
            Concrete\Core\Support\Facade\Facade::clearResolvedInstances();
        }

        /**
         * Get the application instance behind the facade.
         *
         * @return \Illuminate\Foundation\Application
         */
        public static function getFacadeApplication()
        {
            // Concrete\Core\Support\Facade\Facade::getFacadeApplication();
            Concrete\Core\Support\Facade\Facade::getFacadeApplication();
        }

        /**
         * Set the application instance.
         *
         * @param  \Illuminate\Foundation\Application  $app
         * @return void
         */
        public static function setFacadeApplication($app)
        {
            // Concrete\Core\Support\Facade\Facade::setFacadeApplication();
            Concrete\Core\Support\Facade\Facade::setFacadeApplication($app);
        }

        /**
         * Handle dynamic, static calls to the object.
         *
         * @param  string  $method
         * @param  array   $args
         * @return mixed
         */
        public static function __callStatic($method, $args)
        {
            // Concrete\Core\Support\Facade\Facade::__callStatic();
            Concrete\Core\Support\Facade\Facade::__callStatic($method, $args);
        }

    }

    class Session extends \Concrete\Core\Support\Facade\Session
    {

        public static function getFacadeAccessor()
        {
            // Concrete\Core\Support\Facade\Session::getFacadeAccessor();
            Concrete\Core\Support\Facade\Session::getFacadeAccessor();
        }

        /**
         * Get the root object behind the facade.
         *
         * @return mixed
         */
        public static function getFacadeRoot()
        {
            // Concrete\Core\Support\Facade\Facade::getFacadeRoot();
            Concrete\Core\Support\Facade\Facade::getFacadeRoot();
        }

        /**
         * Resolve the facade root instance from the container.
         *
         * @param  string  $name
         * @return mixed
         */
        protected static function resolveFacadeInstance($name)
        {
            // Concrete\Core\Support\Facade\Facade::resolveFacadeInstance();
            Concrete\Core\Support\Facade\Facade::resolveFacadeInstance($name);
        }

        /**
         * Clear a resolved facade instance.
         *
         * @param  string  $name
         * @return void
         */
        public static function clearResolvedInstance($name)
        {
            // Concrete\Core\Support\Facade\Facade::clearResolvedInstance();
            Concrete\Core\Support\Facade\Facade::clearResolvedInstance($name);
        }

        /**
         * Clear all of the resolved instances.
         *
         * @return void
         */
        public static function clearResolvedInstances()
        {
            // Concrete\Core\Support\Facade\Facade::clearResolvedInstances();
            Concrete\Core\Support\Facade\Facade::clearResolvedInstances();
        }

        /**
         * Get the application instance behind the facade.
         *
         * @return \Illuminate\Foundation\Application
         */
        public static function getFacadeApplication()
        {
            // Concrete\Core\Support\Facade\Facade::getFacadeApplication();
            Concrete\Core\Support\Facade\Facade::getFacadeApplication();
        }

        /**
         * Set the application instance.
         *
         * @param  \Illuminate\Foundation\Application  $app
         * @return void
         */
        public static function setFacadeApplication($app)
        {
            // Concrete\Core\Support\Facade\Facade::setFacadeApplication();
            Concrete\Core\Support\Facade\Facade::setFacadeApplication($app);
        }

        /**
         * Handle dynamic, static calls to the object.
         *
         * @param  string  $method
         * @param  array   $args
         * @return mixed
         */
        public static function __callStatic($method, $args)
        {
            // Concrete\Core\Support\Facade\Facade::__callStatic();
            Concrete\Core\Support\Facade\Facade::__callStatic($method, $args);
        }

    }

    class Database extends \Concrete\Core\Support\Facade\Database
    {

        public static function getFacadeAccessor()
        {
            // Concrete\Core\Support\Facade\Database::getFacadeAccessor();
            Concrete\Core\Support\Facade\Database::getFacadeAccessor();
        }

        public static function get()
        {
            // Concrete\Core\Support\Facade\Database::get();
            Concrete\Core\Support\Facade\Database::get();
        }

        /**
         * Get the root object behind the facade.
         *
         * @return mixed
         */
        public static function getFacadeRoot()
        {
            // Concrete\Core\Support\Facade\Facade::getFacadeRoot();
            Concrete\Core\Support\Facade\Facade::getFacadeRoot();
        }

        /**
         * Resolve the facade root instance from the container.
         *
         * @param  string  $name
         * @return mixed
         */
        protected static function resolveFacadeInstance($name)
        {
            // Concrete\Core\Support\Facade\Facade::resolveFacadeInstance();
            Concrete\Core\Support\Facade\Facade::resolveFacadeInstance($name);
        }

        /**
         * Clear a resolved facade instance.
         *
         * @param  string  $name
         * @return void
         */
        public static function clearResolvedInstance($name)
        {
            // Concrete\Core\Support\Facade\Facade::clearResolvedInstance();
            Concrete\Core\Support\Facade\Facade::clearResolvedInstance($name);
        }

        /**
         * Clear all of the resolved instances.
         *
         * @return void
         */
        public static function clearResolvedInstances()
        {
            // Concrete\Core\Support\Facade\Facade::clearResolvedInstances();
            Concrete\Core\Support\Facade\Facade::clearResolvedInstances();
        }

        /**
         * Get the application instance behind the facade.
         *
         * @return \Illuminate\Foundation\Application
         */
        public static function getFacadeApplication()
        {
            // Concrete\Core\Support\Facade\Facade::getFacadeApplication();
            Concrete\Core\Support\Facade\Facade::getFacadeApplication();
        }

        /**
         * Set the application instance.
         *
         * @param  \Illuminate\Foundation\Application  $app
         * @return void
         */
        public static function setFacadeApplication($app)
        {
            // Concrete\Core\Support\Facade\Facade::setFacadeApplication();
            Concrete\Core\Support\Facade\Facade::setFacadeApplication($app);
        }

        /**
         * Handle dynamic, static calls to the object.
         *
         * @param  string  $method
         * @param  array   $args
         * @return mixed
         */
        public static function __callStatic($method, $args)
        {
            // Concrete\Core\Support\Facade\Facade::__callStatic();
            Concrete\Core\Support\Facade\Facade::__callStatic($method, $args);
        }

    }

    class Route extends \Concrete\Core\Routing\Router
    {

        public static function __construct()
        {
            // Concrete\Core\Routing\Router::__construct();
            Concrete\Core\Routing\Router::__construct();
        }

        public static function getList()
        {
            // Concrete\Core\Routing\Router::getList();
            Concrete\Core\Routing\Router::getList();
        }

        public static function setRequest(Concrete\Core\Http\Request $req)
        {
            // Concrete\Core\Routing\Router::setRequest();
            Concrete\Core\Routing\Router::setRequest($req);
        }

        public static function register($rtPath, $callback, $rtHandle = null, $additionalAttributes = null)
        {
            // Concrete\Core\Routing\Router::register();
            Concrete\Core\Routing\Router::register($rtPath, $callback, $rtHandle, $additionalAttributes);
        }

        public static function execute(Concrete\Core\Routing\Route $route, $parameters)
        {
            // Concrete\Core\Routing\Router::execute();
            Concrete\Core\Routing\Router::execute($route, $parameters);
        }

        /**
         * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes
         * @access public
         * @param $path string
         * @param $theme object, if null site theme is default
         * @return void
         */
        public static function setThemeByRoute($path, $theme = null, $wrapper = "view.php")
        {
            // Concrete\Core\Routing\Router::setThemeByRoute();
            Concrete\Core\Routing\Router::setThemeByRoute($path, $theme, $wrapper);
        }

        /**
         * This grabs the theme for a particular path, if one exists in the themePaths array
         * @param string $path
         * @return string|boolean
         */
        public static function getThemeByRoute($path)
        {
            // Concrete\Core\Routing\Router::getThemeByRoute();
            Concrete\Core\Routing\Router::getThemeByRoute($path);
        }

    }

}
