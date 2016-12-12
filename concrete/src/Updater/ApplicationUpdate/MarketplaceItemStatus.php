<?php
namespace Concrete\Core\Updater\ApplicationUpdate;

class MarketplaceItemStatus extends Status
{
    protected $marketplaceItemID;
    protected $marketplaceItemHandle;

    /**
     * @return mixed
     */
    public function getMarketplaceItemID()
    {
        return $this->marketplaceItemID;
    }

    /**
     * @param mixed $marketplaceItemID
     */
    public function setMarketplaceItemID($marketplaceItemID)
    {
        $this->marketplaceItemID = $marketplaceItemID;
    }

    /**
     * @return mixed
     */
    public function getMarketplaceItemHandle()
    {
        return $this->marketplaceItemHandle;
    }

    /**
     * @param mixed $marketplaceItemHandle
     */
    public function setMarketplaceItemHandle($marketplaceItemHandle)
    {
        $this->marketplaceItemHandle = $marketplaceItemHandle;
    }

    public function getJSONObject()
    {
        $o = parent::getJSONObject();
        $o->mpID = $this->getMarketplaceItemID();
        $o->mpHandle = $this->getMarketplaceItemHandle();

        return $o;
    }
}
