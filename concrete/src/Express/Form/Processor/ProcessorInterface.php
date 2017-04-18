<?php

namespace Concrete\Core\Express\Form\Processor;

use Symfony\Component\HttpFoundation\Request;

interface ProcessorInterface
{

    function getValidator(Request $request);

}