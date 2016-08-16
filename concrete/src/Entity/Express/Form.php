<?php
namespace Concrete\Core\Entity\Express;

use Concrete\Core\Export\ExportableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Export\Item\Express\Form as FormExporter;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressForms")
 */
class Form implements \JsonSerializable, ExportableInterface
{
    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="FieldSet", mappedBy="form", cascade={"persist", "remove"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $field_sets;

    /**
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="forms")
     **/
    protected $entity;

    public function __construct()
    {
        $this->field_sets = new ArrayCollection();
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getFieldSets()
    {
        return $this->field_sets;
    }

    /**
     * @param mixed $field_sets
     */
    public function setFieldSets($field_sets)
    {
        $this->field_sets = $field_sets;
    }

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

    public function getControls()
    {
        $controls = array();
        foreach ($this->getFieldSets() as $set) {
            foreach ($set->getControls() as $control) {
                $controls[] = $control;
            }
        }

        return $controls;
    }

    public function jsonSerialize()
    {
        return [
            'exFormName' => $this->getName(),
            'exFormID' => $this->getId()
        ];
    }

    public function getExporter()
    {
        return new FormExporter();
    }
}
