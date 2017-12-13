<?php

namespace Concrete\Core\Area;

use Concrete\Core\Multilingual\Page\Section\Section as MultilingualSection;
use Concrete\Core\Page\Collection\Version\VersionList;
use Concrete\Core\Page\Stack\StackList;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Support\Facade\Application;
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
     *
     * @return Area
     */
    public function create($c, $arHandle)
    {
        $db = Loader::db();
        Stack::getOrCreateGlobalArea($arHandle);
        $db->Replace('Areas', ['cID' => $c->getCollectionID(), 'arHandle' => $arHandle, 'arIsGlobal' => 1], ['arHandle', 'cID'], true);
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
        return t('Sitewide %s', parent::getAreaDisplayName());
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
     * @return int
     */
    public function getTotalBlocksInAreaEditMode()
    {
        $stack = $this->getGlobalAreaStackObject();
        $ax = Area::get($stack, STACKS_AREA_NAME);

        $db = Loader::db();

        return (int) $db->GetOne('select count(b.bID) from CollectionVersionBlocks cvb inner join Blocks b on cvb.bID = b.bID inner join BlockTypes bt on b.btID = bt.btID where cID = ? and cvID = ? and arHandle = ?',
            [$stack->getCollectionID(), $stack->getVersionID(), $ax->getAreaHandle()]
        );
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
            $stack = Stack::getByName($this->arHandle, 'RECENT', null, $contentSource);
        } else {
            $stack = Stack::getByName($this->arHandle, 'ACTIVE', null, $contentSource);
        }
        $blocksTmp = [];
        if (is_object($stack)) {
            $blocksTmp = $stack->getBlocks(STACKS_AREA_NAME);
            $globalArea = self::get($stack, STACKS_AREA_NAME);
        }

        $blocks = [];
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
     *
     * @param string $arHandle
     */
    public static function deleteByName($arHandle)
    {
        $db = Loader::db();
        $db->Execute('select cID from Areas where arHandle = ? and arIsGlobal = 1', [$arHandle]);
        $db->Execute('delete from Areas where arHandle = ? and arIsGlobal = 1', [$arHandle]);
    }

    /**
     * Searches for global areas without any blocks in it and deletes them.
     * This will have a positive impact on the performance as every global area is rendered for every page.
     */
    public static function deleteEmptyAreas()
    {
        $app = Application::getFacadeApplication();
        $siteService = $app->make(SiteService::class);
        $multilingualSectionIDs = [];
        $sites = $siteService->getList();
        foreach ($sites as $site) {
            $multilingualSectionIDs = array_merge(MultilingualSection::getIDList($site));
        }
        $multilingualSections = [];
        foreach (array_unique($multilingualSectionIDs) as $multilingualSectionID) {
            $multilingualSections[] = MultilingualSection::getByID($multilingualSectionID);
        }

        $stackList = new StackList();
        $stackList->filterByGlobalAreas();

        /** @var \Concrete\Core\Page\Stack\Stack[] $globalAreaStacks */
        $globalAreaStacks = $stackList->getResults();

        foreach ($globalAreaStacks as $stack) {
            $stackAlternatives = [];
            if ($stack->isNeutralStack()) {
                $stackAlternatives[] = $stack;
            } else {
                $stackAlternatives[] = $stack->getNeutralStack();
            }
            foreach ($multilingualSections as $multilingualSection) {
                $stackAlternative = $stackAlternatives[0]->getLocalizedStack($multilingualSection);
                if ($stackAlternative !== null) {
                    $stackAlternatives[] = $stackAlternative;
                }
            }
            $hasBlocks = false;
            foreach ($stackAlternatives as $stackAlternative) {
                // get list of all available page versions, we only delete areas if they never had any content
                $versionList = new VersionList($stackAlternative);
                $versions = $versionList->get();

                foreach ($versions as $version) {
                    $pageVersion = Page::getByID($version->getCollectionID(), $version->getVersionID());
                    $totalBlocks = count($pageVersion->getBlockIDs());

                    if ($totalBlocks > 0) {
                        $hasBlocks = true;
                        break 2;
                    }
                }
            }

            if (!$hasBlocks) {
                $stackAlternatives[0]->delete();
            }
        }
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
            $stack = Stack::getByName($this->arHandle, 'RECENT', null, $contentSource);
        } else {
            $stack = Stack::getByName($this->arHandle, 'ACTIVE', null, $contentSource);
        }

        return $stack;
    }
}
