<?php

namespace Concrete\Core\Express\Form\Validator\Routine;

use Concrete\Core\Attribute\FilterableByValueInterface;
use Concrete\Core\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Entity\Attribute\Key\ExpressKey as ExpressKeyEntity;
use Concrete\Core\Express\EntryList;

class ValidateUniqueAttributesRoutine implements RoutineInterface
{

    public function validate(ErrorList $error, Form $form, ?Entry $entry = null)
    {
        $valid = true;
        $entity = $form->getEntity();
        $attributes = $entity->getAttributes();
        foreach ($attributes as $key) {
            /**
             * @var ExpressKeyEntity $key
             */
            if ($key->isAttributeKeyUnique()) {
                $controller = $key->getController();
                if ($controller instanceof FilterableByValueInterface) {
                    // Attribute types not respecting the contract of createAttributeValueFromRequest() may return false (for example
                    // when returning createAttributeValue() without implementing saveValue()) or null (when returning nothing)
                    /** @var \Concrete\Core\Entity\Attribute\Value\Value\AbstractValue|\Concrete\Core\Attribute\Value\EmptyRequestAttributeValue|false|null $value */
                    $value = $controller->createAttributeValueFromRequest();
                    if ($value && method_exists($value, '__toString')) {
                        $valueString = (string) $value;
                        if ($valueString) {
                            // If you leave values blank we allow this, because there is a separate validation routine
                            // for emptiness.
                            $list = new EntryList($entity);
                            $list->ignorePermissions();
                            $controller->filterByExactValue($list, $value);
                            if ($entry != null) {
                                $list->getQueryObject()->andWhere('e.exEntryID != :thisEntryID');
                                $list->getQueryObject()->setParameter('thisEntryID', $entry->getID());
                            }
                            $results = $list->getResults();
                            if (count($results) > 0) {
                                $error->add(t('The current value for "%s" must be unique and is currently assigned to another entry.', $key->getAttributeKeyDisplayName('text')));
                                $valid = false;
                            }
                        }
                    }
                }
            }
        }

        return $valid;
    }


}