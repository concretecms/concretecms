<?php
namespace Concrete\Core\Api\OAuth\Command;

use Concrete\Core\Foundation\Command\Command;


class CreateOAuthClientCommand extends Command
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $redirect;

    /**
     * @var int|null
     */
    protected $consentType;

    /**
     * @var bool
     */
    protected $enableDocumentation = false;

    /**
     * @var bool
     */
    protected $hasCustomScopes = false;

    /**
     * @var string[]
     */
    protected $customScopes = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRedirect(): string
    {
        return $this->redirect;
    }

    /**
     * @param string $redirect
     */
    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
    }

    /**
     * @return int|null
     */
    public function getConsentType()
    {
        return $this->consentType;
    }

    /**
     * @param int|null $consentType
     */
    public function setConsentType($consentType): void
    {
        $this->consentType = $consentType;
    }

    /**
     * @return bool
     */
    public function isEnableDocumentation(): bool
    {
        return $this->enableDocumentation;
    }

    /**
     * @param bool $enableDocumentation
     */
    public function setEnableDocumentation(bool $enableDocumentation): void
    {
        $this->enableDocumentation = $enableDocumentation;
    }

    /**
     * @return bool
     */
    public function hasCustomScopes(): bool
    {
        return $this->hasCustomScopes;
    }

    /**
     * @param bool $hasCustomScopes
     */
    public function setHasCustomScopes(bool $hasCustomScopes): void
    {
        $this->hasCustomScopes = $hasCustomScopes;
    }

    /**
     * @return string[]
     */
    public function getCustomScopes(): array
    {
        return $this->customScopes;
    }

    /**
     * @param string[] $customScopes
     */
    public function setCustomScopes(array $customScopes): void
    {
        $this->customScopes = $customScopes;
    }



}