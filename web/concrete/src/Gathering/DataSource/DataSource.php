<?php
namespace Concrete\Core\Gathering\DataSource;

use Loader;
use Core;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Gathering\DataSource\Configuration\Configuration as GatheringDataSourceConfiguration;
use Concrete\Core\Package\PackageList;

abstract class DataSource extends Object
{
    abstract public function createConfigurationObject(Gathering $ga, $post);
    abstract public function createGatheringItems(GatheringDataSourceConfiguration $configuration);

    protected $optionFormKey = '_gas_';

    public function configure(Gathering $ga, $post)
    {
        $db = Loader::db();
        $o = $this->createConfigurationObject($ga, $post);
        $r = $db->Execute('insert into GatheringConfiguredDataSources (gaID, gasID, gcdObject) values (?, ?, ?)', array(
            $ga->getGatheringID(), $this->gasID, serialize($o),
        ));

        return GatheringDataSourceConfiguration::getByID($db->Insert_ID());
    }

    public static function getByID($gasID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select gasID, gasHandle, pkgID, gasName from GatheringDataSources where gasID = ?', array($gasID));
        if (isset($row['gasID'])) {
            $txt = Loader::helper('text');
            $className = '\\Concrete\\Core\\Gathering\\DataSource\\' . $txt->camelcase($row['gasHandle']) . 'DataSource';
            $gas = Core::make($className);
            $gas->setPropertiesFromArray($row);

            return $gas;
        }
    }

    public function setOptionFormKey($key)
    {
        $this->optionFormKey = $key;
    }

    public function optionFormKey($name)
    {
        return 'gas[' . $this->optionFormKey . '][' . $name . ']';
    }

    public function getOptionFormRequestData()
    {
        return $_REQUEST['gas'][$this->optionFormKey];
    }

    public static function getByHandle($gasHandle)
    {
        $db = Loader::db();
        $row = $db->GetRow('select gasID, gasHandle, pkgID, gasName from GatheringDataSources where gasHandle = ?', array($gasHandle));
        if (isset($row['gasID'])) {
            //todo: hard coded class path to get indexing working for testing, needs to be made more dynamic
            $class = '\\Concrete\\Core\\Gathering\\DataSource\\' . Loader::helper('text')->camelcase($row['gasHandle']) . 'DataSource';
            $gas = new $class();
            $gas->setPropertiesFromArray($row);

            return $gas;
        }
    }

    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $list = array();
        $r = $db->Execute('select gasID from GatheringDataSources where pkgID = ? order by gasID asc', array($pkg->getPackageID()));
        while ($row = $r->FetchRow()) {
            $gas = static::getByID($row['gasID']);
            if (is_object($gas)) {
                $list[] = $gas;
            }
        }
        $r->Close();

        return $list;
    }

    public static function getList()
    {
        $db = Loader::db();
        $list = array();
        $r = $db->Execute('select gasID from GatheringDataSources order by gasDisplayOrder asc');
        while ($row = $r->FetchRow()) {
            $gas = static::getByID($row['gasID']);
            if (is_object($gas)) {
                $list[] = $gas;
            }
        }
        $r->Close();

        return $list;
    }

    public function getGatheringDataSourceID()
    {
        return $this->gasID;
    }
    public function getGatheringDataSourceHandle()
    {
        return $this->gasHandle;
    }
    public function getGatheringDataSourceName()
    {
        return $this->gasName;
    }
    public function getPackageID()
    {
        return $this->pkgID;
    }
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }
    public function getGatheringDataSourceOptionsForm()
    {
        $env = Environment::get();
        $file = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_GATHERING . '/' . DIRNAME_GATHERING_DATA_SOURCES . '/' . $this->gasHandle . '/' . FILENAME_GATHERING_DATA_SOURCE_OPTIONS, $this->getPackageHandle());

        return $file;
    }

    public function updateGatheringDataSourceName($gasName)
    {
        $this->gasName = $gasName;
        $db = Loader::db();
        $db->Execute("update GatheringDataSources set gasName = ? where gasID = ?", array($gasName, $this->gasID));
    }

    public function updateGatheringDataSourceHandle($gasHandle)
    {
        $this->gasHandle = $gasHandle;
        $db = Loader::db();
        $db->Execute("update GatheringDataSources set gasHandle = ? where gasID = ?", array($gasHandle, $this->gasID));
    }

    public static function add($gasHandle, $gasName, $pkg = false)
    {
        $db = Loader::db();
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $sources = $db->GetOne('select count(gasID) from GatheringDataSources');
        $gasDisplayOrder = 0;
        if ($sources > 0) {
            $gasDisplayOrder = $db->GetOne('select max(gasDisplayOrder) from GatheringDataSources');
            ++$gasDisplayOrder;
        }

        $db->Execute('insert into GatheringDataSources (gasHandle, gasName, gasDisplayOrder, pkgID) values (?, ?, ?, ?)', array($gasHandle, $gasName, $gasDisplayOrder, $pkgID));
        $id = $db->Insert_ID();

        $gas = static::getByID($id);

        return $gas;
    }

    public function export($axml)
    {
        $gas = $axml->addChild('gatheringsource');
        $gas->addAttribute('handle', $this->getGatheringDataSourceHandle());
        $gas->addAttribute('name', $this->getGatheringDataSourceName());
        $gas->addAttribute('package', $this->getPackageHandle());

        return $gas;
    }

    public static function exportList($xml)
    {
        $axml = $xml->addChild('gatheringsources');
        $db = Loader::db();
        $r = $db->Execute('select gasID from GatheringDataSources order by gasID asc');
        $list = array();
        while ($row = $r->FetchRow()) {
            $gas = static::getByID($row['gasID']);
            if (is_object($gas)) {
                $list[] = $gas;
            }
        }
        foreach ($list as $gas) {
            $gas->export($axml);
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from GatheringDataSources where gasID = ?', array($this->gasID));
    }
}
