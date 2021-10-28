<?php

namespace Concrete\Core\Entity\OAuth;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
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
     * Disable the users ability to allow / deny consent for this client to access details.
     * This should only be used if a client is fully trusted and owned by this server
     */
    const CONSENT_NONE = 0;

    /**
     * Give the user the option to allow or deny access without changing scopes
     */
    const CONSENT_SIMPLE = 1;

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
     * The type of consent this client must get from the user
     *
     * @var int
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $consentType = self::CONSENT_SIMPLE;

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


    public function isConfidential()
    {
        return true;
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
        /**
         * Note – An empty redirect URL will still trigger League's redirect URI check, because it's looking for
         * is_string() and this returns an empty string. So let's use the falsy check to turn even empty strings
         * into nulls.
         */
        $url = $this->redirectUri ? $this->redirectUri : null;

        if (is_string($url) && strpos($url, '|') !== false) {
            return explode('|', $url);
        }

        return $url;
    }

    /**
     * Set the registered redirect URI (as a string), or an indexed array of redirect URIs.
     *
     * @param string|string[] $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = is_array($redirectUri) ? implode('|', $redirectUri) : $redirectUri;
    }

    /**
     * Get the consent type required by this client
     *
     * @return int Client::CONSENT_SIMPLE | Client::CONSENT_NONE
     */
    public function getConsentType()
    {
        return $this->consentType;
    }

    /**
     * Set the level of consent this client must receive from the authenticating user
     *
     * @param int $consentType Client::CONSENT_SIMPLE | Client::CONSENT_NONE
     */
    public function setConsentType($consentType)
    {
        if ($consentType !== self::CONSENT_SIMPLE &&
            $consentType !== self::CONSENT_NONE) {
            throw new InvalidArgumentException('Invalid consent type provided.');
        }

        $this->consentType = $consentType;
    }
}
