<?php
namespace Concrete\Core\Multilingual\Page\Section;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Database;
use Concrete\Core\Multilingual\Page\Event;
use Gettext\Translations;
use Punic\Language;
use Config;

defined('C5_EXECUTE') or die("Access Denied.");

class Section extends Page
{

    /**
     * @var \Concrete\Core\Entity\Multilingual\Section
     */
    protected $section;

    protected function setSectionEntity($section)
    {
        $this->section = $section;
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\MultilingualSectionResponse';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'multilingual_section';
    }

    public static function assign(Site $site, $c, $language, $country, $numPlurals = null, $pluralRule = '', $pluralCases = array())
    {
        $pluralRule = (string) $pluralRule;
        if (empty($numPlurals) || ($pluralRule === '') || (empty($pluralCases))) {
            $locale = $language;
            if ($country !== '') {
                $locale .= '_' . $country;
            }
            $localeInfo = \Gettext\Languages\Language::getById($locale);
            if ($localeInfo) {
                $numPlurals = count($localeInfo->categories);
                $pluralRule = $localeInfo->formula;
                $pluralCases = array();
                foreach ($localeInfo->categories as $category) {
                    $pluralCases[] = $category->id.'@'.$category->examples;
                }
            }
        }
        $em = Database::get()->getEntityManager();
        $section = $em->find('Concrete\Core\Entity\Multilingual\Section', $c->getCollectionID());
        if (!is_object($section)) {
            $section = new \Concrete\Core\Entity\Multilingual\Section();
        }

        $country = (string) $country;
        $section->setSite($site);
        $section->setPageID($c->getCollectionID());
        $section->setLanguage($language);
        $section->setCountry($country);

        if ((!empty($numPlurals)) && ($pluralRule !== '') && (!empty($pluralCases))) {
            $section->setPluralRule($pluralRule);
            $section->setNumPlurals($numPlurals);
            $pluralCases = is_array($pluralCases) ? implode("\n", $pluralCases) : $pluralCases;
            $section->setPluralCases($pluralCases);
        }

        $em->persist($section);
        $em->flush();
    }

    public function unassign()
    {
        $em = Database::get()->getEntityManager();
        $em->remove($this->section);
        $em->flush();
    }

    /**
     * Returns an instance of  MultilingualSection for the given page ID.
     *
     * @param int $cID
     * @param int|string $cvID
     * @param string $class
     *
     * @return MultilingualSection|false
     */
    public static function getByID($cID, $cvID = 'RECENT')
    {
        $entity = self::getSectionEntity($cID);
        if ($entity) {
            $obj = parent::getByID($cID, $cvID);
            $obj->setSectionEntity($entity);
            return $obj;
        }

        return false;
    }

    /**
     * @param string $language
     *
     * @return Section|false
     */
    public static function getByLanguage($language, Site $site = null)
    {

        if (!is_object($site)) {
            $site = \Site::getSite();
        }

        $em = Database::get()->getEntityManager();
        $section = $em->getRepository('Concrete\Core\Entity\Multilingual\Section')
            ->findOneBy(array('site' => $site, 'msLanguage' => $language));

        if (is_object($section)) {
            $obj = parent::getByID($section->getPageID(), 'RECENT');
            $obj->setSectionEntity($section);
            return $obj;
        }

        return false;
    }

    /**
     * @param string $language
     *
     * @return Section|false
     */
    public static function getByLocale($locale, Site $site = null)
    {
        if (!$site) {
            $site = \Core::make('site')->getSite();
        }
        $locale = explode('_', $locale);
        $em = Database::get()->getEntityManager();
        $section = $em->getRepository('Concrete\Core\Entity\Multilingual\Section')
            ->findOneBy(array('site' => $site, 'msLanguage' => $locale[0], 'msCountry' => $locale[1]));

        if (is_object($section)) {
            $obj = parent::getByID($section->getPageID(), 'RECENT');
            $obj->setSectionEntity($section);
            return $obj;
        }

        return false;
    }

