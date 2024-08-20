<?php
namespace Concrete\Core\Page\Stack;

use Concrete\Core\Area\Area;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Stack\Folder\Folder;
use Concrete\Core\Site\Tree\TreeInterface;
use Doctrine\DBAL\Connection;
use GlobalArea;
use Config;
use Database;
use Core;
use Concrete\Core\Page\Page;
use PageType;
use Concrete\Core\Entity\Site\Site;

/**
 * Class Stack.
 *
 * \@package Concrete\Core\Page\Stack
 */
class Stack extends Page
{
    const ST_TYPE_USER_ADDED = 0;
    const ST_TYPE_GLOBAL_AREA = 20;

    const MULTILINGUAL_CONTENT_SOURCE_CURRENT = 100; // in multilingual sites, loads based on current page's locale
    const MULTILINGUAL_CONTENT_SOURCE_DEFAULT = 200; // in multilingual sites, loads based on default locale (ignores current)

    /**
     * @param string $type
     *
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
     * @param string $path
     * @param string $version
     * \Concrete\Core\Site\Tree\TreeInterface|null $siteTree
     *
     * @return bool|\Concrete\Core\Page\Page
     */
    public static function getByPath($path, $version = 'RECENT', ?TreeInterface $siteTree = null)
    {
        $c = parent::getByPath(STACKS_PAGE_PATH . '/' . trim($path, '/'), $version, $siteTree);
        if (static::isValidStack($c)) {
            return $c;
        }

        return false;
    }

    /**
     * @param string $stackName
     *
     * @return self
     */
    public static function getOrCreateGlobalArea($stackName)
    {
        $stack = static::getByName($stackName);
        if (!$stack) {
            $stack = static::addGlobalArea($stackName);
        }

        return $stack;
    }

    /**
     * @param string $stackName
     * @param string $cvID
     * \Concrete\Core\Site\Tree\TreeInterface|null $site
     * @param int $multilingualContentSource
     *
     * @return self|false|null
     */
    public static function getByName($stackName, $cvID = 'RECENT', ?TreeInterface $site = null, $multilingualContentSource = self::MULTILINGUAL_CONTENT_SOURCE_CURRENT)
    {
        $c = Page::getCurrentPage();
        if (is_object($c) && (!$c->isError())) {
            $identifier = sprintf('/stack/name/%s/%s/%s/%s', $stackName, $c->getCollectionID(), $cvID, $multilingualContentSource);
            $cache = Core::make('cache/request');
            $item = $cache->getItem($identifier);
            if (!$item->isMiss()) {
                $cID = $item->get();
            } else {
                $item->lock();
                $db = Database::connection();
                $ms = false;
                $detector = Core::make('multilingual/detector');
                if ($detector->isEnabled()) {
                    $ms = self::getMultilingualSectionFromType($multilingualContentSource);
                }
                $sql = 'select cID from Stacks where stName = ?';
                $q = [$stackName];
                if ($ms) {
                    $sql .= ' and (stMultilingualSection = ? or stMultilingualSection = 0)';
                    $q[] = $ms->getCollectionID();
                } else {
                    $sql .= ' and stMultilingualSection = 0';
                }
                //$sql .= ' and siteTreeID = ?';
                if ($ms) {
                    $sql .= ' order by stMultilingualSection desc';
                }
                $sql .= ' limit 1';
                /*if (!is_object($site)) {
                    $site = \Core::make('site')->getSite();
                }
                if ($site instanceof Site) {
                    $q[] = $site->getDefaultLocale()->getSiteTree()->getSiteTreeID();
                } else {
                    $q[] = $site->getSiteTreeID();
                }*/
                $cID = $db->fetchColumn($sql, $q);
                $cache->save($item->set($cID));
            }
        } else {
            $db = Database::connection();
            $cID = $db->fetchColumn(
                'select cID from Stacks where stName = ? and stMultilingualSection = 0',
                [$stackName]
            );
        }

        return $cID ? static::getByID($cID, $cvID) : null;
    }

    /**
     * @param int    $cID
     * @param string $cvID
     *
     * @return \Concrete\Core\Page\Page|self|false
     */
    public static function getByID($cID, $cvID = 'RECENT')
    {
        $c = parent::getByID($cID, $cvID);

        if (static::isValidStack($c)) {
            return $c;
        }

        return null;
    }

    /**
     * @param \Concrete\Core\Page\Stack\Stack $stack
     *
     * @return bool
     */
    protected static function isValidStack($stack)
    {
        return $stack->getPageTypeHandle() == STACKS_PAGE_TYPE;
    }

