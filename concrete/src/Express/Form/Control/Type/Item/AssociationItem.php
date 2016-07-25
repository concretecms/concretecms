<?php
namespace Concrete\Core\Express\Form\Control\Type\Item;

use Concrete\Core\Entity\Express\Association;

class AssociationItem implements ItemInterface
{
    protected $association;

    public function __construct(Association $association)
    {
        $this->association = $association;
    }

    public function getDisplayName()
    {
        return $this->association->getFormatter()->getDisplayName();
    }

    public function getIcon()
    {
        return $this->association->getFormatter()->getIcon();
    }

    public function getItemIdentifier()
    {
        return $this->association->getId();
    }
}
