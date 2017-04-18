<?php

namespace Concrete\Core\Express\Form\Validator\Routine;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\Form\Validator\ValidatorInterface;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Request;

class CheckPermissionsRoutine implements RoutineInterface
{

    public function validate(ErrorList $error, Form $form, $requestType)
    {
        $entity = $form->getEntity();
        $permissions = new \Permissions($entity);
        if ($requestType = ValidatorInterface::REQUEST_TYPE_ADD) {
            $valid = $permissions->canAddExpressEntries();
        } else {
            $valid = $permissions->canEditExpressEntries();
        }
        if (!$valid) {
            $error->add(t('You do not have access to submit this form.'));
        }
        return $valid;
    }


}