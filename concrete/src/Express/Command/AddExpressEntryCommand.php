<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Api\Attribute\AttributeValueMap;
use Concrete\Core\Api\Express\Association\AssociationMap;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Foundation\Command\Command;

class AddExpressEntryCommand extends Command
{

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var AttributeValueMap
     */
    protected $attributeMap;

    /**
     * @var AssociationMap
     */
    protected $associationMap;

    /**
     * AddExpressEntryCommand constructor.
     * @param Entity $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     */
    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return AttributeValueMap
     */
    public function getAttributeMap(): ?AttributeValueMap
    {
        return $this->attributeMap;
    }

    /**
     * @param AttributeValueMap $attributeMap
     */
    public function setAttributeMap(AttributeValueMap $attributeMap): void
    {
        $this->attributeMap = $attributeMap;
    }

    /**
     * @return AssociationMap
     */
    public function getAssociationMap(): ?AssociationMap
    {
        return $this->associationMap;
    }

    /**
     * @param AssociationMap $associationMap
     */
    public function setAssociationMap(AssociationMap $associationMap): void
    {
        $this->associationMap = $associationMap;
    }


}
