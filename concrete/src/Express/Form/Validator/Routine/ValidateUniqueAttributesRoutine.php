<?php

namespace Concrete\Core\Express\Form\Validator\Routine;

use Concrete\Core\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Entity\Attribute\Key\ExpressKey as ExpressKeyEntity;

class ValidateUniqueAttributesRoutine implements RoutineInterface
{

    public function validate(ErrorList $error, Form $form, $requestType)
    {
        $entity = $form->getEntity();
        $attributes = $entity->getAttributes();
        foreach ($attributes as $key) {
            /**
             * @var $key ExpressKeyEntity
             */
            if ($key->isAttributeKeyUnique()) {
                $controller = $key->getController();
                $value = $controller->createAttributeValueFromRequest();
                if ($value) {
                    $valueString = (string) $value;
                    if ($valueString) {
                        // If you leave values blank we allow this, because there is a separate validation routine
                        // for emptiness.
                        dd($value);
                    }
                }
            }
        }


        if (!$valid) {
            $error->add(t('You do not have access to submit this form.'));
        }
        return $valid;
    }


}