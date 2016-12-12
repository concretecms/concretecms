<?php

namespace Concrete\Core\Updater;

class RemoteApplicationUpdateFactory
{

    public static function getFromJSON($json)
    {
        $o = json_decode($json);

        $update = new RemoteApplicationUpdate();
        $update->setVersion($o->version);
        $update->setIdentifier($o->identifier);
        $update->setNotes($o->notes);
        $update->setDate($o->date);
        $update->setDirectDownloadURL($o->direct_download_url);

        return $update;
    }
}
