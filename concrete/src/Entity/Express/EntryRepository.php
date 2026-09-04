<?php
namespace Concrete\Core\Entity\Express;

/**
 * @extends \Doctrine\ORM\EntityRepository<\Concrete\Core\Entity\Express\Entry>
 */
class EntryRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOneByID($id)
    {
        return $this->findOneBy(array('exEntryID' => $id));
    }
}
