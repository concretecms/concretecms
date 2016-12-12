<?php
namespace Concrete\Core\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

interface RequestEventInterface
{
    /**
     * @param Request $request
     */
    public function setRequest(SymfonyRequest $request);

    /** @return \Symfony\Component\HttpFoundation\Request */
    public function getRequest();
}
