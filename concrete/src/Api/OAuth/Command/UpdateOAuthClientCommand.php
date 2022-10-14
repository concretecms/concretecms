<?php
namespace Concrete\Core\Api\OAuth\Command;

use Concrete\Core\Foundation\Command\Command;


class UpdateOAuthClientCommand extends CreateOAuthClientCommand
{

    /**
     * @var string
     */
    protected $clientIdentifier;

    /**
     * @param string $clientIdentifier
     */
    public function __construct(string $clientIdentifier)
    {
        $this->clientIdentifier = $clientIdentifier;
    }

    /**
     * @return string
     */
    public function getClientIdentifier(): string
    {
        return $this->clientIdentifier;
    }


}