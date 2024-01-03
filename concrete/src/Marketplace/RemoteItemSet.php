<?php
namespace Concrete\Core\Marketplace;

use Concrete\Core\Foundation\ConcreteObject;

/**
 * @Deprecated Will be removed in v8
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
