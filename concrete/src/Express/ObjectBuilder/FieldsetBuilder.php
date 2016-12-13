<?php
namespace Concrete\Core\Express\ObjectBuilder;

use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\TextControl;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Express\ObjectBuilder;
use Doctrine\ORM\Id\UuidGenerator;

class FieldsetBuilder
{

    protected $fieldsetName;
    protected $controls;

    public function __construct($fieldsetName)
    {
        $this->fieldsetName = $fieldsetName;
    }

    public function build(ObjectBuilder $builder)
    {
        $fieldset = new FieldSet();
        $fieldset->setTitle($this->fieldsetName);
        foreach($this->controls as $control) {
            $control = $control->build($builder);
            $control->setId((new UuidGenerator())->generate($builder->getEntityManager(), $control));
            $fieldset->getControls()->add($control);
        }
        return $fieldset;
    }

    public function addAttributeKeyControl($akHandle)
    {
        $control = new AttributeKeyControl();
        $key = new ExpressKey();
        $key->setAttributeKeyHandle($akHandle);
        $control->setAttributeKey($key);
        $this->controls[] = $control;
        return $this;
    }

    public function addTextControl($headline, $body)
    {
        $control = new TextControl();
        $control->setHeadline($headline);
        $control->setBody($body);
        $this->controls[] = $control;
        return $this;
    }

}
