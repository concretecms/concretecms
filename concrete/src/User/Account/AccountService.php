<?php

namespace Concrete\Core\User\Account;

use Concrete\Core\Page\Page;

/**
 * Useful functions when working with "My Account" functionality
 *
 * Class AccountService
 * @package Concrete\Core\User\Account
 */
class AccountService
{

    /**
     * Test if the a page or path path is within the my account section.
     *
     * @param  \Concrete\Core\Page\Page|string|null $pageOrPath
     *
     * @return bool
     */
    public function inMyAccount($pageOrPath)
    {
        $path = '';
        if (is_string($pageOrPath)) {
            $path = $pageOrPath;
        } elseif ($pageOrPath instanceof Page && !$pageOrPath->isError()) {
            $path = $pageOrPath->getCollectionPath();
        }
        return $path === '/account' || strpos($path, '/account/') === 0;
    }


}
