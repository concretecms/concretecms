<?php

namespace Concrete\Core\User\Component;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Request;

class AvatarCropperInstanceFactory implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    protected $tokenService;

    protected $config;

    public function __construct(Token $tokenService, Repository $config)
    {
        $this->tokenService = $tokenService;
        $this->config = $config;
    }

    protected function getAccessTokenString(): string
    {
        return sprintf('avatar_cropper');
    }

    public function createInstance(): AvatarCropperInstance
    {
        $width = (int) $this->config->get('concrete.icons.user_avatar.width');
        $height = (int) $this->config->get('concrete.icons.user_avatar.height');
        $resolutionMultiplier = (int) $this->config->get('concrete.icons.user_avatar.resolution', 1);
        $width = $width * $resolutionMultiplier;
        $height = $height * $resolutionMultiplier;

        $accessToken = $this->tokenService->generate($this->getAccessTokenString());
        $instance = $this->app->make(AvatarCropperInstance::class);
        $instance->setAccessToken($accessToken);
        $instance->setWidth($width);
        $instance->setHeight($height);
        return $instance;
    }

    public function createInstanceFromRequest(Request $request)
    {
        return $this->createInstance();
    }

    public function instanceMatchesAccessToken(AvatarCropperInstance $instance, string $accessToken): bool
    {
        return $this->tokenService->validate(
            $this->getAccessTokenString(),
            $accessToken
        );
    }

}
