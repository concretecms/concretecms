<?php
namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Control\TextControl;
use Symfony\Component\HttpFoundation\Request;

class TextControlSaveHandler extends ControlSaveHandler
{
    public function saveFromRequest(Control $control, Request $request)
    {
        /**
         * @var $control TextControl
         */
        $control = parent::saveFromRequest($control, $request);
        $control->setHeadline($request->request('headline'));
        $control->setBody($request->request('body'));
        return $control;
    }
}