    /**
     * Gets the MultilingualSection object for the current section of the site.
     *
     * @return Section
     */
    public static function getCurrentSection()
    {
        static $lang;
        if (!isset($lang)) {
            $c = Page::getCurrentPage();
            if ($c instanceof Page) {
                $lang = self::getBySectionOfSite($c);
            }
        }

        return $lang;
    }

    public static function getByLocaleOrLanguage($locale)
    {
        $explode = preg_split('/-|_/', $locale);
        if (count($explode) == 2) {
            // we have a language first, and a country second
            $section = static::getByLocale($explode[0] . '_' . $explode[1]);
            if ($section) {
                return $section;
            }
        }
        $section = static::getByLanguage($explode[0]);

        return $section;
    }

    /**
     * @param Page $page
     *
     * @return Section
     */
    public static function getBySectionOfSite($page)
    {
        $identifier = sprintf('/multilingual/section/%s', $page->getCollectionID());
        $cache = \Core::make('cache/request');
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            $returnID = $item->get();
        } else {
            $item->lock();
            $returnID = null;
            if ($page->getPageTypeHandle() == STACKS_PAGE_TYPE) {
                $parent = Page::getByID($page->getCollectionParentID());
                if ($parent->getCollectionPath() == STACKS_PAGE_PATH) {
                    // this is the default multilingual section.
                    return static::getDefaultSection();
                } else {
                    // this is a stack category page type
                    $locale = $parent->getCollectionHandle();

                    return static::getByLocale($locale);
                }
            } else {

                if ($page->isPageDraft() && $page->getPageDraftTargetParentPageID()) {
                    $cParentID = $page->getPageDraftTargetParentPageID();
                } else {
                    $cParentID = $page->getCollectionParentID();
                }

                $parent = \Page::getByID($cParentID);
                $nav = \Core::make('helper/navigation');
                $pages = $nav->getTrailToCollection($parent);
                $pages = array_reverse($pages);
                $pages[] = $parent;
                $pages[] = $page;
                $ids = self::getIDList();
                $returnID = false;
                foreach ($pages as $pc) {
                    if (in_array($pc->getCollectionID(), $ids)) {
                        $returnID = $pc->getCollectionID();
                    }
                }
            }
            $cache->save($item->set($returnID));
        }

