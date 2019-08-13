<?php
namespace Concrete\Core\Calendar\Event\Version;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;

/**
 * @since 8.3.0
 */
class Listener
{

    public function preRemove(CalendarEventVersion $version, LifecycleEventArgs $event)
    {

        $category = \Core::make('Concrete\Core\Attribute\Category\EventCategory');

        foreach($category->getAttributeValues($version) as $value) {
            $category->deleteValue($value);
        }

    }



}