<?php
namespace Concrete\Core\Entity\Express\Control;

use Concrete\Controller\Element\Dashboard\Express\Control\Options;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Express\Form\Control\Type\SaveHandler\ControlSaveHandler;
use Concrete\Core\Export\Item\Express\Control as ControlExporter;
use Concrete\Core\Express\Form\Control\Type\TypeInterface;
use Concrete\Core\Express\ObjectBuilder;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Form\Control\ControlInterface;
use Concrete\Core\Import\ImportableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="ExpressFormFieldSetControls")
 */
abstract class Control implements \JsonSerializable, ExportableInterface, ControlInterface
{
    /**
     * @ORM\Id @ORM\Column(type="guid")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_required = false;

    /**
     * @ORM\Column(type="integer")
     */
    protected $position = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $custom_label;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\FieldSet")
     **/
    protected $field_set;

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
    public function getCustomLabel()
    {
        return $this->custom_label;
    }

    /**
     * @param mixed $custom_label
     */
    public function setCustomLabel($custom_label)
    {
        $this->custom_label = $custom_label;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getFieldSet()
    {
        return $this->field_set;
    }

    /**
     * @param mixed $field_set
     */
    public function setFieldSet($field_set)
    {
        $this->field_set = $field_set;
    }

    public function getControlOptionsController()
    {
        return new Options($this);
    }

    public function getControlSaveHandler()
    {
        return new ControlSaveHandler();
    }

    abstract public function getControlLabel();

    abstract public function getType();

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'displayLabel' => $this->getDisplayLabel(),
            'isRequired' => $this->isRequired(),
        ];
    }

    /**
     * @return mixed
     */
    public function isRequired()
    {
        return $this->is_required;
    }

    /**
     * @param mixed $is_required
     */
    public function setIsRequired($is_required)
    {
        $this->is_required = $is_required;
    }

    public function getDisplayLabel()
    {
        return $this->getCustomLabel() ?
            $this->getCustomLabel() :
            $this->getControlLabel();
    }

    /**
     * @return TypeInterface
     */
    public function getControlType()
    {
        $manager = \Core::make('express/control/type/manager');

        return $manager->driver($this->getType());
    }

    public function build(ObjectBuilder $builder)
    {
        return $this;
    }

}
