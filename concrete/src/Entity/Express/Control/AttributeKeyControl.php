<?php
namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Export\Item\AttributeKey;
use Concrete\Core\Express\ObjectBuilder;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Form\Context\Registry\ControlRegistry;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressFormFieldSetAttributeKeyControls")
 */
class AttributeKeyControl extends Control
{
    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @ORM\JoinColumn(name="akID", referencedColumnName="akID")
     */
    protected $attribute_key;

    /**
     * @return Key
     */
    public function getAttributeKey()
    {
        return $this->attribute_key;
    }

    /**
     * @param mixed $attribute_key
     */
    public function setAttributeKey($attribute_key)
    {
        $this->attribute_key = $attribute_key;
    }

    public function getControlLabel()
    {
        return $this->getAttributeKey()->getAttributeKeyDisplayName();
    }

    public function getType()
    {
        return 'attribute_key';
    }

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['attributeType'] = $this->getAttributeKey()->getAttributeTypeHandle();
        return $data;
    }

    public function getExporter()
    {
        return new \Concrete\Core\Export\Item\Express\Control\AttributeKeyControl();
    }

    public function getControlView(ContextInterface $context)
    {
        $registry = \Core::make(ControlRegistry::class);
        return $registry->getControlView($context, 'express_control_attribute_key', [
            $this
        ]);
    }

    public function build(ObjectBuilder $builder)
    {
        // before we have built the object we have a proxy attribute key
        // object that just has the attribute key handle
        $akHandle = $this->attribute_key->getAttributeKeyHandle();
        foreach($builder->getEntity()->getAttributes() as $ak) {
            if ($akHandle == $ak->getAttributeKeyHandle()) {
                $this->setAttributeKey($ak);
            }
        }
        return $this;
    }
}
