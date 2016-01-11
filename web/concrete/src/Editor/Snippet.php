<?php
namespace Concrete\Core\Editor;

use Loader;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Package\PackageList;
use Core;
use Package;

abstract class Snippet extends Object
{
    /**
     * Required for snippets to work.
     */
    abstract public function replace();

    public function getSystemContentEditorSnippetHandle()
    {
        return $this->scsHandle;
    }
    public function getSystemContentEditorSnippetName()
    {
        return $this->scsName;
    }
    public function isSystemContentEditorSnippetActive()
    {
        return $this->scsIsActive;
    }
    public function getPackageID()
    {
        return $this->pkgID;
    }
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }
    public function getPackageObject()
    {
        return Package::getByID($this->pkgID);
    }

    public function findAndReplace($text)
    {
        $r = preg_replace_callback('/\<span[^>]*data-scsHandle="' . $this->scsHandle . '">[^<]*<\/span>/is', array($this, 'replace'), $text);

        return $r;
    }

    public static function getByHandle($scsHandle)
    {
        $db = Loader::db();
        $r = $db->GetRow('select scsHandle, scsIsActive, pkgID, scsName from SystemContentEditorSnippets where scsHandle = ?', array($scsHandle));
        if (is_array($r) && $r['scsHandle']) {
            $pkgHandle = false;
            if ($r['pkgID']) {
                $pkgHandle = PackageList::getHandle($r['pkgID']);
            }

            $class = overrideable_core_class(
                'Core\\Editor\\' . camelcase($r['scsHandle']) . 'Snippet',
                DIRNAME_CLASSES . '/Editor/' . camelcase($r['scsHandle']) . 'Snippet.php',
                $pkgHandle
            );

            $sc = Core::make($class);
            $sc->setPropertiesFromArray($r);

            return $sc;
        }
    }

    public static function add($scsHandle, $scsName, $pkg = false)
    {
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $db = Loader::db();
        $db->Execute('insert into SystemContentEditorSnippets (scsHandle, scsName, pkgID) values (?, ?, ?)', array($scsHandle, $scsName, $pkgID));

        return static::getByHandle($scsHandle);
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from SystemContentEditorSnippets where scsHandle = ?', array($this->scsHandle));
    }

    public function activate()
    {
        $db = Loader::db();
        $db->Execute('update SystemContentEditorSnippets set scsIsActive = 1 where scsHandle = ?', array($this->scsHandle));
    }

    public function deactivate()
    {
        $db = Loader::db();
        $db->Execute('update SystemContentEditorSnippets set scsIsActive = 0 where scsHandle = ?', array($this->scsHandle));
    }

    public static function getList()
    {
        $db = Loader::db();
        $scsHandles = $db->GetCol('select scsHandle from SystemContentEditorSnippets order by scsHandle asc');
        $libraries = array();
        foreach ($scsHandles as $scsHandle) {
            $scs = static::getByHandle($scsHandle);
            $libraries[] = $scs;
        }

        return $libraries;
    }

    public static function getActiveList()
    {
        $db = Loader::db();
        $scsHandles = $db->GetCol('select scsHandle from SystemContentEditorSnippets where scsIsActive = 1 order by scsHandle asc');
        $libraries = array();
        foreach ($scsHandles as $scsHandle) {
            $scs = static::getByHandle($scsHandle);
            $libraries[] = $scs;
        }

        return $libraries;
    }

    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $scsHandles = $db->GetCol('select scsHandle from SystemContentEditorSnippets where pkgID = ? order by scsHandle asc', array($pkg->getPackageID()));
        $libraries = array();
        foreach ($scsHandles as $scsHandle) {
            $scs = static::getByHandle($scsHandle);
            $libraries[] = $scs;
        }

        return $libraries;
    }

    public static function exportList($xml)
    {
        $list = self::getList();
        $nxml = $xml->addChild('systemcontenteditorsnippets');
        foreach ($list as $sc) {
            $activated = 0;
            $type = $nxml->addChild('snippet');
            $type->addAttribute('handle', $sc->getSystemContentEditorSnippetHandle());
            $type->addAttribute('name', $sc->getSystemContentEditorSnippetName());
            $type->addAttribute('package', $sc->getPackageHandle());
            $type->addAttribute('activated', $sc->isSystemContentEditorSnippetActive());
        }
    }
}