    /**
     * @param \Concrete\Core\Page\Page $parent
     * @param $name
     * @param int $type
     *
     * @return self|false
     */
    private static function addStackToCategory(\Concrete\Core\Page\Page $parent, $name, $type = 0)
    {
        $data = [];
        $data['name'] = $name;
        if (!$name) {
            $data['name'] = t('No Name');
        }
        $pagetype = PageType::getByHandle(STACKS_PAGE_TYPE);
        $page = $parent->add($pagetype, $data);

        // we have to do this because we need the area to exist before we try and add something to it.
        Area::getOrCreate($page, STACKS_AREA_NAME);

        // finally we add the row to the stacks table
        $db = Database::connection();
        $stackCID = $page->getCollectionID();
        //$siteTreeID = $parent->getSiteTreeObject()->getSiteTreeID();
        //$v = array($name, $stackCID, $type, $siteTreeID);
        $v = [$name, $stackCID, $type];
        $db->Execute('insert into Stacks (stName, cID, stType) values (?, ?, ?)', $v);

        $stack = static::getByID($stackCID);

        return $stack;
    }

    /**
     * @param $type
     *
     * @return \Concrete\Core\Multilingual\Page\Section\Section|false|null
     */
    protected static function getMultilingualSectionFromType($type)
    {
        $detector = Core::make('multilingual/detector');
        if ($type == self::MULTILINGUAL_CONTENT_SOURCE_DEFAULT) {
            $ms = Section::getDefaultSection();
        } else {
            $c = \Page::getCurrentPage();
            $ms = Section::getBySectionOfSite($c);
            if (!is_object($ms)) {
                $ms = $detector->getPreferredSection();
            }
        }

        return $ms;
    }

    /**
     * @param $area
     *
     * @return self|false
     */
    public static function addGlobalArea($area)
    {
        $parent = \Page::getByPath(STACKS_PAGE_PATH);

        return self::addStackToCategory($parent, $area, static::ST_TYPE_GLOBAL_AREA);
    }

    /**
     * @param $stack
     * @param \Concrete\Core\Page\Stack\Folder\Folder|null $folder
     *
     * @return self|false
     */
    public static function addStack($stack, ?Folder $folder = null)
    {
        $parent = \Page::getByPath(STACKS_PAGE_PATH);
        if ($folder) {
            $parent = $folder->getPage();
        }

        return self::addStackToCategory($parent, $stack, static::ST_TYPE_USER_ADDED);
    }

