<?php
namespace Concrete\Core\Entity\Calendar;

use Doctrine\ORM\EntityRepository;

class CalendarEventRepository extends EntityRepository
{

    public function findOneById($id)
    {
        return $this->findOneBy(
            array('eventID' => $id)
        );
    }


}
