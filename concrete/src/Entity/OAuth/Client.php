<?php

namespace Concrete\Core\Entity\OAuth;

use Concrete\Core\Api\Documentation\RedirectUriFactory;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="ClientRepository")
 * @ORM\Table(
 *     name="OAuth2Client",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="client_idx", columns={"clientKey", "clientSecret"})}
 * )
 */
class Client implements ClientEntityInterface, \JsonSerializable
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
     * @var string
     * @ORM\Column(type="text")
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
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $documentationEnabled = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $hasCustomScopes = false;

    /**
     * @ORM\ManyToMany(targetEntity="Scope", inversedBy="clients")
     * @ORM\JoinTable(name="OAuth2ClientScopes",
     *      joinColumns={@ORM\JoinColumn(name="clientIdentifier", referencedColumnName="identifier", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="scopeIdentifier", referencedColumnName="identifier")}
     *      )
     */
    protected $scopes;

    /**
     * The type of consent this client must get from the user
     *
     * @var int
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $consentType = self::CONSENT_SIMPLE;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->scopes = new ArrayCollection();
    }

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
     * Returns the actual redirect URI bound to the entity. This is a string (sometimes containing a | to explode
     * into multiple.) We have a separate method because the getRedirectUri method below actually splits piped
     * strings into arrays, and it also appends the Swagger UI doc redirectUri if docs are enabled on the client.
     *
     * @return string|null
     */
    public function getSpecifiedRedirectUri(): ?string
    {
        return $this->redirectUri;
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
         *
         * Additional note - I'm keeping the original comment above but this is NO LONGER THE CASE. League OAuth2
         * 8.2+ requires a redirectUri on auth code flows. So the change to null isn't necessary but I'm going to
         * keep it (AE)
         */
        $url = $this->redirectUri ? $this->redirectUri : null;

        $urls = [];

        if (is_string($url)) {
            if (strpos($url, '|') !== false) {
                $urls[] = explode('|', $url);
            } else {
                $urls[] = $url;
            }
        } else {
            $urls[] = '';
        }

        if ($this->isDocumentationEnabled()) {
            $urls[] = app(RedirectUriFactory::class)->createDocumentationRedirectUri($this);
        }

        if (count($urls) > 1) {
            return $urls;
        } else if (isset($urls[0])) {
            // we could technically return just the array every time but this will keep tests working just as before
            return $urls[0];
        } else {
            return '';
        }
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

    /**
     * @return bool
     */
    public function isDocumentationEnabled(): bool
    {
        return $this->documentationEnabled;
    }

    /**
     * @param bool $documentationEnabled
     */
    public function setDocumentationEnabled(bool $documentationEnabled): void
    {
        $this->documentationEnabled = $documentationEnabled;
    }

    /**
     * @return bool
     */
    public function hasCustomScopes(): bool
    {
        return $this->hasCustomScopes;
    }

    /**
     * @param bool $hasCustomScopes
     */
    public function setHasCustomScopes(bool $hasCustomScopes): void
    {
        $this->hasCustomScopes = $hasCustomScopes;
    }

    /**
     * @return ArrayCollection
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    public function setScopes($scopes): void
    {
        $this->scopes = $scopes;
    }


    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'identifier' => $this->getIdentifier(),
        ];
    }

}
