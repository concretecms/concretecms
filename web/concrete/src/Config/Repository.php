<?php
namespace Concrete\Core\Config;

class Repository extends \Illuminate\Config\Repository
{

    public function clearCache()
    {
        $this->items = array();
    }

}
