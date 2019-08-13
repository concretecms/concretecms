<?php
namespace Concrete\Core\Express\Form\Control\Validator;

use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

/**
 * @since 8.0.0
 */
interface ValidatorInterface
{
    public function validateRequest(Control $control, Request $request);
}
