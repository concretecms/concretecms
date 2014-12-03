<?php

namespace Concrete\Multilingual\Page;
use Concrete\Core\Page\Page as CorePage;

defined('C5_EXECUTE') or die("Access Denied.");

class Page extends CorePage
{

    /**
     * @var string
     */
    public $msLocale;

    /**
     * @var string
     */
    public $msIcon;

    /**
     * @var string
     */
    public $msLanguage;

    public static function assign($c, $language, $icon)
    {
        $db = Loader::db();

        $locale = $language . (strlen($icon) ? '_' . $icon : '');

        $db->Replace(
            'MultilingualSections',
            array('cID' => $c->getCollectionID(), 'msLanguage' => $language, 'msIcon' => $icon, 'msLocale' => $locale),
            array('cID'),
            true
        );
    }

    public function unassign()
    {
        $db = Loader::db();
        $db->Execute('delete from MultilingualSections where cID = ?', array($this->getCollectionID()));
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
            $obj = parent::getByID($cID, $cvID, 'MultilingualSection');
            $obj->msLanguage = $r['msLanguage'];
            $obj->msIcon = $r['msIcon'];
            $obj->msLocale = $r['msLocale'];
            return $obj;
        }

        return false;
    }

    /**
     * @param string $language
     * @return MultilingualSection|false
     * @deprecated
     */
    public static function getByLanguage($language)
    {
        $db = Loader::db();
        $r = $db->GetRow(
            'select cID, msLanguage, msIcon, msLocale from MultilingualSections where msLanguage = ?',
            array($language)
        );
        if ($r && is_array($r) && $r['msLanguage']) {
            $obj = parent::getByID($r['cID'], 'RECENT', 'MultilingualSection');
            $obj->msLanguage = $r['msLanguage'];
            $obj->msIcon = $r['msIcon'];
            $obj->msLocale = $r['msLocale'];
            return $obj;
        }
        return false;
    }

    /**
     * @param string $language
     * @return MultilingualSection|false
     */
    public static function getByLocale($locale)
    {
        $db = Loader::db();
        $r = $db->GetRow(
            'select cID, msLanguage, msIcon, msLocale from MultilingualSections where msLocale = ?',
            array($locale)
        );
        if ($r && is_array($r) && $r['msLocale']) {
            $obj = parent::getByID($r['cID'], 'RECENT', 'MultilingualSection');
            $obj->msLanguage = $r['msLanguage'];
            $obj->msIcon = $r['msIcon'];
            $obj->msLocale = $r['msLocale'];
            return $obj;
        }
        return false;
    }


    /**
     * gets the MultilingualSection object for the current section of the site
     * @return MultilingualSection
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

    /**
     * @param Page $page
     * @return MultilingualSection
     */
    public static function getBySectionOfSite($page)
    {
        // looks at the page, traverses its parents until it finds the proper language
        $nav = Loader::helper('navigation');
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
            return MultilingualSection::getByID($returnID);
        }
    }

    public function getLanguage()
    {
        return $this->msLanguage;
    }

    public function getLocale()
    {
        return $this->msLocale;
    }

    public function getLanguageText($locale = ACTIVE_LOCALE)
    {
        if (!class_exists('Zend_Locale')) {
            Loader::library('3rdparty/Zend/Locale');
        }
        try {
            $text = Zend_Locale::getTranslation($this->msLanguage, 'language', $locale);
        } catch (Exception $e) {
            $text = $this->msLanguage;
        }
        return $text;
    }

    public function getIcon()
    {
        return $this->msIcon;
    }

    public static function assignDelete($page)
    {
        $db = Loader::db();
        $db->Execute('delete from MultilingualSections where cID = ?', array($page->getCollectionID()));
        $db->Execute('delete from MultilingualPageRelations where cID = ?', array($page->getCollectionID()));
    }

    public static function relatePage($oldPage, $newPage, $locale)
    {
        $db = Loader::db();
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
            Events::fire('on_multilingual_page_relate', $newPage, $locale);
        }
    }

    public static function isAssigned($page)
    {
        $db = Loader::db();
        $mpRelationID = $db->GetOne(
            'select mpRelationID from MultilingualPageRelations where cID = ?',
            array($page->getCollectionID())
        );
        return $mpRelationID > 0;
    }

    public static function assignDuplicate($newPage, $oldPage)
    {
        $db = Loader::db();
        $mpRelationID = $db->GetOne(
            'select mpRelationID from MultilingualPageRelations where cID = ?',
            array($oldPage->getCollectionID())
        );
        if (self::isMultilingualSection($newPage)) {
            $ms = MultilingualSection::getByID($newPage->getCollectionID());
        } else {
            $ms = MultilingualSection::getBySectionOfSite($newPage);
        }
        if (self::isMultilingualSection($oldPage)) {
            $msx = MultilingualSection::getByID($oldPage->getCollectionID());
        } else {
            $msx = MultilingualSection::getBySectionOfSite($oldPage);
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

            /**
             * Grabs the multilingual section for the old page, and for the new page, and compares themes
             * If they are different, the page gets the theme from the new section
             */
            if (is_object($ms) && is_object($msx)) {
                if ($ms->getCollectionThemeID() != $msx->getCollectionThemeID()) {
                    $pt = $ms->getCollectionThemeObject();
                    $newPage->setTheme($pt);
                }
            }
            Events::fire('on_multilingual_page_relate', $newPage, $language);
        }
    }

    // make sure there is a relations entry in the multilingual table
    public static function assignAdd($page)
    {
        $db = Loader::db();
        $ms = MultilingualSection::getBySectionOfSite($page);
        if (is_object($ms)) {
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
            Events::fire('on_multilingual_page_relate', $page, $ms->getLocale());
        }
    }


    public static function assignMove($page, $oldParent, $newParent)
    {
        if (self::isMultilingualSection($newParent)) {
            $ms = MultilingualSection::getByID($newParent->getCollectionID());
        } else {
            $ms = MultilingualSection::getBySectionOfSite($newParent);
        }
        if (self::isMultilingualSection($oldParent)) {
            $msx = MultilingualSection::getByID($oldParent->getCollectionID());
        } else {
            $msx = MultilingualSection::getBySectionOfSite($oldParent);
        }
        $db = Loader::db();
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

            // now we check to see if the new target section has a different theme
            if (is_object($ms) && is_object($msx)) {
                if ($ms->getCollectionThemeID() != $msx->getCollectionThemeID()) {
                    $pt = $ms->getCollectionThemeObject();
                    $page->setTheme($pt);
                }
            }
        } else {
            self::assignDelete($page);
        }
    }

    public static function isMultilingualSection($cID)
    {
        if (is_object($cID)) {
            $cID = $cID->getCollectionID();
        }
        $db = Loader::db();
        $r = $db->GetRow(
            'select cID, msLanguage, msIcon, msLocale from MultilingualSections where cID = ?',
            array($cID)
        );
        if ($r && is_array($r) && $r['msLocale']) {
            return $r;
        } else {
            return false;
        }
    }

    public static function ignorePageRelation($page, $locale)
    {
        $db = Loader::db();
        $mpRelationID = $db->GetOne(
            'select mpRelationID from MultilingualPageRelations where cID = ?',
            array($page->getCollectionID())
        );
        if ($mpRelationID) {
            $v = array($mpRelationID, 0, $locale);
            $db->Execute('insert into MultilingualPageRelations (mpRelationID, cID, mpLocale) values (?, ?, ?)', $v);
            Events::fire('on_multilingual_page_ignore', $page, $locale);

        }
    }

    public static function getIDLIst()
    {
        static $ids;
        if (isset($ids)) {
            return $ids;
        }

        $db = Loader::db();
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
        $db = Loader::db();
        $ids = MultilingualSection::getIDList();
        if (in_array($page->getCollectionID(), $ids)) {
            $cID = $db->GetOne('select cID from MultilingualSections where msLocale = ?', array($this->getLocale()));
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

}
