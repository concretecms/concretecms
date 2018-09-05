<?php

namespace Concrete\Core\API\OAuth;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;

final class Controller
{

    /** @var \League\OAuth2\Server\AuthorizationServer */
    private $oauthServer;

    /** @var \Doctrine\ORM\EntityManagerInterface  */
    private $entityManager;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    public function __construct(AuthorizationServer $oauthServer, EntityManagerInterface $entityManager, ServerRequestInterface $request)
    {
        $this->oauthServer = $oauthServer;
        $this->entityManager = $entityManager;
        $this->request = $request;
    }

    /**
     * Handle authorization
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function token()
    {
        try {
            $response = $this->oauthServer->respondToAccessTokenRequest($this->request, new Response());

            // We generated a new token, let's prune old ones
            $this->pruneTokens();

            return $response;
        } catch (\Exception $e) {
            // Rethrow the exception
            throw $e;
        }
    }

    /**
     * Prune old authentication tokens
     */
    private function pruneTokens()
    {
        $now = new \DateTime('now');
        $qb = $this->entityManager->createQueryBuilder();

        // Delete all expired access tokens
        $qb->delete(AccessToken::class, 'token')
            ->where($qb->expr()->lt('token.expiryDateTime', ':now'))
            ->getQuery()->execute([':now' => $now]);

        // Delete all expired refresh tokens
        $qb->delete(RefreshToken::class, 'token')
            ->where($qb->expr()->lt('token.expiryDateTime', ':now'))
            ->getQuery()->execute([':now' => $now]);

        // Delete all expired auth codes
        $qb->delete(AuthCode::class, 'token')
            ->where($qb->expr()->lt('token.expiryDateTime', ':now'))
            ->getQuery()->execute([':now' => $now]);
    }

}
