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
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entity")
     **/
    protected $entity;

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
}
