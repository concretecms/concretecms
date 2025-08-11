<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressAttributeKeys")
 */
class ExpressKey extends Key
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entity", inversedBy="attributes")
     **/
    protected $entity;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"comment": "Enables SKU-type attributes"})
     */
    protected $eakUnique = false;

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getAttributeCategory()
    {
        return $this->entity->getAttributeKeyCategory();
    }

    /**
     * @return bool
     */
    public function isAttributeKeyUnique(): bool
    {
        return $this->eakUnique;
    }

    /**
     * @param bool $eakUnique
     */
    public function setIsAttributeKeyUnique(bool $eakUnique): void
    {
        $this->eakUnique = $eakUnique;
    }


}
