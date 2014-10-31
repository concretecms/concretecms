<?php

namespace Concrete\Core\Http;

use Loader;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * @package    Core
 * @category   Concrete
 * @author     Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that represents a particular request to the Concrete-powered website. The request object then determines what is being requested, based on the path, and presents itself to the rest of the dispatcher (which loads the page, etc...)
 *
 * @package    Core
 * @author     Andrew Embler <andrew@concrete5.org>
 * @category   Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Request extends SymfonyRequest
{

    protected $hasCustomRequestUser;
    protected $customRequestUser;
    protected $customRequestDateTime;
    protected $c;

    public function getCurrentPage()
    {
        return $this->c;
    }

    public function setCurrentPage(\Concrete\Core\Page\Page $c)
    {
        $this->c = $c;
    }

    public function getCustomRequestUser()
    {
        return $this->customRequestUser;
    }

    public function setCustomRequestUser($ui)
    {
        $this->hasCustomRequestUser = true;
        $this->customRequestUser = $ui;
    }

    public function hasCustomRequestUser()
    {
        return $this->hasCustomRequestUser;
    }

    public function getCustomRequestDateTime()
    {
        return $this->customRequestDateTime;
    }

    public function setCustomRequestDateTime($date)
    {
        $this->customRequestDateTime = $date;
    }

    /**
     * Determines whether a request matches a particular pattern
     */
    public function matches($pattern)
    {
        return Loader::helper('text')->match($pattern, $this->getPath());
    }

    /**
     * Returns the full path for a request
     */
    public function getPath()
    {
        $pathInfo = rawurldecode($this->getPathInfo());
        $path = '/' . trim($pathInfo, '/');
        return ($path == '/') ? '' : $path;
    }

    /**
     * Get a parameter from the request body `$_POST`
     *
     * @param string $key
     * @param null   $default
     * @param bool   $deep
     * @return mixed
     */
    public function post($key, $default = null, $deep = false)
    {
        return $this->request->get($key, $default, $deep);
    }

    /**
     * Get a parameter from the query string `$_GET`
     *
     * @param string $key
     * @param null   $default
     * @param bool   $deep
     * @return mixed
     */
    public function get($key, $default = null, $deep = false)
    {
        return $this->query->get($key, $default, $deep);
    }

    /**
     * Get a parameter from either get or post
     * This is a convenience method, it should only be used if the incoming request type is unknowable.
     *
     * @param      $key
     * @param null $default
     * @param bool $deep
     * @return mixed
     */
    public function request($key, $default = null, $deep = false)
    {
        $result = $this->get($key, $this, $deep);

        return $result === $this ? $this->post($key, $default, $deep) : $result;
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
     * @param string $key     the key
     * @param mixed  $default the default value
     * @param bool   $deep    is parameter deep in multidimensional array
     *
     * @return mixed
     */
    public function query($key, $default = null, $deep = false)
    {
        $result = $this->query->get($key, $this, $deep);
        if ($result === $this) {
            $result = $this->attributes->get($key, $this, $deep);
        }
        if ($result === $this) {
            $result = $this->request->get($key, $this, $deep);
        }
        if ($result === $this) {
            return $default;
        }
        return $result;
    }

    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

}
