<?php
namespace Concrete\Core\Entity\Calendar;

use Doctrine\ORM\EntityRepository;

/**
 * @since 8.3.0
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
