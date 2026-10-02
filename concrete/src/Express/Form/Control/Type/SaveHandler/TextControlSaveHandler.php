<?php
namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Control\TextControl;
use Symfony\Component\HttpFoundation\Request;

class TextControlSaveHandler extends ControlSaveHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Express\Form\Control\Type\SaveHandler\ControlSaveHandler::saveFromRequest()
     *
     * @param \Concrete\Core\Http\Request $request
     */
    public function saveFromRequest(Control $control, Request $request)
    {
        /**
         * @var TextControl $control
         */
        $control = parent::saveFromRequest($control, $request);
        $control->setHeadline($request->request('headline'));
        $control->setBody($request->request('body'));
        return $control;
    }
}
