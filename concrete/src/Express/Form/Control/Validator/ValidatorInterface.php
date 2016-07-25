<?php
namespace Concrete\Core\Express\Form\Control\Validator;

use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

interface ValidatorInterface
{
    public function validateRequest(Control $control, Request $request);
}