    /**
     * @return int
     */
    public function getStackType()
    {
        $db = Database::connection();

        return $db->GetOne('select stType from Stacks where cID = ?', [$this->getCollectionID()]);
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public function update($data)
    {
        if (isset($data['stackName'])) {
            $txt = Core::make('helper/text');
            $data['cName'] = $data['stackName'];
            $data['cHandle'] = str_replace('-', Config::get('concrete.seo.page_path_separator'), $txt->urlify($data['stackName']));
        }
        $worked = parent::update($data);

        if (isset($data['stackName'])) {
            // Make sure the stack path is always up-to-date after a name change
            $this->rescanCollectionPath();

            $db = Database::connection();
            $stackName = $data['stackName'];
            $db->Execute('update Stacks set stName = ? WHERE cID = ?', [$stackName, $this->getCollectionID()]);
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

        if ($this->isNeutralStack()) {
            foreach (Section::getList() as $section) {
                $localized = $this->getLocalizedStack($section);
                if ($localized !== null) {
                    $localized->delete();
                }
            }
        }
        parent::delete();
        $db = Database::connection();

        return $db->Execute('delete from Stacks where cID = ?', [$this->getCollectionID()]);
    }

    /**
     * @return string
     */
    public function getStackName()
    {
        $db = Database::connection();

        return $db->GetOne('select stName from Stacks where cID = ?', [$this->getCollectionID()]);
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
     * @return \Concrete\Core\Export\Item\ItemInterface|\Concrete\Core\Export\Item\Stack|\Concrete\Core\Page\Exporter
     */
    public function getExporter()
    {
        return new \Concrete\Core\Export\Item\Stack();
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

    private $multilingualSectionID;

    /**
     * Returns the ID of the multilingual section associated to this stack (or 0 if it's the language-neutral version).
     *
     * @return int
     */
    public function getMultilingualSectionID()
    {
        if (!isset($this->multilingualSectionID)) {
            $db = Database::connection();
            $cID = $db->GetOne('select stMultilingualSection from Stacks where cID = ?', [$this->getCollectionID()]);
            $this->multilingualSectionID = $cID ? (int) $cID : 0;
        }

        return $this->multilingualSectionID;
    }

    /**
     * Returns the multilingual section associated to this stack (or null if it's the language-neutral version).
     *
     * @return \Concrete\Core\Multilingual\Page\Section\Section|null
     */
    public function getMultilingualSection()
    {
        $result = null;
        $msID = $this->getMultilingualSectionID();
        if ($msID !== 0) {
            $s = Section::getByID($msID);
            if ($s) {
                $result = $s;
            }
        }

        return $result;
    }

    /*
        public function updateMultilingualSection(Section $section)
        {
            $db = Database::connection();
            $db->Execute('update Stacks set stMultilingualSection = ? where cID = ?', array($section->getCollectionID(), $this->getCollectionID()));
        }
    */

    /**
     * Returns the collection ID of the locale.neutral version of this stack (or null if this instance is already the neutral version).
     *
     * @return int|null
     */
    protected function getNeutralStackID()
    {
        return ($this->getMultilingualSectionID() === 0) ? null : (int) $this->getCollectionParentID();
    }

    /**
     * Checks if this instance is the locale-neutral version of the stack.
     *
     * @return bool
     */
    public function isNeutralStack()
    {
        return $this->getMultilingualSectionID() === 0;
    }

    /**
     * Returns the locale-neutral version of this stack (or null if this instance is already the neutral version).
     *
     * @param string|int $cvID
     *
     * @return self|null
     */
    public function getNeutralStack($cvID = 'RECENT')
    {
        $result = null;
        $cID = $this->getNeutralStackID();
        if ($cID !== null) {
            $result = static::getByID($cID, $cvID);
        }

        return $result;
    }

    /**
     * Returns the localized version of this stack.
     *
     * @param \Concrete\Core\Multilingual\Page\Section\Section $section
     * @param string|int $cvID
     *
     * @return self|null
     */
    public function getLocalizedStack(Section $section, $cvID = 'RECENT')
    {
        $result = null;
        $mySectionID = $this->getMultilingualSectionID();
        if ($mySectionID !== 0 && $section->getCollectionID() == $mySectionID) {
            $result = $this;
        } else {
            $neutralID = ($mySectionID === 0) ? $this->getCollectionID() : $this->getNeutralStackID();
            $db = Database::connection();
            $localizedID = $db->fetchColumn(
                '
                    select
    	               Stacks.cID
                    from
    	               Stacks
    	               inner join Pages on Stacks.cID = Pages.cID
                    where
    	               Pages.cParentID = ? and Stacks.stMultilingualSection = ?
                    limit 1
                ',
                [$neutralID, $section->getCollectionID()]
            );
            if ($localizedID) {
                $localized = static::getByID($localizedID, $cvID);
                if ($localized) {
                    $result = $localized;
                }
            }
        }

        return $result;
    }

    /**
     * @param \Concrete\Core\Multilingual\Page\Section\Section $section
     *
     * @return self
     */
    public function addLocalizedStack(Section $section)
    {
        $neutralStack = $this->getNeutralStack();
        if ($neutralStack === null) {
            $neutralStack = $this;
        }
        $name = $neutralStack->getCollectionName();
        $neutralStackPage = Page::getByID($neutralStack->getCollectionID());
        $localizedStackPage = $neutralStackPage->duplicate($neutralStackPage);
        $localizedStackPage->update([
            'cName' => $name,
        ]);
        //$siteTreeID = $neutralStack->getSiteTreeID();
        // we have to do this because we need the area to exist before we try and add something to it.
        Area::getOrCreate($localizedStackPage, STACKS_AREA_NAME);
        $localizedStackCID = $localizedStackPage->getCollectionID();
        $localizedStack = static::getByID($localizedStackCID);
        $localizedStack->setMultilingualSection($section, $name);

        return $localizedStack;
    }

    /**
     * Mark this stack as a localized version.
     *
     * @param Section $section Multilingual Section
     * @param string $stackName Optional
     */
    public function setMultilingualSection(Section $section, $stackName = '')
    {
        if ($stackName == '') {
            $stackName = $this->getStackName();
        }

        /** @var Connection $db */
        $db = Database::connection();
        $db->update(
            'Stacks',
            [
                'stMultilingualSection' => $section->getCollectionID(),
                'stName' => $stackName,
            ],
            [
                'cID' => $this->getCollectionID(),
            ]
        );
    }

    /**
     * Copy localized versions from an another neutral stack.
     *
     * @param Stack $original The Stack that has original localized versions
     */
    public function copyLocalizedStacksFrom(Stack $original)
    {
        // Create localized stacks only this is a neutral stack
        if ($this->isNeutralStack()) {
            foreach (Section::getList() as $section) {
                $localized = $this->getLocalizedStack($section);
                // We should skip if localized stack is already created
                if ($localized === null) {
                    $originalLocalized = $original->getLocalizedStack($section);
                    if ($originalLocalized !== null) {
                        $copiedLocalized = $originalLocalized->duplicate($this);
                        $copiedLocalized->update(['cName' => $this->getStackName()]);
                        $copiedLocalized->setMultilingualSection($section, $this->getStackName());
                    }
                }
            }
        }
    }
}
