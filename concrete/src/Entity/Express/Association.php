<?php
namespace Concrete\Core\Entity\Express;

use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Export\Item\Express\Association as AssociationExporter;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="ExpressEntityAssociations")
 */
abstract class Association implements ExportableInterface
{
    abstract public function getAssociationBuilder();

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Entity")
     **/
    protected $source_entity;

    /**
     * @ORM\OneToOne(targetEntity="Entity")
     **/
    protected $target_entity;

    /**
     * @ORM\Column(type="boolean")
     **/
    protected $is_owned_by_association = false;

    /**
     * @ORM\Column(type="boolean")
     **/
    protected $is_owning_association = false;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Express\Entry\Association",
     *     mappedBy="association", cascade={"remove"})
     */
    protected $entry;


    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Express\Control\AssociationControl", mappedBy="association", cascade={"remove"})
     */
    protected $controls;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $target_property_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $inversed_by_property_name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function isOwningAssociation()
    {
        return $this->is_owning_association;
    }

    /**
     * @param mixed $is_owning_association
     */
    public function setIsOwningAssociation($is_owning_association)
    {
        $this->is_owning_association = $is_owning_association;
    }

    /**
     * @return mixed
     */
    public function isOwnedByAssociation()
    {
        return $this->is_owned_by_association;
    }

    /**
     * @param mixed $is_owned_by_association
     */
    public function setIsOwnedByAssociation($is_owned_by_association)
    {
        $this->is_owned_by_association = $is_owned_by_association;
    }

    /**
     * @return mixed
     */
    public function getTargetPropertyName()
    {
        return $this->target_property_name;
    }

    /**
     * @param mixed $name
     */
    public function setTargetPropertyName($target_property_name)
    {
        $this->target_property_name = $target_property_name;
    }

    /**
     * @return mixed
     */
    public function getInversedByPropertyName()
    {
        return $this->inversed_by_property_name;
    }

    /**
     * @param mixed $inversed_by_property_name
     */
    public function setInversedByPropertyName($inversed_by_property_name)
    {
        $this->inversed_by_property_name = $inversed_by_property_name;
    }

    /**
     * @return mixed
     */
    public function getSourceEntity()
    {
        return $this->source_entity;
    }

    /**
     * @param mixed $source_entity
     */
    public function setSourceEntity($source_entity)
    {
        $this->source_entity = $source_entity;
    }

    /**
     * @return mixed
     */
    public function getTargetEntity()
    {
        return $this->target_entity;
    }

    /**
     * @param mixed $target_entity
     */
    public function setTargetEntity($target_entity)
    {
        $this->target_entity = $target_entity;
    }

    public function getComputedTargetPropertyName()
    {
        if ($this->getTargetPropertyName()) {
            return $this->getTargetPropertyName();
        } else {
            return uncamelcase($this->getTargetEntity()->getName());
        }
    }

    public function getComputedInversedByPropertyName()
    {
        if ($this->getInversedByPropertyName()) {
            return $this->getInversedByPropertyName();
        } else {
            return uncamelcase($this->getSourceEntity()->getName());
        }
    }

    abstract public function getFormatter();
    abstract public function getSaveHandler();

    public function getExporter()
    {
        return new AssociationExporter();
    }
}
