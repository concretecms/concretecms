<?php

namespace Concrete\Core\Express\Form\Processor;

use Concrete\Core\Entity\Express\Entry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

interface ProcessorInterface
{

    const REQUEST_TYPE_ADD = 1;
    const REQUEST_TYPE_UPDATE = 2;

    function getValidator(Request $request);
    function deliverResponse(Entry $entry, $requestType, RedirectResponse $response = null);

}