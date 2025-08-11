<?php
namespace Concrete\Core\Api\OAuth\Command;

use Concrete\Core\Foundation\Command\Command;


class DeleteOAuthClientCommand extends Command
{

    /**
     * @var string
     */
    protected $clientId;

    public function __construct(string $clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    


}