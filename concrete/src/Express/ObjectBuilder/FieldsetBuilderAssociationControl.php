<?php
namespace Concrete\Core\Express\ObjectBuilder;

use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\TextControl;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Express\ObjectBuilder;
use Doctrine\ORM\Id\UuidGenerator;

class FieldsetBuilderAssociationControl
{

    protected $target_property_name;

    /**
     * FieldsetBuilderAssociationControl constructor.
     * @param $target_property_name
     */
    public function __construct($target_property_name = null)
    {
        $this->target_property_name = $target_property_name;
    }

    /**
     * @return mixed
     */
    public function getTargetPropertyName()
    {
        return $this->target_property_name;
    }

    /**
     * @param mixed $target_property_name
     */
    public function setTargetPropertyName($target_property_name)
    {
        $this->target_property_name = $target_property_name;
    }

    public function build(ObjectBuilder $builder)
    {
        $entity = $builder->getEntity();
        foreach($entity->getAssociations() as $association) {
            if ($association->getTargetPropertyName() == $this->getTargetPropertyName()) {
                $control = new AssociationControl();
                $control->setAssociation($association);
                return $control;
            }
        }

        throw new \Exception(t("Unable to build association control for target property name '%s'", $this->getTargetPropertyName()));
    }



}
