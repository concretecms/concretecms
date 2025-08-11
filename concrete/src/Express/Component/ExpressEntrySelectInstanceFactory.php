<?php

namespace Concrete\Core\Express\Component;

use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Request;

class ExpressEntrySelectInstanceFactory
{
    
    protected $tokenService;

    public function __construct(Token $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    protected function getAccessTokenString(string $entityHandle): string
    {
        return sprintf('express_entry_select:entity:%s', $entityHandle);
    }

    public function createInstance(string $entityHandle): ExpressEntrySelectInstance
    {
        $accessToken = $this->tokenService->generate($this->getAccessTokenString($entityHandle));
        $instance = new ExpressEntrySelectInstance();
        $instance->setAccessToken($accessToken);
        $instance->setEntityHandle($entityHandle);
        return $instance;
    }

    public function createInstanceFromRequest(Request $request)
    {
        return $this->createInstance((string) $request->request->get('entity'));
    }

    public function instanceMatchesAccessToken(ExpressEntrySelectInstance $instance, string $accessToken): bool
    {
        return $this->tokenService->validate(
            $this->getAccessTokenString($instance->getEntityHandle()),
            $accessToken
        );
    }

}
