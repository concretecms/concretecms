<?php
namespace Concrete\Core\Page\Collection\Version;

class Event extends \Concrete\Core\Page\Event
{
    public function setCollectionVersionObject($version)
    {
        $this->version = $version;
    }

    public function getCollectionVersionObject()
    {
        return $this->version;
    }
}
