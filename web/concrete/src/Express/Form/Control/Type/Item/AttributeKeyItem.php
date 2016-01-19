<?php
namespace Concrete\Core\Express\Form\Control\Type\Item;

use Concrete\Core\Entity\Attribute\Key\Key;

class AttributeKeyItem implements ItemInterface
{
    protected $key;

    public function __construct(Key $key)
    {
        $this->key = $key;
    }

    public function getDisplayName()
    {
        return $this->key->getAttributeKeyDisplayName();
    }

    public function getIcon()
    {
        $controller = $this->key->getController();
        $formatter = $controller->getIconFormatter();

        return $formatter->getListIconElement();
    }

    public function getItemIdentifier()
    {
        return $this->key->getAttributeKeyID();
    }
}
