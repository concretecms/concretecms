<?php
namespace Concrete\Core\Express\Association\Formatter;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Express\BaseEntity;

abstract class AbstractFormatter implements FormatterInterface
{
    protected $association;

    public function __construct(Association $association)
    {
        $this->association = $association;
    }

    public function getEntityDisplayName(AssociationControl $control, BaseEntity $entity)
    {
        // Do we have a custom display mask? If so, we try to use that
        if ($control->getAssociationEntityLabelMask()) {
            try {
                return preg_replace_callback('/%(.*?)%/i', function ($matches) use ($entity) {
                    return $entity->getAttribute($matches[1]);
                }, $control->getAssociationEntityLabelMask());
            } catch (\Exception $e) {
            }
        }
        $targetEntity = $this->association->getTargetEntity();
        $attribute = $targetEntity->getAttributes()[0];
        if (is_object($attribute)) {
            $attribute = $attribute->getAttribute();

            return $entity->getAttribute($attribute->getAttributeKeyHandle());
        }
    }

    public function getDisplayName()
    {
        return sprintf(
            '%s > %s', $this->association->getTargetEntity()->getName(),
            $this->association->getSourceEntity()->getName()
        );
    }
}
