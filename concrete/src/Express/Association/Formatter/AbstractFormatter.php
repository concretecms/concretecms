<?php
namespace Concrete\Core\Express\Association\Formatter;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Entry;

abstract class AbstractFormatter implements FormatterInterface
{
    protected $association;

    public function __construct(Association $association)
    {
        $this->association = $association;
    }

    public function getEntryDisplayName(AssociationControl $control, Entry $entry)
    {
        // Do we have a custom display mask? If so, we try to use that
        if ($control->getAssociationEntityLabelMask()) {
            try {
                return preg_replace_callback('/%(.*?)%/i', function ($matches) use ($entry) {
                    $attribute = $entry->getAttribute($matches[1]);
                    if ($attribute) {
                        return $attribute;
                    }

                    $association = $entry->getAssociation($matches[1]);
                    if (is_object($association)) {
                        return $association->getSelectedEntry()->getLabel();
                    }
                }, $control->getAssociationEntityLabelMask());
            } catch (\Exception $e) {
            }
        }

        $entity = $entry->getEntity();

        /** @var \Concrete\Core\Entity\Attribute\Key\ExpressKey[] $attributes */
        $attributes = $entity->getAttributes();

        if ($keyId = $entity->getDisplayAttributeKey()) {

            foreach ($attributes as $key) {
                if ($key->getAttributeKeyID() == $keyId) {
                    $attribute = $key;
                    break;
                }
            }
        } else {
            foreach ($attributes as $attribute) {
                if ($attribute->getAttributeType()->getAttributeTypeHandle() === 'text') {
                    break;
                }
            }
        }

        if (is_object($attribute)) {
            $result = $entry->getAttribute($attribute);
            if (!$result || (!is_string($result) && !is_callable($result, '__toString'))) {
                return t('[Invalid Name]');
            }

            return $result;
        }
    }

    public function getDisplayName()
    {
        return sprintf(
            '%s > %s', $this->association->getSourceEntity()->getEntityDisplayName(),
            $this->association->getTargetEntity()->getEntityDisplayName()
        );
    }
}
