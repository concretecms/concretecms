<?php
namespace Concrete\Core\Calendar\Calendar;

use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarRepository;

class CalendarService
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    public function getList($site = null)
    {
        /**
         * @var $r CalendarRepository
         */
        $r = $this->entityManager->getRepository(Calendar::class);
        if (is_object($site)) {
            return $r->findBySite($site);
        } else {
            return $r->findAll();
        }
    }

    public function getByID($id)
    {
        $r = $this->entityManager->getRepository(Calendar::class);
        return $r->findOneById($id);
    }

    public function save(Calendar $calendar)
    {
        $this->entityManager->persist($calendar);
        $this->entityManager->flush();
        return $calendar;
    }

    public function delete(Calendar $calendar)
    {
        $this->entityManager->remove($calendar);
        $this->entityManager->flush();
    }

    public function getByName($name)
    {
        /**
         * @var $r CalendarRepository
         */
        $r = $this->entityManager->getRepository(Calendar::class);
        return $r->findOneByName($name);
    }
}
