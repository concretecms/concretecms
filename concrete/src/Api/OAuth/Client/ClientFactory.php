<?php
namespace Concrete\Core\Api\OAuth\Client;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\OAuth\Client;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\UuidGenerator;

class ClientFactory
{

    /**
     * @var \Concrete\Core\Application\Application
     */
    private $app;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Doctrine\ORM\Id\UuidGenerator
     */
    private $generator;

    public function __construct(Application $app, EntityManager $entityManager, UuidGenerator $generator)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
        $this->generator = $generator;
    }

    /**
     * Create a new OAuth client object and provide it a proper UUID
     *
     * @param string $name The client name
     * @param string $redirect The redirect url
     * @param string[] $scopes The scopes this client is allowed to interact with
     * @param string $key The client's api key
     * @param string $secret This secret should be properly hashed with something like `password_hash`
     * @return \Concrete\Core\Entity\OAuth\Client
     */
    public function createClient($name, $redirect, array $scopes, $key, $secret)
    {
        $client = $this->app->make(Client::class);
        $client->setName($name);
        $client->setRedirectUri($redirect);
        // @TODO support scopes
        //$client->setScopes($scopes);
        $client->setClientKey($key);
        $client->setClientSecret($secret);

        // Apply a new UUID to the client
        $id = $this->generator->generate($this->entityManager, $client);
        $client->setIdentifier($id);

        return $client;
    }

    /**
     * Generate new credentials for use with a client
     * Note: the secret provided by with these credentials is in plain text and should be hashed before storing to the
     * database.
     *
     * @param int $keyLength
     * @param int $secretLength
     * @return \Concrete\Core\Api\OAuth\Client\Credentials
     * @throws \InvalidArgumentException If an invalid size is passed
     */
    public function generateCredentials($keyLength = 64, $secretLength = 96)
    {
        // Make sure we have ints
        $keyLength = (int) $keyLength;
        $secretLength = (int) $secretLength;

        // Make sure our keys are long enough
        if ($keyLength < 16 || $secretLength < 16) {
            throw new \InvalidArgumentException('A key must have a length longer than 16');
        }

        return $this->app->make(Credentials::class, [
            'key' => $this->generateString($keyLength),
            'secret' => $this->generateString($secretLength)
        ]);
    }

    /**
     * Generate a cryptographically secure strig
     *
     * @param $length
     * @return bool|string
     */
    protected function generateString($length)
    {
        $bytes = ceil($length / 2);
        $string = bin2hex(random_bytes($bytes));

        return substr($string, 0, $length);
    }

}
