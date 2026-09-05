<?php
namespace Concrete\Core\Entity\Calendar;

use Doctrine\ORM\EntityRepository;

/**
 * @extends \Doctrine\ORM\EntityRepository<\Concrete\Core\Entity\Calendar\CalendarEvent>
 */
class CalendarEventRepository extends EntityRepository
{

    public function findOneById($id)
    {
        return $this->findOneBy(
            array('eventID' => $id)
        );
    }


}
