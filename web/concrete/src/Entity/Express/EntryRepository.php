<?php
namespace Concrete\Core\Entity\Express;

use Doctrine\ORM\EntityRepository;

class EntryRepository extends EntityRepository
{
    public function findOneByID($id)
    {
        return $this->findOneBy(array('exEntryID' => $id));
    }
}
