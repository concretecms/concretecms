<?php

namespace Concrete\Core\Updater\ApplicationUpdate;

class DiagnosticFactory
{

    public static function getFromJSON($json)
    {
        $o = json_decode($json);

        $diagnostic = new Diagnostic();
        if ($o->requested_version) {
            $version = new Version();
            $version->setVersion($o->requested_version->version);
            $version->setReleaseNotes($o->requested_version->notes);
            $version->setReleaseNotesURL($o->requested_version->notes_url);
            $diagnostic->setRequestedVersion($version);
        }

        if ($o->marketplace_item_status) {
            foreach($o->marketplace_item_status as $status) {
                $s = new MarketplaceItemStatus();
                $s->setMarketplaceItemHandle($status->mpHandle);
                $s->setMarketplaceItemID($status->mpID);
                $s->setSafety($status->safety);
                $s->setStatus($status->status);
                $diagnostic->addMarketplaceItemStatusObject($s);
            }
        }

        if ($o->notices) {
            foreach($o->notices as $status) {
                $s = new Status();
                $s->setSafety($status->safety);
                $s->setStatus($status->status);
                $diagnostic->addNoticeStatusObject($s);
            }
        }

        if ($o->status) {
            $s = new Status();
            $s->setSafety($o->status->safety);
            $s->setStatus($o->status->status);
            $diagnostic->setUpdateStatusObject($s);
        }

        return $diagnostic;
    }

}
