<?php

namespace Concrete\Core\Updater\ApplicationUpdate;

class Diagnostic
{

    protected $requestedVersion;
    protected $marketplaceItemStatuses = array();
    protected $notices = array();
    protected $status;

    /**
     * @return mixed
     */
    public function getRequestedVersion()
    {
        return $this->requestedVersion;
    }

    /**
     * @param mixed $requestedVersion
     */
    public function setRequestedVersion(Version $requestedVersion)
    {
        $this->requestedVersion = $requestedVersion;
    }

    public function addMarketplaceItemStatusObject(MarketplaceItemStatus $s)
    {
        $this->marketplaceItemStatuses[] = $s;
    }

    public function addNoticeStatusObject(Status $s)
    {
        $this->notices[] = $s;
    }

    public function setUpdateStatusObject(Status $status)
    {
        $this->status = $status;
    }

    public function getJSONObject()
    {
        $o = new \stdClass();
        if ($this->requestedVersion) {
            $o->requestedVersion = $this->requestedVersion->getVersion();
            $o->releaseNotes = $this->requestedVersion->getReleaseNotes();
            $o->releaseNotesUrl = $this->requestedVersion->getReleaseNotesURL();
        }
        $o->marketplaceItemStatuses = array();
        foreach($this->marketplaceItemStatuses as $s) {
            $o->marketplaceItemStatuses[] = $s->getJSONObject();
        }
        $o->notices = array();
        foreach($this->notices as $s) {
            $o->notices[] = $s->getJSONObject();
        }
        if ($this->status) {
            $o->status = $this->status->getJSONObject();
        }
        return $o;
    }

}
