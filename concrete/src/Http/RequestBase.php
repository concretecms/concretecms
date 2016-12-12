<?php
namespace Concrete\Core\Http;

use Core;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * \@package    Core
 *
 * @category   Concrete
 *
 * @author     Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * An object that represents a particular request to the Concrete-powered website. The request object then determines what is being requested, based on the path, and presents itself to the rest of the dispatcher (which loads the page, etc...).
 *
 * \@package    Core
 *
 * @author     Andrew Embler <andrew@concrete5.org>
 *
 * @category   Concrete
 *
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class RequestBase extends SymfonyRequest
{
    /**
     * @var bool
     */
    protected $hasCustomRequestUser;

    /**
     * @var \Concrete\Core\User\UserInfo
     */
    protected $customRequestUser;

    /**
     * @var string
     */
    protected $customRequestDateTime;

    /**
     * @var SymfonyRequest
     */
    protected static $instance;

    /**
     * @var \Concrete\Core\Page\Page
     */
    protected $c;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = static::createFromGlobals();
        }

        return self::$instance;
    }

    /**
     * @param SymfonyRequest $instance
     */
    public static function setInstance(SymfonyRequest $instance)
    {
        self::$instance = $instance;
    }

    /**
     * @return \Concrete\Core\Page\Page
     */
    public function getCurrentPage()
    {
        return $this->c;
    }

    /**
     * @param \Concrete\Core\Page\Page $c
     */
    public function setCurrentPage(\Concrete\Core\Page\Page $c)
    {
        $this->c = $c;
    }

    public function clearCurrentPage()
    {
        $this->c = null;
    }

    /**
     * @return \Concrete\Core\User\UserInfo
     */
    public function getCustomRequestUser()
    {
        return $this->customRequestUser;
    }

    /**
     * @param \Concrete\Core\User\UserInfo $ui
     */
    public function setCustomRequestUser($ui)
    {
        $this->hasCustomRequestUser = true;
        $this->customRequestUser = $ui;
    }

    /**
     * @return bool
     */
    public function hasCustomRequestUser()
    {
        return $this->hasCustomRequestUser;
    }

    /**
     * @return string
     */
    public function getCustomRequestDateTime()
    {
        return $this->customRequestDateTime;
    }

    /**
     * @param string $date
     */
    public function setCustomRequestDateTime($date)
    {
        $this->customRequestDateTime = $date;
    }

    /**
     * Determines whether a request matches a particular pattern.
     *
     * @param string $pattern
     *
     * @return bool
     */
    public function matches($pattern)
    {
        return Core::make('helper/text')->match($pattern, $this->getPath());
    }

    /**
     * Returns the full path for a request.
     *
     * @return string
     */
    public function getPath()
    {
        $pathInfo = rawurldecode($this->getPathInfo());
        $path = '/' . trim($pathInfo, '/');

        return ($path == '/') ? '' : $path;
    }

    /**
     * If no arguments are passed, returns the post array. If a key is passed, it returns the value as it exists in the post array.
     * If a default value is provided and the key does not exist in the POST array, the default value is returned.
     *
     * @param string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function post($key = null, $defaultValue = null)
    {
        if ($key == null) {
            return $_POST;
        }
        if (isset($_POST[$key])) {
            return (is_string($_POST[$key])) ? trim($_POST[$key]) : $_POST[$key];
        }

        return $defaultValue;
    }

    /**
     * @param string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function request($key = null, $defaultValue = null)
    {
        if ($key == null) {
            return $_REQUEST;
        }
        $req = static::getInstance();
        if ($req->query->has($key)) {
            return $req->query->get($key);
        } else {
            if ($req->request->has($key)) {
                return $req->request->get($key);
            }
        }

        return $defaultValue;
    }

    /**
     * @return bool
     */
    public static function isPost()
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST';
    }
}
