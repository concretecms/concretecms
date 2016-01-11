<?php
namespace Concrete\Core\Gathering\DataSource\Configuration;

class PageConfiguration extends Configuration
{
    protected $ptID;

    public function setPageTypeID($ptID)
    {
        $this->ptID = $ptID;
    }

    public function getPageTypeID()
    {
        return $this->ptID;
    }
}
