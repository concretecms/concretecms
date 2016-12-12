<?php
namespace Concrete\Core\Area;

use Loader;
use Page;
use Permissions;
use Stack;

class GlobalArea extends Area
{

    protected $ignoreCurrentMultilingualLanguageSection = false;

    /**
     * @return bool
     */
    public function isGlobalArea()
    {
        return true;
    }

    /**
     * If called on a multilingual website, this global area will not load its content from the language-specific global area stack. Instead, it'll use
     * the stack in the default language, throughout the website.
     */
    public function ignoreCurrentLanguageSection()
    {
        $this->ignoreCurrentMultilingualLanguageSection = true;
    }

    /**
     * @param Page $c
     * @param string $arHandle
     * @return Area
     */
    public function create($c, $arHandle)
    {
        $db = Loader::db();
        Stack::getOrCreateGlobalArea($arHandle);
        $db->Replace('Areas', array('cID' => $c->getCollectionID(), 'arHandle' => $arHandle, 'arIsGlobal' => 1), array('arHandle', 'cID'), true);
        $this->refreshCache($c);
        $area = self::get($c, $arHandle);
        $area->rescanAreaPermissionsChain();
        return $area;
    }

    /**
     * @return string
     */
    public function getAreaDisplayName()
    {
        return t('Sitewide %s', $this->getAreaHandle());
    }

    /**
     * @param Page $c
     *
     * @return int
     */
    public function getTotalBlocksInArea($c = false)
    {
        $stack = $this->getGlobalAreaStackObject($c);
        $ax = Area::get($stack, STACKS_AREA_NAME);
        if (is_object($ax)) {
            return $ax->getTotalBlocksInArea();
        }
        return 0;
    }

    /**
     * @param Page $c
     *
     * @return Page
     */
    protected function getGlobalAreaStackObject($c = false)
    {
        if (!$c) {
            $c = Page::getCurrentPage();
        }
        $cp = new Permissions($c);
        $contentSource = Stack::MULTILINGUAL_CONTENT_SOURCE_CURRENT;
        if ($this->ignoreCurrentMultilingualLanguageSection) {
            $contentSource = Stack::MULTILINGUAL_CONTENT_SOURCE_DEFAULT;
        }
        if ($cp->canViewPageVersions()) {
            $stack = Stack::getByName($this->arHandle, 'RECENT', $contentSource);
        } else {
            $stack = Stack::getByName($this->arHandle, 'ACTIVE', $contentSource);
        }
        return $stack;
    }

    /**
     * @return int
     */
    public function getTotalBlocksInAreaEditMode()
    {
        $stack = $this->getGlobalAreaStackObject();
        $ax = Area::get($stack, STACKS_AREA_NAME);

        $db = Loader::db();
        $r = $db->GetOne('select count(b.bID) from CollectionVersionBlocks cvb inner join Blocks b on cvb.bID = b.bID inner join BlockTypes bt on b.btID = bt.btID where cID = ? and cvID = ? and arHandle = ?',
            array($stack->getCollectionID(), $stack->getVersionID(), $ax->getAreaHandle())
        );
        return $r;
    }

    /**
     * @return \Block[]
     */
    public function getAreaBlocks()
    {
        $cp = new Permissions($this->c);
        $contentSource = Stack::MULTILINGUAL_CONTENT_SOURCE_CURRENT;
        if ($this->ignoreCurrentMultilingualLanguageSection) {
            $contentSource = Stack::MULTILINGUAL_CONTENT_SOURCE_DEFAULT;
        }
        if ($cp->canViewPageVersions()) {
            $stack = Stack::getByName($this->arHandle, 'RECENT', $contentSource);
        } else {
            $stack = Stack::getByName($this->arHandle, 'ACTIVE', $contentSource);
        }
        $blocksTmp = array();
        if (is_object($stack)) {
            $blocksTmp = $stack->getBlocks(STACKS_AREA_NAME);
            $globalArea = self::get($stack, STACKS_AREA_NAME);
        }

        $blocks = array();
        foreach ($blocksTmp as $ab) {
            $ab->setBlockAreaObject($globalArea);
            $ab->setBlockActionCollectionID($stack->getCollectionID());
            $blocks[] = $ab;
        }

        unset($blocksTmp);
        return $blocks;
    }

    public function display($c = false, $fake = null)
    {
        parent::display($c, null);
    }

    /**
     * Note that this function does not delete the global area's stack.
     * You probably want to call the "delete" method of the Stack model instead.
     * @param string $arHandle
     */
    public static function deleteByName($arHandle)
    {
        $db = Loader::db();
        $db->Execute('select cID from Areas where arHandle = ? and arIsGlobal = 1', array($arHandle));
        $db->Execute('delete from Areas where arHandle = ? and arIsGlobal = 1', array($arHandle));
    }

}