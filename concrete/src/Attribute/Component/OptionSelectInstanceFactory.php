<?php

namespace Concrete\Core\Attribute\Component;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Validation\CSRF\Token;

class OptionSelectInstanceFactory
{

    protected $tokenService;

    public function __construct(Token $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    protected function getAccessTokenString(Key $key): string
    {
        return sprintf(
            'express_entry_select:attribute:key:%s',
            $key->getAttributeKeyHandle()
        );
    }

    public function createInstance(Key $key): OptionSelectInstance
    {
        $accessToken = $this->tokenService->generate($this->getAccessTokenString($key));
        $instance = new OptionSelectInstance();
        $instance->setAccessToken($accessToken);
        $instance->setAttributeKey($key);
        return $instance;
    }

    public function instanceMatchesAccessToken(OptionSelectInstance $instance, string $accessToken): bool
    {
        return $this->tokenService->validate(
            $this->getAccessTokenString($instance->getAttributeKey()),
            $accessToken
        );
    }

}
