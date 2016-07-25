<?php
namespace Concrete\Core\Search;

use Session;
use Request;

class StickyRequest
{
    protected $namespace;

    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    protected function getStickySearchNameSpace()
    {
        return 'search/' . $this->namespace;
    }

    public function resetSearchRequest($namespace = '')
    {
        Session::set($this->getStickySearchNameSpace(), array());
    }

    public function addToSearchRequest($key, $value)
    {
        $data = Session::get($this->getStickySearchNameSpace());
        if (!is_array($data)) {
            $data = array();
        }
        $data[$key] = $value;
        Session::set($this->getStickySearchNameSpace(), $data);
    }

    public function getSearchRequest()
    {
        $data = Session::get($this->getStickySearchNameSpace());
        if (!is_array($data)) {
            $data = array();
        }

        foreach (Request::getInstance()->query->all() as $key => $value) {
            $data[$key] = $value;
        }
        foreach (Request::getInstance()->request->all() as $key => $value) {
            $data[$key] = $value;
        }

        Session::set($this->getStickySearchNameSpace(), $data);

        return $data;
    }
}
