<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Api\Express\Association\AssociationMap;
use Concrete\Core\Api\Attribute\AttributeValueMap;

class UpdateExpressEntryCommand extends Command
{

    /**
     * @var Entry
     */
    protected $entry;

    /**
     * @var AttributeValueMap
     */
    protected $attributeMap;

    /**
     * @var AssociationMap
     */
    protected $associationMap;

    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return Entry
     */
    public function getEntry(): Entry
    {
        return $this->entry;
    }

    /**
     * @param Entry $entry
     */
    public function setEntry(Entry $entry): void
    {
        $this->entry = $entry;
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
