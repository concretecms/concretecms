<?php

namespace Concrete\Core\Express\Form\Control\Validator;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Error\ErrorList\ErrorList;
use Symfony\Component\HttpFoundation\Request;

class AssociationControlValidator implements ValidatorInterface
{
    /**
     * @var ErrorList
     */
    protected $errorList;

    public function __construct(ErrorList $errorList)
    {
        $this->errorList = $errorList;
    }

    public function validateRequest(Control $control, Request $request)
    {
        if ($control->isRequired()) {
            $associationValue = $request->request->get('express_association_' . $control->getId());
            if (!$associationValue) {
                /**
                 * @var AssociationControl
                 */
                $this->errorList->add(t('You must select a valid %s', $control->getAssociation()->getTargetEntity()->getName()));
            }
        }

        return $this->errorList;
    }
}
