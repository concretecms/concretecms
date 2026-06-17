<?php
namespace Concrete\Core\Marketplace;

use Concrete\Core\Foundation\ConcreteObject;

/**
 * @deprecated This will be removed in version 10
 * @see PackageRepositoryInterface::getPackages()
 */
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
