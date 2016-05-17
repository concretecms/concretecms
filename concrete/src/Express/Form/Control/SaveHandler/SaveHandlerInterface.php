<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

interface SaveHandlerInterface
{
    public function saveFromRequest(Control $control, Entry $entry, Request $request);
}
