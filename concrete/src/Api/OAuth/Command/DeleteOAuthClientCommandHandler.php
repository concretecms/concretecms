<?php
namespace Concrete\Core\Api\OAuth\Command;


use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Doctrine\ORM\EntityManager;

class DeleteOAuthClientCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function revokeClientTokens(Client $client)
    {
        /** @var \Concrete\Core\Entity\OAuth\RefreshTokenRepository $refreshTokenRepository */
        $refreshTokenRepository = $this->entityManager->getRepository(RefreshToken::class);

        /** @var \Concrete\Core\Entity\OAuth\AccessTokenRepository $accessTokenRepository */
        $accessTokenRepository = $this->entityManager->getRepository(AccessToken::class);

        /** @var \Concrete\Core\Entity\OAuth\AuthCodeRepository $authCodeRepository */
        $authCodeRepository = $this->entityManager->getRepository(AuthCode::class);

        $criteria = ['client' => $client];

        foreach ($accessTokenRepository->findBy($criteria) as $token) {
            // If there is an associated refresh token, revoke it
            if ($refreshToken = $refreshTokenRepository->findOneBy(['accessToken' => $token])) {
                $refreshTokenRepository->revokeRefreshToken($refreshToken);
            }

            // Revoke the access token
            $accessTokenRepository->revokeAccessToken($token);
        }

        // Finally revoke all auth codes
        foreach ($authCodeRepository->findBy($criteria) as $authCode) {
            $authCodeRepository->revokeAuthCode($authCode);
        }
    }


    public function __invoke(DeleteOAuthClientCommand $command)
    {
        $client = $this->entityManager->find(Client::class, $command->getClientId());
        if ($client) {
            // Revoke all tokens associated with the client
            $this->revokeClientTokens($client);
            $this->entityManager->remove($client);
            $this->entityManager->flush();
        }
    }

}