<?php
namespace Concrete\Core\Page\Stack;

use Area;
use GlobalArea;
use CacheLocal;
use Config;
use Loader;
use Page;
use PageType;

/**
 * Class Stack
 *
 * @package Concrete\Core\Page\Stack
 */
class Stack extends Page
{

    const ST_TYPE_USER_ADDED = 0;
    const ST_TYPE_GLOBAL_AREA = 20;

    /**
     * @param string $type
     * @return int
     */
    public static function mapImportTextToType($type)
    {
        switch ($type) {
            case 'global_area':
                return static::ST_TYPE_GLOBAL_AREA;
                break;
            default:
                return static::ST_TYPE_USER_ADDED;
                break;
        }
    }

    /**
     * @param $stackName
     * @return Stack
     */
    public static function getOrCreateGlobalArea($stackName)
    {
        $stack = static::getByName($stackName);
        if (!$stack) {
            $stack = static::addStack($stackName, static::ST_TYPE_GLOBAL_AREA);
        }
        return $stack;
    }

    /**
     * @param string $stackName
     * @param string $cvID
     * @return Page
     */
    public static function getByName($stackName, $cvID = 'RECENT')
    {
        $cID = CacheLocal::getEntry('stack_by_name', $stackName);
        if (!$cID) {
            $db = Loader::db();
            $cID = $db->GetOne('select cID from Stacks where stName = ?', array($stackName));
            CacheLocal::set('stack_by_name', $stackName, $cID);
        }

        if ($cID) {
            return static::getByID($cID, $cvID);
        }
    }

    /**
     * @param int    $cID
     * @param string $cvID
     * @return bool|Page
     */
    public static function getByID($cID, $cvID = 'RECENT')
    {
        $db = Loader::db();
        $c = parent::getByID($cID, $cvID, 'Stack');

        if (static::isValidStack($c)) {
            return $c;
        }
        return false;
    }

    /**
     * @param Stack $stack
     * @return bool
     */
    protected static function isValidStack($stack)
    {
        return $stack->getPageTypeHandle() == STACKS_PAGE_TYPE;
    }

    /**
     * @param string $stackName
     * @param int    $type
     * @return Page
     */
    public static function addStack($stackName, $type = 0)
    {
        $ct = new PageType();
        $data = array();

        $parent = Page::getByPath(STACKS_PAGE_PATH);
        $data = array();
        $data['name'] = $stackName;
        if (!$stackName) {
            $data['name'] = t('No Name');
        }
        $pagetype = PageType::getByHandle(STACKS_PAGE_TYPE);
        $page = $parent->add($pagetype, $data);

        // we have to do this because we need the area to exist before we try and add something to it.
        $a = Area::getOrCreate($page, STACKS_AREA_NAME);

        // finally we add the row to the stacks table
        $db = Loader::db();
        $stackCID = $page->getCollectionID();
        $v = array($stackName, $stackCID, $type);
        $db->Execute('insert into Stacks (stName, cID, stType) values (?, ?, ?)', $v);

        //Return the new stack
        return static::getByID($stackCID);
    }

    /**
     * @param |\Concrete\Core\Page\Collection $nc
     * @param bool $preserveUserID
     * @return Stack
     */
    public function duplicate($nc = null, $preserveUserID = false)
    {
        if (!is_object($nc)) {
            // There is not necessarily need to provide the parent
            // page for the duplicate since for stacks, that is
            // always the same page.
            $nc = Page::getByPath(STACKS_PAGE_PATH);
        }
        $page = parent::duplicate($nc, $preserveUserID);

        // we have to do this because we need the area to exist before we try and add something to it.
        $a = Area::getOrCreate($page, STACKS_AREA_NAME);

        $db = Loader::db();
        $v = array($page->getCollectionName(), $page->getCollectionID(), $this->getStackType());
        $db->Execute('insert into Stacks (stName, cID, stType) values (?, ?, ?)', $v);

        // Make sure we return an up-to-date record
        return static::getByID($page->getCollectionID());
    }

    /**
     * @return int
     */
    public function getStackType()
    {
        $db = Loader::db();
        return $db->GetOne('select stType from Stacks where cID = ?', array($this->getCollectionID()));
    }

    /**
     * @param $data
     * @return bool
     */
    public function update($data)
    {
        if (isset($data['stackName'])) {
            $txt = Loader::helper('text');
            $data['cName'] = $data['stackName'];
            $data['cHandle'] = str_replace('-', Config::get('concrete.seo.page_path_separator'), $txt->urlify($data['stackName']));
        }
        $worked = parent::update($data);

        if (isset($data['stackName'])) {
            // Make sure the stack path is always up-to-date after a name change
            $this->rescanCollectionPath();

            $db = Loader::db();
            $stackName = $data['stackName'];
            $db->Execute('update Stacks set stName = ? WHERE cID = ?', array($stackName, $this->getCollectionID()));
        }
        return $worked;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if ($this->getStackType() == static::ST_TYPE_GLOBAL_AREA) {
            GlobalArea::deleteByName($this->getStackName());
        }

        parent::delete();
        $db = Loader::db();
        return $db->Execute('delete from Stacks where cID = ?', array($this->getCollectionID()));
    }

    /**
     * @return string
     */
    public function getStackName()
    {
        $db = Loader::db();
        return $db->GetOne('select stName from Stacks where cID = ?', array($this->getCollectionID()));
    }

    /**
     * @return bool
     */
    public function display()
    {
        $ax = Area::get($this, STACKS_AREA_NAME);
        $ax->disableControls();
        $ax->display($this);
        return true;
    }

    /**
     * @param Page $pageNode
     */
    public function export($pageNode)
    {

        $p = $pageNode->addChild('stack');
        $p->addAttribute('name', Loader::helper('text')->entities($this->getCollectionName()));
        if ($this->getStackTypeExportText()) {
            $p->addAttribute('type', $this->getStackTypeExportText());
        }

        $db = Loader::db();
        // you shouldn't ever have a sub area in a stack but just in case.
        $r = $db->Execute('select arHandle from Areas where cID = ? and arParentID = 0', array($this->getCollectionID()));
        while ($row = $r->FetchRow()) {
            $ax = Area::get($this, $row['arHandle']);
            $ax->export($p, $this);
        }
    }

    /**
     * @return bool|string
     */
    public function getStackTypeExportText()
    {
        switch ($this->getStackType()) {
            case static::ST_TYPE_GLOBAL_AREA:
                return 'global_area';
                break;
            default:
                return false;
                break;
        }
    }

}
