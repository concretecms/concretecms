<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\ExpressValue;
use Concrete\Core\Entity\Express\Entry\Association as EntryAssociation;
use Concrete\Core\Entity\Express\Entry\ManyAssociation;
use Concrete\Core\Entity\Express\Entry\OneAssociation;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Express\Entry\Formatter\EntryFormatterInterface;
use Concrete\Core\Export\Item\Express\Entry as EntryExporter;
use Concrete\Core\Express\EntryBuilder\AssociationUpdater;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Permission\Assignment\ExpressEntryAssignment;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Permission\Response\ExpressEntryResponse;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Concrete\Core\Entity\Express\EntryRepository")
 * @ORM\Table(name="ExpressEntityEntries",
 *  *     indexes={
 *         @ORM\Index(name="resultsNodeID", columns={"resultsNodeID"}),
 *         @ORM\Index(name="createdSort", columns={"exEntryDateCreated"}),
 *         @ORM\Index(name="modifiedSort", columns={"exEntryDateModified"})
 *     }
 * )
 * @ORM\EntityListeners({"\Concrete\Core\Express\Entry\Listener"})
 */
class Entry implements \JsonSerializable, PermissionObjectInterface, AttributeObjectInterface, ExportableInterface
{
    use ObjectTrait;

    protected $entryFormatter;

    /**
     * Returns either an attribute (if passed an attribute handle) or the content
     * of an association, if it matches an association.
     *
     * @param $nm
     * @param $a
     *
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
                    return [];
                }
            } elseif ($association instanceof OneAssociation) {
                return $association->getSelectedEntry();
            }

            // Assume attribute otherwise
            return $this->getAttribute($identifier);
        }

        if (substr($nm, 0, 3) == 'set') {
            $nm = preg_replace('/(?!^)[[:upper:]]/', '_\0', $nm);
            $nm = strtolower($nm);
            $identifier = substr($nm, 4);

            // Assume attribute otherwise
            $this->setAttribute($identifier, $a[0]);
        }

        return null;
    }

    /**
     * Checks if this Entry's entity handle is the same as the one specified.
     *
     * @param $entityHandle
     *
     * @return bool
     */
    public function is($entityHandle)
    {
        return $this->getEntity()->getHandle() == $entityHandle;
    }

    /**
     * Returns the ID of this Entry.
     *
     * @return mixed
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->exEntryID;
    }

    /**
     * @return string
     */
    public function getPermissionResponseClassName()
    {
        return ExpressEntryResponse::class;
    }

    /**
     * @return bool
     */
    public function getPermissionAssignmentClassName()
    {
        return ExpressEntryAssignment::class;
    }

    /**
     * @return bool
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'express_entry';
    }

    /**
     * @return \Concrete\Core\Attribute\Category\CategoryInterface
     */
    public function getObjectAttributeCategory()
    {
        $category = app(ExpressCategory::class, ['entity' => $this->getEntity()]);
        return $category;
    }

    /**
     * @param \Concrete\Core\Attribute\AttributeKeyInterface|string $ak
     * @param bool $createIfNotExists
     *
     * @return \Concrete\Core\Attribute\AttributeValueInterface|ExpressValue|null
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = $this->getEntity()->getAttributeKeyCategory()->getAttributeKeyByHandle($ak);
        }
        $value = false;
        if (is_object($ak)) {
            foreach ($this->getAttributes() as $attribute) {
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
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $author;

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $exEntryDateModified;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $publicIdentifier;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $resultsNodeID;

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

    /**
     * @return mixed
     */
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
     * @return \Concrete\Core\Entity\Express\Entry\Association[]
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
     * @return mixed
     */
    public function getResultsNodeID()
    {
        return $this->resultsNodeID;
    }

    /**
     * @param mixed $resultsNodeID
     */
    public function setResultsNodeID($resultsNodeID): void
    {
        $this->resultsNodeID = $resultsNodeID;
    }


    /**
     * @param $handle
     *
     * @return EntryAssociation|null
     */
    public function getAssociation($handle)
    {
        if ($handle instanceof Association) {
            return $this->getEntryAssociation($handle);
        }

        /**
         * @var EntryAssociation $entryAssociation
         */
        foreach ($this->associations as $entryAssociation) {
            if ($entryAssociation->getAssociation()->getTargetPropertyName() === $handle) {
                return $entryAssociation;
            }
        }
    }

    /**
     * Get the EntryAssociation for a given association.
     *
     * @param \Concrete\Core\Entity\Express\Association $association
     *
     * @return \Concrete\Core\Entity\Express\Entry\Association|null
     */
    public function getEntryAssociation(Association $association)
    {
        $id = $association->getId();

        /**
         * @var EntryAssociation $entryAssociation
         */
        foreach ($this->associations as $entryAssociation) {
            if ($entryAssociation->getAssociation()->getId() === $id) {
                return $entryAssociation;
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getOwnedByEntry()
    {
        foreach ($this->associations as $association) {
            if ($association->getAssociation()->isOwnedByAssociation() && $association instanceof OneAssociation) {
                return $association->getSelectedEntry();
            }
        }
    }

    /**
     * Entry constructor.
     */
    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->associations = new ArrayCollection();
        $this->containing_associations = new ArrayCollection();
        $this->exEntryDateCreated = new \DateTime();
        $this->exEntryDateModified = new \DateTime();
    }

    /**
     * Updates the entity dateModified field
     * Normally called by ExpressValue Entity.
     */
    public function updateDateModified()
    {
        $this->exEntryDateModified = new \DateTime();
    }

    /**
     * Formats the label of this entry to the mask (e.g. %product-name%) or the standard format.
     *
     * @return mixed
     */
    public function getLabel(): string
    {
        if (!$this->entryFormatter) {
            $this->entryFormatter = Application::getFacadeApplication()->make(EntryFormatterInterface::class);
        }

        if ($mask = $this->getEntity()->getLabelMask()) {
            $name = $this->entryFormatter->format($mask, $this);
        }

        if (!isset($name)) {
            $name = $this->entryFormatter->getLabel($this);
        }

        return (string)$name;
    }

    public function getURL()
    {
        return (string)Url::to("/dashboard/express/entries/view_entry/", $this->getID());
    }

    /**
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $app = Application::getFacadeApplication();
        /** @var Date $dateHelper */
        $dateHelper = $app->make(Date::class);
        $data = [
            'exEntryID' => $this->getID(),
            'label' => $this->getLabel(),
            'url' => $this->getURL(),
            'exEntryDateCreated' => $dateHelper->formatDateTime($this->getDateCreated()),
            'exEntryDateModified' => $dateHelper->formatDateTime($this->getDateModified()),
        ];

        return $data;
    }

    /**
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->exEntryDateModified;
    }

    /**
     * @param mixed $exEntryDateModified
     */
    public function setDateModified($exEntryDateModified)
    {
        $this->exEntryDateModified = $exEntryDateModified;
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

    /**
     * @return mixed
     */
    public function associateEntries()
    {
        return \Core::make(AssociationUpdater::class, ['entry' => $this]);
    }

    /**
     * @return \Concrete\Core\Export\Item\ItemInterface
     */
    public function getExporter()
    {
        return \Core::make(EntryExporter::class);
    }

    /**
     * @return \Concrete\Core\Entity\User\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getPublicIdentifier()
    {
        return $this->publicIdentifier;
    }

    /**
     * @param mixed $publicIdentifier
     */
    public function setPublicIdentifier($publicIdentifier)
    {
        $this->publicIdentifier = $publicIdentifier;
    }


}
