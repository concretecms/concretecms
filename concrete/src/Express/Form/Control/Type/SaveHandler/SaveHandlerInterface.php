<?php
namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

/**
 * @since 8.0.0
 */
interface SaveHandlerInterface
{
    public function saveFromRequest(Control $control, Request $request);
}
