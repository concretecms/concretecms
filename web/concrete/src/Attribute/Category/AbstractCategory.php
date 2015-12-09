<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Key\Factory;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Concrete\Core\Entity\Express\Attribute;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCategory implements CategoryInterface
{

    protected $entityManager;
    protected $entity;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    }
