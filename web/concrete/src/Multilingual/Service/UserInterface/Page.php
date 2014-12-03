<?php

namespace Concrete\Core\Multilingual\Service\UserInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class Page
{

    /** Cached values to avoid multiple calls to self::getAliasesOf().
     * @var null|array
     */
    private static $_pageAliasesOf = null;

    /** Cached values to avoid multiple calls to MultilingualSection::getList().
     * @var null|array
     */
    private static $_allLanguages = null;

    /** Resets the class cache. */
    public function reset()
    {
        self::$_pageAliasesOf = null;
        self::$_allLanguages = null;
    }

    /** Returns the cID for the page. If $page is an alias it returns the cID of the original page.
     * @param Page $page The Page instance for which you want the original Page.
     * @param int $aliasLevel [out] Will be set to: 0 if $page is not an alias, 1 if $page is an alias (but not the current page), 2 if $page is an alias and it's the current page.
     * @return int Returns the cID of the original page.
     */
    private static function getOriginal_cID($page, &$aliasLevel = null)
    {
        $cID = (int)$page->cID;
        if ($page->isAlias()) {
            $aliasOf = (int)$page->getCollectionPointerID();
            if ($aliasOf != $cID) {
                $aliasLevel = 1;
                return $aliasOf;
            } else {
                // This happens if $page is an alias and it's the current page.
                $aliasLevel = 2;
            }
        } else {
            $aliasLevel = 0;
        }
        return $cID;
    }

    /** Returns all the aliases of a specific page.
     * @param int $cID The collection ID of the Page.
     * @return array Returns an array whose keys are the cID of the aliased pages, and the values are arrays with these keys:<ul>
     *    <li>int <b>cID</b> The collection ID of the alias.</li>
     *    <li>Page <b>Page</b> The Page instance of the alias.</li>
     *    <li>MultilingualSection <b>Lang</b> The MultilingualSection of the alias.</li>
     * </ul>
     */
    private static function getAliasesOf($cID)
    {
        $db = Loader::db();
        $aliases = array();
        if ($cID) {
            $rs = $db->Query('SELECT cID FROM Pages WHERE (cPointerID = ?)', array($cID));
            foreach ($rs->GetAll() as $row) {
                $row['Page'] = Page::getByID($row['cID']);
                $row['Lang'] = MultilingualSection::getBySectionOfSite($row['Page']);
                $aliases[$row['cID']] = $row;
            }
            $rs->Close();
        }
        return $aliases;
    }

    /** Returns the page in a specific language related to the specified page, with support to aliased pages.
     * @param Page $page The page for which you want the translated page.
     * @param MultilingualSection $lang The language of the translated page.
     * @param bool $fallbackToHome Set to true (default) if you want to fallback to the homepage of $lang if there's no translation for $page. If false you'll get null.
     * @return Page|null
     */
    public function getTranslatedPageWithAliasSupport($page, $lang, $fallbackToHome = true)
    {
        // Is the page already under $lang (in the same locale)?
        $pageLang = MultilingualSection::getBySectionOfSite($page);
        if ($pageLang && ($pageLang->msLocale === $lang->msLocale)) {
            return $page;
        }
        // Let's determine the original cID (differs from $page->cID if $page is an alias).
        $original_cID = self::getOriginal_cID($page, $aliasLevel);
        // Let's determine the aliases of the page (if there are some).
        if (!is_array(self::$_pageAliasesOf)) {
            self::$_pageAliasesOf = array();
        }
        if (!array_key_exists($original_cID, self::$_pageAliasesOf)) {
            self::$_pageAliasesOf[$original_cID] = self::getAliasesOf($original_cID);
        }
        // If we're querying an alias of $page, return it.
        foreach (self::$_pageAliasesOf[$original_cID] as $alias) {
            if ($alias['Lang'] && ($alias['Lang']->msLocale === $lang->msLocale)) {
                return $alias['Page'];
            }
        }
        // Let's check if we're wanting the page itself.
        if ($aliasLevel == 2) {
            $originalPage = Page::getByID($original_cID);
            $originalLanguage = MultilingualSection::getBySectionOfSite($originalPage);
            if ($originalLanguage && ($originalLanguage->msLocale === $lang->msLocale)) {
                return $originalPage;
            }
        }
        // Let's check if we've got a translated page of this page
        $relatedID = $lang->getTranslatedPageID($page);
        if ($relatedID) {
            return Page::getByID($relatedID);
        }
        // Let's see if we can point to an alias of another translated page
        if (!is_array(self::$_allLanguages)) {
            self::$_allLanguages = MultilingualSection::getList();
        }
        foreach (self::$_allLanguages as $allLanguage) {
            $translatedTo_cID = $allLanguage->getTranslatedPageID($page);
            if ($translatedTo_cID) {
                $translatedTo_Page = Page::getByID($translatedTo_cID);
                $translatedTo_cID = self::getOriginal_cID($translatedTo_Page, $aliasLevel);
                if (!$aliasLevel) {
                    if (!array_key_exists($translatedTo_cID, self::$_pageAliasesOf)) {
                        self::$_pageAliasesOf[$translatedTo_cID] = self::getAliasesOf($translatedTo_cID);
                    }
                    foreach (self::$_pageAliasesOf[$translatedTo_cID] as $alias) {
                        if ($alias['Lang'] && ($alias['Lang']->msLocale === $lang->msLocale)) {
                            return $alias['Page'];
                        }
                    }
                }
            }
        }
        // Fallback_ let's return the $lang homepage
        return $fallbackToHome ? $lang : null;
    }

    /** Adds the link rel="alternate" hreflang=".." href=".." tag to the specified page.
     * @param Page $page
     */
    public function addAlternateHrefLang($page)
    {
        if ($page || ($page = Page::getCurrentPage())) {
            if (!$page->isAdminArea()) {
                if ($lang = MultilingualSection::getBySectionOfSite($page)) {
                    if (!is_array(self::$_allLanguages)) {
                        self::$_allLanguages = MultilingualSection::getList();
                    }
                    $html = Loader::helper('html');
                    $navigation = Loader::helper('navigation');
                    $v = View::getInstance();
                    if (!$page->isAlias() && $page->cID == $lang->cID) {
                        $isRoot = true;
                    } else {
                        $isRoot = false;
                    }
                    foreach (self::$_allLanguages as $otherLang) {
                        if ($otherLang->msLocale != $lang->msLocale) {
                            if ($isRoot) {
                                $otherPage = $otherLang;
                            } else {
                                $otherPage = $this->getTranslatedPageWithAliasSupport($page, $otherLang, false);
                            }
                            if ($otherPage) {
                                $v->addHeaderItem(
                                    '<link rel="alternate" hreflang="' . $otherLang->msLocale . '" href="' . $navigation->getLinkToCollection(
                                        $otherPage
                                    ) . '" />'
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}
