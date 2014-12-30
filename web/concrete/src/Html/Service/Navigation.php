<?php
namespace Concrete\Core\Html\Service;

use Config;
use Loader;
use Page;

class Navigation
{

    /**
     * Returns a link to a page
     * 
     * @param Page $cObj
     * @param boolean $prependBaseURL
     * @param boolean $ignoreUrlRewriting
     * @return string
     */
    public function getLinkToCollection(&$cObj, $prependBaseURL = false, $ignoreUrlRewriting = false)
    {
        // basically returns a link to a collection, based on whether or we have
        // mod_rewrite enabled, and the collection has a path
        $dispatcher = '';
        if (!Config::get('concrete.seo.url_rewriting_all')) {
            if ((!Config::get('concrete.seo.url_rewriting')) || $ignoreUrlRewriting) {
                $dispatcher = '/' . DISPATCHER_FILENAME;
            }
        }
        if ($cObj->isExternalLink() && $prependBaseURL == false) {
            $link = $cObj->getCollectionPointerExternalLink();
            return $link;
        }

        if ($cObj->getCollectionPath() != null) {
            $txt = Loader::helper('text');
            $link = DIR_REL . $dispatcher . $txt->encodePath($cObj->getCollectionPath()) . '/';
        } else {
            $_cID = ($cObj->getCollectionPointerID() > 0) ? $cObj->getCollectionPointerOriginalID() : $cObj->getCollectionID();
            if ($_cID > 1) {
                $link = DIR_REL . $dispatcher . '?cID=' . $_cID;
            } else {
                $link = DIR_REL . '/';
            }
        }

        if ($prependBaseURL) {
            $link = BASE_URL . $link;
        }

        if (!Config::get('concrete.seo.trailing_slash') && $link != '/') {
            $link = rtrim($link, '/');
        }

        return $link;
    }

    /**
     * Returns an array of collections as a breadcrumb to the current page
     * 
     * @param Page $c
     * @return Page[]
     */
    public function getTrailToCollection($c)
    {
        $db = Loader::db();

        $cArray = array();
        $currentcParentID = $c->getCollectionParentID();
        if ($currentcParentID > 0) {
            while (is_numeric($currentcParentID) && $currentcParentID > 0 && $currentcParentID) {
                $q = "select cID, cParentID from Pages where cID = '{$currentcParentID}'";
                $r = $db->query($q);
                $row = $r->fetchRow();
                if ($row['cID']) {
                    $cArray[] = Page::getByID($row['cID'], 'ACTIVE');
                }

                $currentcParentID = $row['cParentID']; // moving up the tree until we hit 1
            }
        }

        return $cArray;
    }

    /**
     * Returns the URL of a collection so that it can be clicked on
     * 
     * @param Page $cObj
     * @return string
     */
    public function getCollectionURL($cObj)
    {
        return $this->getLinkToCollection($cObj, true);
    }

}
