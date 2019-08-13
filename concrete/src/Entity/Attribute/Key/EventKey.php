<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Entity\Attribute\Key\Key;

/**
 * @ORM\Entity
 * @ORM\Table(name="CalendarEventAttributeKeys")
 * @since 8.3.0
 */
class EventKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'event';
    }

}
