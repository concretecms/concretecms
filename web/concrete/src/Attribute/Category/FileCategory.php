<?php

namespace Concrete\Core\Attribute\Category;

class FileCategory extends AbstractCategory
{

    public function getByHandle($handle)
    {
        $query = 'select f from \Concrete\Core\Entity\File\Attribute f join f.attribute a
         where a.akHandle = :handle';
        $query = $this->entityManager->createQuery($query);
        $query->setParameter('handle', $handle);
        return $query->getOneOrNullResult();
    }



}
