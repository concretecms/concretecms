<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\Key\Factory;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Concrete\Core\Entity\Express\Attribute;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ExpressCategory extends AbstractCategory
{

    public function add(AttributeKey $key)
    {
        $attribute = new Attribute();
        $attribute->setAttribute($key);
        $attribute->setEntity($this->getEntity());
        $this->entity->getAttributes()->add($attribute);
        $this->entityManager->persist($this->getEntity());
        $this->entityManager->flush();
    }

}
