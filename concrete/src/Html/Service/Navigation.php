<?php
namespace Concrete\Core\Html\Service;

use Database;
use Page;
use URL;
use User;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Navigation
{
    /**
     * Returns a link to a page. Note: this always returns a string.
     * if you really need the URL object, use \URL::to($page) instead. Not returning a string was killing a json
     * encode in the sitemap (and could probably screw up other stuff down the line.).
     *
     * @param Page $cObj
     *
     * @return string
     */
    public function getLinkToCollection($cObj)
    {
        return (string) URL::to($cObj);
    }

    /**
     * Returns an array of collections as a breadcrumb to the current page.
     *
     * @param Page $c
     *
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
     * Returns the URL of a collection so that it can be clicked on.
     *
     * @param Page $cObj
     *
     * @return string
     */
    public function getCollectionURL($cObj)
    {
        return $this->getLinkToCollection($cObj);
    }

    /**
     * Get the path to the login page, relative to the concrete5 web root.
     *
     * @return string
     */
    public function getLoginPath()
    {
        return app('config')->get('concrete.paths.login');
    }

    /**
     * Get the URL to the login page.
     *
     * @param array $arguments additional arguments for the login page.
     *
     * @return \Concrete\Core\Url\UrlImmutable
     */
    public function getLoginUrl(array $arguments = [])
    {
        array_unshift($arguments, $this->getLoginPath());

        return app(ResolverManagerInterface::class)->resolve($arguments);
    }

    /**
     * Get the URL to be visited to log out the current user.
     *
     * @return \Concrete\Core\Url\UrlImmutable
     */
    public function getLogoutUrl()
    {
        return $this->getLoginUrl([
            'do_logout',
            app('token')->generate('do_logout'),
        ]);
    }

    /**
     * Get an "<a>" HTML element pointing to the login page (if the user is not logged in) or to the URL that logs out the user.
     *
     * @return string
     */
    public function getLogInOutLink()
    {
        if (!id(new User())->isLoggedIn()) {
            $url = $this->getLoginUrl();
            $label = t('Log in');
        } else {
            $url = $this->getLogoutUrl();
            $label = t('Log out');
        }

        return sprintf('<a href="%s">%s</a>', $url, $label);
    }
}
