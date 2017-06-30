<?php
namespace Concrete\Core\Marketplace;

use Concrete\Core\Foundation\Object as ConcreteObject;

class RemoteItemSet extends ConcreteObject
{
    public function getMarketplaceRemoteSetName()
    {
        return $this->name;
    }
    public function getMarketplaceRemoteSetID()
    {
        return $this->id;
    }
}
