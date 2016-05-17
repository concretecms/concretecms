<?php
namespace Concrete\Core\Gathering\DataSource\Configuration;

use Loader;
use Concrete\Core\Foundation\Object;

class Configuration extends Object
{
    protected $dataSource;

    public static function getByID($gcsID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select gcsID, gasID, gaID, gcdObject from GatheringConfiguredDataSources where gcsID = ?', array($gcsID));
        if (isset($row['gcsID'])) {
            $source = GatheringDataSource::getByID($row['gasID']);
            $o = @unserialize($row['gcdObject']);
            if (is_object($o)) {
                unset($row['gcdObject']);
                $o->setPropertiesFromArray($row);
                $o->dataSource = GatheringDataSource::getByID($row['gasID']);

                return $o;
            }
        }
    }

    public function duplicate(Gathering $gathering)
    {
        $db = Loader::db();
        $gasID = $this->getGatheringDataSourceID();
        // unset the items we don't want in our serialized object
        $this->dataSource = null;
        unset($this->gaID);
        unset($this->gcsID);
        unset($this->gasID);
        $gcdObject = serialize($this);
        $db->Execute('insert into GatheringConfiguredDataSources (gasID, gaID, gcdObject) values (?, ?, ?)', array(
            $gasID,
            $gathering->getGatheringID(),
            $gcdObject,
        ));
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->dataSource, $method), $args);
    }

    public function getGatheringDataSourceObject()
    {
        return $this->dataSource;
    }

    public function getGatheringObject()
    {
        $gathering = Gathering::getByID($this->gaID);

        return $gathering;
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from GatheringConfiguredDataSources where gcsID = ?', array($this->gcsID));
    }
}
