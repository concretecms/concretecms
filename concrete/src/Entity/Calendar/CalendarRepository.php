<?php
namespace Concrete\Core\Entity\Calendar;

use Concrete\Core\Entity\Site\Site;
use Doctrine\ORM\EntityRepository;

class CalendarRepository extends EntityRepository
{
    public function findBySite(Site $site)
    {
        $query = $this->getEntityManager()->createQuery('select c from \Concrete\Core\Entity\Calendar\Calendar c where c.site = :site order by c.caName asc');
        $query->setParameter('site', $site);
        return $query->getResult();
    }

    public function findAll()
    {
        return parent::findBy([], ['caName' => 'asc']);
    }

    public function findOneById($id)
    {
        return $this->findOneBy(
            array('caID' => $id)
        );
    }

    public function findOneByName($name)
    {
        return $this->findOneBy(
            array('caName' => $name)
        );
    }


}
