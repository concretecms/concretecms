<?php

namespace Concrete\Core\Page\Component;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Request;

/**
 * Methods for use with the ConcretePageSelect component - primarily for creating on-the-fly access tokens
 */
class PageSelectInstanceFactory
{

    protected $tokenService;

    public function __construct(Token $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    protected function getAccessTokenString(): string
    {
        return sprintf('page_select');
    }

    public function createInstance(): PageSelectInstance
    {
        $accessToken = $this->tokenService->generate($this->getAccessTokenString());
        $instance = new PageSelectInstance();
        $instance->setAccessToken($accessToken);
        return $instance;
    }

    public function createInstanceFromRequest(Request $request)
    {
        return $this->createInstance();
    }

    public function instanceMatchesAccessToken(PageSelectInstance $instance, string $accessToken): bool
    {
        return $this->tokenService->validate(
            $this->getAccessTokenString(),
            $accessToken
        );
    }

}
