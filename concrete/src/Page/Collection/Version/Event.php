<?php
namespace Concrete\Core\Page\Collection\Version;

class Event extends \Concrete\Core\Page\Event
{
    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var \Concrete\Core\Page\Collection\Version\Version
     */
    public $version;

    public function setCollectionVersionObject($version)
    {
        $this->version = $version;
    }

    public function getCollectionVersionObject()
    {
        return $this->version;
    }
}
