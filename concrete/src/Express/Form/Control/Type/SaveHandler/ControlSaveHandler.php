<?php
namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

class ControlSaveHandler implements SaveHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Express\Form\Control\Type\SaveHandler\SaveHandlerInterface::saveFromRequest()
     *
     * @param \Concrete\Core\Http\Request $request
     */
    public function saveFromRequest(Control $control, Request $request)
    {
        $control->setIsRequired((bool) $request->request("isRequired"));
        $control->setCustomLabel($request->request("customLabel"));

        return $control;
    }
}
