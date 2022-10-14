<?php
namespace Concrete\Core\Api\OAuth\Command;

use Concrete\Core\Api\OAuth\Client\ClientFactory;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Foundation\Command\Command;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;


class CreateOAuthClientCommandHandler
{

    /**
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param ClientFactory $clientFactory
     */
    public function __construct(EntityManager $entityManager, ClientFactory $clientFactory)
    {
        $this->entityManager = $entityManager;
        $this->clientFactory = $clientFactory;
    }

    protected function setConsentType(Client $client, $command)
    {
        try {
            $requestConsentType = $command->getConsentType();
            $client->setConsentType((int) $requestConsentType);
        } catch (\InvalidArgumentException $e) {
            // Default to simple consent
            $client->setConsentType(Client::CONSENT_SIMPLE);
        }
    }

    protected function setCustomScopes(Client $client, $command)
    {
        $client->setScopes(new ArrayCollection());
        if ($command->hasCustomScopes()) {
            $client->setHasCustomScopes(true);
            foreach ($command->getCustomScopes() as $scopeIdentifier) {
                $scope = $this->entityManager->find(Scope::class, $scopeIdentifier);
                if ($scope) {
                    $client->getScopes()->add($scope);
                }
            }
        } else {
            $client->setHasCustomScopes(false);
        }
    }

    public function __invoke($command)
    {
        $credentials = $this->clientFactory->generateCredentials();

        // Create a new client while hashing the new secret
        $client = $this->clientFactory->createClient(
            $command->getName(),
            $command->getRedirect(),
            [],
            $credentials->getKey(),
            password_hash($credentials->getSecret(), PASSWORD_DEFAULT),
            $command->isEnableDocumentation()
        );

        $this->setConsentType($client, $command);
        $this->setCustomScopes($client, $command);

        // Persist the new client to the database
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return [$client, $credentials];
    }


}