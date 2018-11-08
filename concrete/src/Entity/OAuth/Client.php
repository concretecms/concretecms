<?php

namespace Concrete\Core\Entity\OAuth;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @ORM\Entity(repositoryClass="ClientRepository")
 * @ORM\Table(
 *     name="OAuth2Client",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="client_idx", columns={"clientKey", "clientSecret"})}
 * )
 */
class Client implements ClientEntityInterface
{
    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $identifier;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string|string[]
     * @ORM\Column(type="string")
     */
    protected $redirectUri;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $clientKey;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $clientSecret;

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\ClientEntityInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set the client's identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\ClientEntityInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the client's name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getClientKey()
    {
        return $this->clientKey;
    }

    /**
     * @param string $clientKey
     */
    public function setClientKey($clientKey)
    {
        $this->clientKey = $clientKey;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\ClientEntityInterface::getRedirectUri()
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * Set the registered redirect URI (as a string), or an indexed array of redirect URIs.
     *
     * @param string|string[] $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }
}
