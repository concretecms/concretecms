<?php

namespace Concrete\Core\User\Component;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Request;

/**
 * Methods for use with the ConcreteUserSelect component - primarily for creating on-the-fly access tokens
 */
class UserSelectInstanceFactory implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    const LABEL_FORMAT_AUTO = 'auto'; // uses username_email unless username is disabled on site.
    const LABEL_FORMAT_AUTO_MINIMUM = 'auto_minimum'; // Only uses username and no email - unless username is disabled on site.
    const LABEL_FORMAT_USERNAME = 'username';
    const LABEL_FORMAT_EMAIL = 'email';
    const LABEL_FORMAT_USERNAME_EMAIL = 'username_email';

    protected $tokenService;

    public function __construct(Token $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    protected function getAccessTokenString(string $labelFormat, bool $includeAvatar): string
    {
        return sprintf('user_select:format:%s:avatar:%s', $labelFormat, $includeAvatar ? '1' : '0');
    }

    public function createInstance(string $labelFormat, bool $includeAvatar): UserSelectInstance
    {
        $accessToken = $this->tokenService->generate($this->getAccessTokenString($labelFormat, $includeAvatar));
        $instance = $this->app->make(UserSelectInstance::class);
        $instance->setAccessToken($accessToken);
        $instance->setLabelFormat($labelFormat);
        $instance->setIncludeAvatar($includeAvatar);
        return $instance;
    }

    public function createInstanceFromRequest(Request $request)
    {
        $labelFormat = $request->request->get('labelFormat') ?? '';
        $includeAvatar = $request->request->getBoolean('includeAvatar') ?? false;
        return $this->createInstance($labelFormat, $includeAvatar);
    }

    public function instanceMatchesAccessToken(UserSelectInstance $instance, string $accessToken): bool
    {
        return $this->tokenService->validate(
            $this->getAccessTokenString($instance->getLabelFormat(), $instance->includeAvatar()),
            $accessToken
        );
    }

}
