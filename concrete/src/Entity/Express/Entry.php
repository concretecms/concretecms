<?php
namespace Concrete\Core\Entity\Express;

use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\ExpressValue;
use Concrete\Core\Entity\Express\Entry\Association as EntryAssociation;
use Concrete\Core\Entity\Express\Entry\ManyAssociation;
use Concrete\Core\Entity\Express\Entry\OneAssociation;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Express\Entry\Formatter\EntryFormatterInterface;
use Concrete\Core\Express\EntryBuilder\AssociationBuilder;
use Concrete\Core\Export\Item\Express\Entry as EntryExporter;
use Concrete\Core\Express\EntryBuilder\AssociationUpdater;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Concrete\Core\Entity\Express\EntryRepository")
 * @ORM\Table(name="ExpressEntityEntries")
 * @ORM\EntityListeners({"\Concrete\Core\Express\Entry\Listener"})
 */
class Entry implements \JsonSerializable, PermissionObjectInterface, AttributeObjectInterface, ExportableInterface
{

    use ObjectTrait;

    protected $entryFormatter;

    /**
     * Returns either an attribute (if passed an attribute handle) or the content
     * of an association, if it matches an association.
     * @param $nm
     * @param $a
     * @return $mixed
     */
    public function __call($nm, $a)
    {
        if (substr($nm, 0, 3) == 'get') {
            $nm = preg_replace('/(?!^)[[:upper:]]/', '_\0', $nm);
            $nm = strtolower($nm);
            $identifier = str_replace('get_', '', $nm);

            // check for association
            $association = $this->getAssociation($identifier);
            if ($association instanceof ManyAssociation) {
                $collection = $association->getSelectedEntries();
                if (is_object($collection)) {
                    return $collection->toArray();
                } else {
                    return array();
                }
            } else if ($association instanceof OneAssociation) {
                return $association->getSelectedEntry();
            }

            // Assume attribute otherwise
            return $this->getAttribute($identifier);
        }

        if (substr($nm, 0, 3) == 'set') {
            $nm = preg_replace('/(?!^)[[:upper:]]/', '_\0', $nm);
            $nm = strtolower($nm);
            $identifier = str_replace('set_', '', $nm);

            // Assume attribute otherwise
            $this->setAttribute($identifier, $a[0]);
        }

        return null;
    }

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
     * @ORM\Column(type="integer")
     */
    protected $exEntryDisplayOrder = 0;

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
     * @param mixed $exEntryID
     */
    public function setID($exEntryID)
    {
        $this->exEntryID = $exEntryID;
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
     * @return mixed
     */
    public function getEntryDisplayOrder()
    {
        return $this->exEntryDisplayOrder;
    }

    /**
     * @param mixed $exEntryDisplayOrder
     */
    public function setEntryDisplayOrder($exEntryDisplayOrder)
    {
        $this->exEntryDisplayOrder = $exEntryDisplayOrder;
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
     * @ORM\ManyToMany(targetEntity="\Concrete\Core\Entity\Express\Entry\Association", mappedBy="selectedEntries")
     * @ORM\JoinTable(name="ExpressEntityAssociationSelectedEntries",
     * joinColumns={@ORM\JoinColumn(name="exSelectedEntryID", referencedColumnName="exEntryID")},
     * inverseJoinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id")  }
     * )
     */
    protected $containing_associations;

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


    /**
     * @deprecated See \Concrete\Core\Entity\Express\Entry::getEntryAssociation
     */
    public function getAssociation($handle)
    {
        if ($handle instanceof Association) {
            return $this->getEntryAssociation($handle);
        }

        /**
         * @var $entryAssociation EntryAssociation
         */
        foreach ($this->associations as $entryAssociation) {
            if ($entryAssociation->getAssociation()->getTargetPropertyName() === $handle) {
                return $entryAssociation;
            }
        }
    }

    /**
     * Get the EntryAssociation for a given association
     *
     * @param \Concrete\Core\Entity\Express\Association $association
     * @return \Concrete\Core\Entity\Express\Entry\Association|null
     */
    public function getEntryAssociation(Association $association)
    {
        $id = $association->getId();

        /**
         * @var $entryAssociation EntryAssociation
         */
        foreach ($this->associations as $entryAssociation) {
            if ($entryAssociation->getAssociation()->getId() === $id) {
                return $entryAssociation;
            }
        }

        return null;
    }

    public function getOwnedByEntry()
    {
        foreach($this->associations as $association) {
            if ($association->getAssociation()->isOwnedByAssociation()) {
                return $association->getEntry();
            }
        }
    }
    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->associations = new ArrayCollection();
        $this->containing_associations = new ArrayCollection();
        $this->exEntryDateCreated = new \DateTime();
    }

    public function getLabel()
    {
        if (!$this->entryFormatter) {
            $this->entryFormatter = Application::getFacadeApplication()->make(EntryFormatterInterface::class);
        }

        if ($mask = $this->getEntity()->getLabelMask()) {
            $name = $this->entryFormatter->format($mask, $this);
        }

        if (!$name) {
            $name = $this->entryFormatter->getLabel($this);
        }

        return $name;
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
     * @return \DateTime
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

    public function associateEntries()
    {
        return \Core::make(AssociationUpdater::class, ['entry' => $this]);
    }

    public function getExporter()
    {
        return \Core::make(EntryExporter::class);
    }

}
