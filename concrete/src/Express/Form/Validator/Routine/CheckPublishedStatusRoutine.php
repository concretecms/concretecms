<?php

namespace Concrete\Core\Express\Form\Validator\Routine;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;

class CheckPublishedStatusRoutine implements RoutineInterface
{

    public function validate(ErrorList $error, Form $form, Entry $entry = null)
    {
        $entity = $form->getEntity();
        if (!$entity->isPublished()) {
            $error->add(t('This Express Object has not yet been published. You cannot add entries to an Express object until it is published.'));
            return false;
        }
        return true;
    }


}