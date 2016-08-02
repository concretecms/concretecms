<?php
namespace Concrete\Core\Entity\Express;

use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\ExpressValue;
use Concrete\Core\Entity\Express\Entry\Association as EntryAssociation;
use Concrete\Core\Permission\ObjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineProxies\__CG__\Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Concrete\Core\Entity\Express\EntryRepository")
 * @ORM\Table(name="ExpressEntityEntries")
 */
class Entry implements \JsonSerializable, ObjectInterface
{

    use ObjectTrait;

    public function getPermissionObjectIdentifier()
    {
        return $this->exEntryID;
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\ExpressEntryResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return false;
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return false;
    }

    public function getObjectAttributeCategory()
    {
        $category = \Core::make('\Concrete\Core\Attribute\Category\ExpressCategory', [$this->getEntity()]);
        return $category;
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = $this->getEntity()->getAttributeKeyCategory()->getByHandle($ak);
        }
        $value = false;
        if (is_object($ak)) {
            foreach($this->getAttributes() as $attribute) {
                if ($attribute->getAttributeKey()->getAttributeKeyID() == $ak->getAttributeKeyID()) {
                    return $attribute;
                }
            }
        }

        if ($createIfNotExists) {
            $attributeValue = new ExpressValue();
            $attributeValue->setEntry($this);
            $attributeValue->setAttributeKey($ak);
            return $attributeValue;
        }
    }

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $exEntryID;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $exEntryDateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="entries")
     * @ORM\JoinColumn(name="exEntryEntityID", referencedColumnName="id")
     */
    protected $entity;

    /**
     * @return Entity
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

    public function getID()
    {
        return $this->exEntryID;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\ExpressValue", mappedBy="entry", cascade={"all"})
     * @ORM\JoinColumn(name="exEntryID", referencedColumnName="exEntryID")
     */
    protected $attributes;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Express\Entry\Association", mappedBy="entry", cascade={"all"})
     */
    protected $associations;

    /**
     * @return mixed
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * @param mixed $associations
     */
    public function setAssociations($associations)
    {
        $this->associations = $associations;
    }

    public function getAssociation($handle)
    {
        if ($handle instanceof Association) {
            $handle = $handle->getTargetPropertyName();
        }

        /**
         * @var $association EntryAssociation
         */
        foreach($this->associations as $association) {
            if ($association->getAssociation()->getTargetPropertyName() == $handle) {
                return $association;
            }
        }
    }

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->associations = new ArrayCollection();
        $this->exEntryDateCreated = new \DateTime();
    }

    public function getLabel()
    {
        $firstAttribute = $this->getEntity()->getAttributes()[0];
        if (is_object($firstAttribute)) {
            return $this->getAttribute($firstAttribute);
        }
    }

    public function jsonSerialize()
    {
        $data = array(
            'exEntryID' => $this->getID(),
            'label' => $this->getLabel()
        );
        return $data;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->exEntryDateCreated;
    }

    /**
     * @param mixed $exEntryDateCreated
     */
    public function setDateCreated($exEntryDateCreated)
    {
        $this->exEntryDateCreated = $exEntryDateCreated;
    }


}
