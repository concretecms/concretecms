<?php
namespace Concrete\Core\Entity\Express;

class EntryRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOneByID($id)
    {
        return $this->findOneBy(array('exEntryID' => $id));
    }
}
