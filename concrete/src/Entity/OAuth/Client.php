<?php

namespace Concrete\Core\Entity\OAuth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ClientRepository")
 * @ORM\Table(
 *     name="OAuth2Client",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="client_idx", columns={"clientKey", "clientSecret"})}
 * )
 */
class Client implements ClientEntityInterface
{

    use EntityTrait, ClientTrait;

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
     * @param string|string[] $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }
}
