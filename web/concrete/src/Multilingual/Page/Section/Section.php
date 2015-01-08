<?php

namespace Concrete\Core\Multilingual\Page\Section;
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
     * @var string
     */
    protected $msCountry;

    /**
     * @var string
     */
    public $msLanguage;

    public static function assign($c, $language, $country)
    {
        $db = Database::get();
        $db->Replace(
            'MultilingualSections',
            array('cID' => $c->getCollectionID(), 'msLanguage' => $language, 'msCountry' => $country),
            array('cID'),
            true
        );

        // Now we make sure we have multilingual enabled
        Config::save('concrete.multilingual.enabled', true);
    }

    public function unassign()
    {
        $db = Database::get();
        $db->delete('MultilingualSections', array('cID' => $this->getCollectionID()));

        $total = $db->GetOne('select count(*) from MultilingualSections');
        if ($total < 1) {
            Config::save('concrete.multilingual.enabled', false);
        }
    }

    /**
     * returns an instance of  MultilingualSection for the given page ID
     * @param int $cID
     * @param int $cvID
     * @return MultilingualSection|false
     */
    public static function getByID($cID, $cvID = 'RECENT')
    {
        $r = self::isMultilingualSection($cID);
        if ($r) {
            $obj = parent::getByID($cID, $cvID, '\Concrete\Core\Multilingual\Page\Section\Section');
            $obj->msLanguage = $r['msLanguage'];
            $obj->msCountry = $r['msCountry'];
            return $obj;
        }

        return false;
    }

    /**
     * @param string $language
     * @return Section|false
     */
    public static function getByLanguage($language)
    {
        $db = Database::get();
        $r = $db->GetRow(
            'select cID, msLanguage, msCountry from MultilingualSections where msLanguage = ?',
            array($language)
        );
        if ($r && is_array($r) && $r['msLanguage']) {
            $obj = parent::getByID($r['cID'], 'RECENT', '\Concrete\Core\Multilingual\Page\Section\Section');
            $obj->msLanguage = $r['msLanguage'];
            $obj->msCountry = $r['msCountry'];
            return $obj;
        }
        return false;
    }

    /**
     * @param string $language
     * @return Section|false
     */
    public static function getByLocale($locale)
    {
        $locale = explode('_', $locale);
        $db = Database::get();
        $r = $db->GetRow(
            'select cID, msLanguage, msCountry from MultilingualSections where msLanguage = ? and msCountry = ?',
            array($locale[0], $locale[1])
        );
        if ($r && is_array($r) && $r['msLanguage']) {
            $obj = parent::getByID($r['cID'], 'RECENT', '\Concrete\Core\Multilingual\Page\Section\Section');
            $obj->msLanguage = $r['msLanguage'];
            $obj->msCountry = $r['msCountry'];
            return $obj;
        }
        return false;
    }


    /**
     * gets the MultilingualSection object for the current section of the site
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
     * @return Section
     */
    public static function getBySectionOfSite($page)
    {
        // looks at the page, traverses its parents until it finds the proper language
        $nav = \Core::make('helper/navigation');
        $pages = $nav->getTrailToCollection($page);
        $pages = array_reverse($pages);
        $pages[] = $page;
        $ids = self::getIDList();
        $returnID = false;
        foreach ($pages as $pc) {
            if (in_array($pc->getCollectionID(), $ids)) {
                $returnID = $pc->getCollectionID();
            }
        }
        if ($returnID) {
            return static::getByID($returnID);
        }
    }

    public function getLanguage()
    {
        return $this->msLanguage;
    }

    public function getLocale()
    {
        $locale = $this->getLanguage();
        if ($this->getCountry()) {
            $locale .= '_' . $this->getCountry();
        }
        return $locale;
    }

    public function getLanguageText($locale = null)
    {
        try {
            if (!$locale) {
                $locale = \Localization::activeLocale();
            }
            $text = Language::getName($this->msLanguage, $locale);
        } catch (Exception $e) {
            $text = $this->msLanguage;
        }
        return $text;
    }

    public function getIcon()
    {
        return $this->getCountry();
    }

    public function getCountry()
    {
        return $this->msCountry;
    }

    public static function registerPage($page)
    {
        if (Config::get('concrete.multilingual.enabled')) {
            $db = Database::get();
            $ms = static::getBySectionOfSite($page);
            if (is_object($ms)) {
                $mpRelationID = $db->GetOne(
                    'select mpRelationID from MultilingualPageRelations where cID = ?',
                    array($page->getCollectionID())
                );
                if ($mpRelationID) {
                    // already exists. We quit and return
                    return $mpRelationID;
                }

                // otherwise, we create a new one.

                $mpRelationID = $db->GetOne('select max(mpRelationID) as mpRelationID from MultilingualPageRelations');
                if (!$mpRelationID) {
                    $mpRelationID = 1;
                } else {
                    $mpRelationID++;
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
        $db = Database::get();
        $db->Execute('delete from MultilingualSections where cID = ?', array($page->getCollectionID()));
        $db->Execute('delete from MultilingualPageRelations where cID = ?', array($page->getCollectionID()));
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
                    $mpRelationID++;
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
        $mpRelationID = $db->GetOne(
            'select mpRelationID from MultilingualPageRelations where cID = ?',
            array($oldPage->getCollectionID())
        );
        if ($mpRelationID) {
            $v = array($mpRelationID, $newPage->getCollectionID(), $locale);
            $db->Execute(
                'delete from MultilingualPageRelations where mpRelationID = ? and mpLocale = ?',
                array($mpRelationID, $locale)
            );
            $db->Execute('delete from MultilingualPageRelations where cID = ?', array($newPage->getCollectionID()));
            $db->Execute('insert into MultilingualPageRelations (mpRelationID, cID, mpLocale) values (?, ?, ?)', $v);
            $pde = new Event($newPage);
            $pde->setLocale($locale);
            \Events::dispatch('on_multilingual_page_relate', $pde);
        }
    }

    public static function isAssigned($page)
    {
        $db = Database::get();
        $mpRelationID = $db->GetOne(
            'select mpRelationID from MultilingualPageRelations where cID = ?',
            array($page->getCollectionID())
        );
        return $mpRelationID > 0;
    }

    public static function registerDuplicate($newPage, $oldPage)
    {
        $db = Database::get();
        $mpRelationID = $db->GetOne(
            'select mpRelationID from MultilingualPageRelations where cID = ?',
            array($oldPage->getCollectionID())
        );
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
                    $mpRelationID++;
                }

                if (is_object(
                    $msx
                )) {   // adding in a check to see if old page was part of a language section or neutral.
                    $db->Execute(
                        'insert into MultilingualPageRelations (mpRelationID, cID, mpLanguage, mpLocale) values (?, ?, ?, ?)',
                        array(
                            $mpRelationID,
                            $oldPage->getCollectionID(),
                            $msx->getLanguage(),
                            $msx->getLocale()
                        )
                    );
                }

            }
            $v = array($mpRelationID, $newPage->getCollectionID(), $ms->getLocale());
            $cID = $db->GetOne(
                'select cID from MultilingualPageRelations where mpRelationID = ? and mpLocale = ?',
                array($mpRelationID, $ms->getLocale())
            );
            if ($cID < 1) {
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



    public static function isMultilingualSection($cID)
    {
        if (is_object($cID)) {
            $cID = $cID->getCollectionID();
        }
        $db = Database::get();
        $r = $db->GetRow(
            'select cID, msLanguage, msCountry from MultilingualSections where cID = ?',
            array($cID)
        );
        if ($r && is_array($r) && $r['msLanguage']) {
            return $r;
        } else {
            return false;
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

    public static function getIDList()
    {
        static $ids;
        if (isset($ids)) {
            return $ids;
        }

        $db = Database::get();
        $ids = $db->GetCol(
            'select MultilingualSections.cID from MultilingualSections inner join Pages on MultilingualSections.cID = Pages.cID order by cDisplayOrder asc'
        );
        if (!$ids) {
            $ids = array();
        }
        return $ids;
    }

    public static function getList()
    {
        $ids = self::getIDList();
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
     * Receives a page in a different language tree, and tries to return the corresponding page in the current language tree
     */
    public function getTranslatedPageID($page)
    {
        $db = Database::get();
        $ids = static::getIDList();
        $locale = explode('_' , $this->getLocale());
        if (in_array($page->getCollectionID(), $ids)) {
            $cID = $db->GetOne('select cID from MultilingualSections where msLanguage = ? and msCountry = ?', array($locale[0], $locale[1]));
            return $cID;
        }
        $mpRelationID = $db->GetOne(
            'select mpRelationID from MultilingualPageRelations where cID = ?',
            array($page->getCollectionID())
        );
        if ($mpRelationID) {
            $cID = $db->GetOne(
                'select cID from MultilingualPageRelations where mpRelationID = ? and mpLocale = ?',
                array($mpRelationID, $this->getLocale())
            );
            return $cID;
        }
    }

    /**
     * @param Section $section
     * @return \Concrete\Core\Multilingual\Page\Section\Translation[] $translations
     */
    public function getSectionInterfaceTranslations()
    {
        $translations = new Translations();
        $db = \Database::get();
        $r = $db->query('select mtID, (if(mt.msgstr = "", 0, 1)) as completed from MultilingualTranslations mt where mtSectionID = ? order by completed asc, msgid asc', array($this->getCollectionID()));
        while ($row = $r->fetch()) {
            $t = Translation::getByID($row['mtID']);
            if (is_object($t)) {
                $translations[] = $t;
            }
        }
        return $translations;
    }

}
