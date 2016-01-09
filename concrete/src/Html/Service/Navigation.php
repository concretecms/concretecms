<?php
namespace Concrete\Core\Html\Service;

use Database;
use Page;
use URL;
use User;
use Concrete\Core\Validation\CSRF\Token;

class Navigation
{

    /**
     * Returns a link to a page. Note: this always returns a string.
     * if you really need the URL object, use \URL::to($page) instead. Not returning a string was killing a json
     * encode in the sitemap (and could probably screw up other stuff down the line.)
     *
     * @param Page $cObj
     * @return string
     */
    public function getLinkToCollection($cObj)
    {
        return (string) URL::to($cObj);
    }

    /**
     * Returns an array of collections as a breadcrumb to the current page
     *
     * @param Page $c
     * @return Page[]
     */
    public function getTrailToCollection($c)
    {
        $db = Database::connection();

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
        return $this->getLinkToCollection($cObj);
    }

    public function getLogInOutLink()
    {
        if (!id(new User())->isLoggedIn()) {
            $url = URL::to('/login');
            $label = t('Log in');
        } else {
            $url = URL::to('/login', 'logout', id(new Token())->generate('logout'));
            $label = t('Log out');
        }
        return sprintf('<a href="%s">%s</a>', $url, $label);
    }

}
