<?php

namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Express\BaseEntity;
use Concrete\Core\Express\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

interface SaveHandlerInterface
{

    public function saveFromRequest(ObjectManager $manager, BaseEntity $entity, Request $request);

}