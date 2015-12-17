<?php

namespace Concrete\Core\Attribute\Category;

class PageCategory extends AbstractCategory
{

    public function getByHandle($handle)
    {
        $query = 'select p from \Concrete\Core\Entity\Page\Attribute p join p.attribute a
         where a.akHandle = :handle';
        $query = $this->entityManager->createQuery($query);
        $query->setParameter('handle', $handle);
        return $query->getOneOrNullResult();
    }



}
