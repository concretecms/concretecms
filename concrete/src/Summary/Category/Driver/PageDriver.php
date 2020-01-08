<?php
namespace Concrete\Core\Summary\Category\Driver;

use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Category\CategoryMemberInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class PageDriver extends AbstractDriver
{
    
    public function getCategoryMemberFromIdentifier($identifier): CategoryMemberInterface
    {
        return Page::getByID($identifier, 'ACTIVE');
    }

}