        if ($returnID) {
            return static::getByID($returnID);
        }
    }

    public function getLanguage()
    {
        return $this->section->getLanguage();
    }

    public function getLocale()
    {
        $locale = $this->getLanguage();
        if ($this->getCountry()) {
            $locale .= '_' . $this->getCountry();
        }

        return $locale;
    }

    public static function getDefaultSection(Site $site = null)
    {
        if (!is_object($site)) {
            $site = \Site::getSite();
        }
        $default_locale = $site->getConfigRepository()->get('multilingual.default_locale');
        return static::getByLocale($default_locale);
    }

    public function getLanguageText($locale = null)
    {
        try {
            if (!$locale) {
                $locale = \Localization::activeLocale();
            }
            $text = Language::getName($this->section->getLanguage(), $locale);
        } catch (\Exception $e) {
            $text = $this->section->getLanguage();
        }

        return $text;
    }

    public function getIcon()
    {
        return $this->section->getCountry();
    }

    public function getCountry()
    {
        return $this->section->getCountry();
    }

    /**
     * Returns the number of plural forms.
     *
     * @return int
     *
     * @example For Japanese: returns 1
     * @example For English: returns 2
     * @example For French: returns 2
     * @example For Russian returns 3
     */
    public function getNumberOfPluralForms()
    {
        return (int) $this->section->getNumPlurals();
    }

    /**
     * Returns the rule to determine which plural we should use (in gettext notation).
     *
     * @return string
     *
     * @example For Japanese: returns '0'
     * @example For English: returns 'n != 1'
     * @example For French: returns 'n > 1'
     * @example For Russian returns '(n % 10 == 1 && n % 100 != 11) ? 0 : ((n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14)) ? 1 : 2)'
     */
    public function getPluralsRule()
    {
        return (string) $this->section->getPluralRule();
    }

    /**
     * Returns the plural cases for the language; array keys are the case name, array values are some examples for that case.
     *
     * @return array
     *
     * @example For Japanese: returns
     *     'other' => '0~15, 100, 1000, 10000, 100000, 1000000, …'
     * @example For English: returns
     *     'one' => '1',
     *     'other' => '0, 2~16, 100, 1000, 10000, 100000, 1000000, …'
     * @example For French: returns
     *     'one' => '0, 1',
     *     'other' => '2~17, 100, 1000, 10000, 100000, 1000000, …'
     * @example For Russian returns
     *     'one' => '1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, …',
     *     'few' => '2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, …',
     *     'other' => '0, 5~19, 100, 1000, 10000, 100000, 1000000, …',
     */
    public function getPluralsCases()
    {
        return (array) $this->section->getPluralCases();
    }

    public static function registerPage($page)
    {
        if (\Core::make('multilingual/detector')->isEnabled()) {
            $db = Database::get();
            $ms = static::getBySectionOfSite($page);
            if (is_object($ms)) {
                $mpRelationID = self::getMultilingualPageRelationID($page->getCollectionID());

                if ($mpRelationID) {
                    // already exists. We quit and return
                    return $mpRelationID;
                }

                // otherwise, we create a new one.

                $mpRelationID = $db->GetOne('select max(mpRelationID) as mpRelationID from MultilingualPageRelations');
                if (!$mpRelationID) {
                    $mpRelationID = 1;
                } else {
                    ++$mpRelationID;
                }
                $v = array($mpRelationID, $page->getCollectionID(), $ms->getLanguage(), $ms->getLocale());
                $db->Execute(
                    'insert into MultilingualPageRelations (mpRelationID, cID, mpLanguage, mpLocale) values (?, ?, ?, ?)',
                    $v
                );
                $pde = new Event($page);
                $pde->setLocale($ms->getLocale());
                \Events::dispatch('on_multilingual_page_relate', $pde);

                return $mpRelationID;
            }
        }
    }

    public static function unregisterPage($page)
    {
        if (static::isMultilingualSection($page)) {
            $entity = static::getSectionEntity($page->getCollectionID());
            $db = Database::get();
            if (is_object($entity)) {
                $em = $db->getEntityManager();
                $em->remove($entity);
                $em->flush();
                $db->Execute('delete from MultilingualPageRelations where cID = ?', array($page->getCollectionID()));
            }
        }
    }

    public static function registerMove($page, $oldParent, $newParent)
    {
        if (static::isMultilingualSection($newParent)) {
            $ms = static::getByID($newParent->getCollectionID());
        } else {
            $ms = static::getBySectionOfSite($newParent);
        }
        if (self::isMultilingualSection($oldParent)) {
            $msx = static::getByID($oldParent->getCollectionID());
        } else {
            $msx = static::getBySectionOfSite($oldParent);
        }
        $db = Database::get();
        if (is_object($ms)) {
            $cID = $db->GetOne(
                'select cID from MultilingualPageRelations where cID = ?',
                array($page->getCollectionID())
            );
            if (!$cID) {
                $mpRelationID = $db->GetOne('select max(mpRelationID) as mpRelationID from MultilingualPageRelations');
                if (!$mpRelationID) {
                    $mpRelationID = 1;
                } else {
                    ++$mpRelationID;
                }
                $v = array($mpRelationID, $page->getCollectionID(), $ms->getLanguage(), $ms->getLocale());
                $db->Execute(
                    'insert into MultilingualPageRelations (mpRelationID, cID, mpLanguage, mpLocale) values (?, ?, ?, ?)',
                    $v
                );
            } else {
                $db->Execute(
                    'update MultilingualPageRelations set mpLanguage = ? where cID = ?',
                    array($ms->getLanguage(), $page->getCollectionID())
                );
            }
        } else {
            self::unregisterPage($page);
        }
    }

    public static function relatePage($oldPage, $newPage, $locale)
    {
        $db = Database::get();
        $mpRelationID = self::getMultilingualPageRelationID($oldPage->getCollectionID());

        $section = Section::getByLocale($locale);

        if ($mpRelationID && $section) {
            $v = array($mpRelationID, $newPage->getCollectionID(), $section->getLocale(), $section->getLanguage());
            $db->Execute(
                'delete from MultilingualPageRelations where mpRelationID = ? and mpLocale = ?',
                array($mpRelationID, $section->getLocale())
            );
            $db->Execute('delete from MultilingualPageRelations where cID = ?', array($newPage->getCollectionID()));
            $db->Execute('insert into MultilingualPageRelations (mpRelationID, cID, mpLocale, mpLanguage) values (?, ?, ?, ?)', $v);
            $pde = new Event($newPage);
            $pde->setLocale($locale);
            \Events::dispatch('on_multilingual_page_relate', $pde);
        }
    }

    public static function isAssigned($page)
    {
        $mpRelationID = self::getMultilingualPageRelationID($page->getCollectionID());

        return $mpRelationID > 0;
    }

    public static function getMultilingualPageRelationID($cID)
    {
        $db = Database::get();

        $mpRelationID = $db->getOne(
            'select mpRelationID from MultilingualPageRelations where cID = ?',
            array($cID)
        );

        return $mpRelationID;
    }

    public static function getCollectionIDForLocale($mpRelationID, $locale)
    {
        $db = Database::get();

        $cID = $db->GetOne(
            'select cID from MultilingualPageRelations where mpRelationID = ? and mpLocale = ?',
            array($mpRelationID, $locale)
        );

        return $cID;
    }

    public static function getRelatedCollectionIDForLocale($cID, $locale)
    {
        $mpRelationID = self::getMultilingualPageRelationID($cID);

        if (!$mpRelationID) {
            return null;
        }

        $relatedCID = self::getCollectionIDForLocale($mpRelationID, $locale);

        return $relatedCID;
    }

    public static function registerDuplicate($newPage, $oldPage)
    {
        $db = Database::get();

        $mpRelationID = self::getMultilingualPageRelationID($oldPage->getCollectionID());

        if (static::isMultilingualSection($newPage)) {
            $ms = static::getByID($newPage->getCollectionID());
        } else {
            $ms = static::getBySectionOfSite($newPage);
        }
        if (static::isMultilingualSection($oldPage)) {
            $msx = static::getByID($oldPage->getCollectionID());
        } else {
            $msx = static::getBySectionOfSite($oldPage);
        }
        if (is_object($ms)) {
            if (!$mpRelationID) {
                $mpRelationID = $db->GetOne('select max(mpRelationID) as mpRelationID from MultilingualPageRelations');
                if (!$mpRelationID) {
                    $mpRelationID = 1;
                } else {
                    ++$mpRelationID;
                }

                // adding in a check to see if old page was part of a language section or neutral.
                if (is_object($msx)) {
                    $db->Execute(
                        'insert into MultilingualPageRelations (mpRelationID, cID, mpLanguage, mpLocale) values (?, ?, ?, ?)',
                        array(
                            $mpRelationID,
                            $oldPage->getCollectionID(),
                            $msx->getLanguage(),
                            $msx->getLocale(),
                        )
                    );
                }
            }

            $v = array($mpRelationID, $newPage->getCollectionID(), $ms->getLocale());

            $cID = self::getCollectionIDForLocale($mpRelationID, $ms->getLocale());

            if ($cID > 0) {
                $db->Execute(
                    'delete from MultilingualPageRelations where mpRelationID = ? and mpLocale = ?',
                    array($mpRelationID, $ms->getLocale())
                );
            }

            $db->Execute('insert into MultilingualPageRelations (mpRelationID, cID, mpLocale) values (?, ?, ?)', $v);

            $pde = new Event($newPage);
            $pde->setLocale($ms->getLocale());
            \Events::dispatch('on_multilingual_page_relate', $pde);
        }
    }

    public function isDefaultMultilingualSection(Site $site = null)
    {
        if (!is_object($site)) {
            $site = \Site::getSite();
        }
        $default_locale = $site->getConfigRepository()->get('multilingual.default_locale');

        return $this->getLocale() == $default_locale;
    }

    protected static function getSectionEntity($cID)
    {
        $em = Database::get()->getEntityManager();
        $entity = $em->find('Concrete\Core\Entity\Multilingual\Section', $cID);
        return $entity;
    }

    public static function isMultilingualSection($cID)
    {
        if (is_object($cID)) {
            $cID = $cID->getCollectionID();
        }

        if ($cID) {
            $entity = self::getSectionEntity($cID);
            return is_object($entity);
        }
    }

    public static function ignorePageRelation($page, $locale)
    {
        $db = Database::get();

        // first, we retrieve the relation for the page in the default locale.
        $mpRelationID = static::registerPage($page);

        $v = array($mpRelationID, 0, $locale);
        $db->Execute('insert into MultilingualPageRelations (mpRelationID, cID, mpLocale) values (?, ?, ?)', $v);
        $pde = new Event($page);
        $pde->setLocale($locale);
        \Events::dispatch('on_multilingual_page_ignore', $pde);
    }

    public static function getIDList(Site $site = null)
    {
        if (!$site) {
            $site = \Site::getSite();
        }
        static $ids;
        if (isset($ids)) {
            return $ids;
        }

        $em = Database::get()->getEntityManager();
        $sites = $em->getRepository('Concrete\Core\Entity\Multilingual\Section')
            ->findBySite($site);

        $ids = array();
        foreach($sites as $site) {
            $ids[] = $site->getPageID();
        }

        return $ids;
    }

    public static function getList(Site $site = null)
    {
        $ids = self::getIDList($site);
        $pages = array();
        if ($ids && is_array($ids)) {
            foreach ($ids as $cID) {
                $obj = self::getByID($cID);
                if (is_object($obj)) {
                    $pages[] = $obj;
                }
            }
        }

        return $pages;
    }

    /**
     * Receives a page in a different language tree, and tries to return the corresponding page in the current language tree.
     *
     * @return int|null
     */
    public function getTranslatedPageID($page)
    {
        $db = Database::get();
        $ids = static::getIDList();
        $locale = explode('_', $this->getLocale());
        if (in_array($page->getCollectionID(), $ids)) {
            return $this->section->getPageID();
        }

        $mpRelationID = self::getMultilingualPageRelationID($page->getCollectionID());

        if ($mpRelationID) {
            $cID = self::getCollectionIDForLocale($mpRelationID, $this->getLocale());

            return $cID;
        }
    }

    /**
     * Loads the translations of this multilingual section.
     *
     * @param bool $untranslatedFirst Set to true to have untranslated strings first
     *
     * @return \Gettext\Translations
     */
    public function getSectionInterfaceTranslations($untranslatedFirst = false)
    {
        $translations = new Translations();
        $translations->setLanguage($this->getLocale());
        $translations->setPluralForms($this->getNumberOfPluralForms(), $this->getPluralsRule());
        $db = \Database::get();
        $r = $db->query(
            "select *
            from MultilingualTranslations
            where mtSectionID = ?
            order by ".($untranslatedFirst ? "if(ifnull(msgstr, '') = '', 0, 1), " : "")."mtID",
            array($this->getCollectionID())
        );
        while ($row = $r->fetch()) {
            $t = Translation::getByRow($row);
            if (isset($t)) {
                $translations[] = $t;
            }
        }

        return $translations;
    }
}
