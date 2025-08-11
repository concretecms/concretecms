<?php
namespace Concrete\Core\Api\OAuth\Command;

use Concrete\Core\Api\OAuth\Client\ClientFactory;
use Concrete\Core\Entity\OAuth\Client;
use Doctrine\ORM\EntityManager;


class UpdateOAuthClientCommandHandler extends CreateOAuthClientCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param ClientFactory $clientFactory
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke($command)
    {
        /**
         * @var $client Client
         */
        $client = $this->entityManager->find(Client::class, $command->getClientIdentifier());
        $client->setName($command->getName());
        $client->setRedirectUri($command->getRedirect());
        $client->setDocumentationEnabled($command->isEnableDocumentation());
        $this->setConsentType($client, $command);
        $this->setCustomScopes($client, $command);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $client;
    }


}