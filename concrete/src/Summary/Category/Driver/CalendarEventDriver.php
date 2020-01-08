<?php
namespace Concrete\Core\Summary\Category\Driver;

use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Summary\Category\CategoryMemberInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventDriver extends AbstractDriver
{
    
    public function getCategoryMemberFromIdentifier($identifier): CategoryMemberInterface
    {
        return $this->app->make(EventService::class)->getByID($identifier);
    }


}
