<?php

namespace Concrete\Core\Entity\OAuth;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

/**
 * @ORM\Entity(repositoryClass="AccessTokenRepository")
 * @ORM\Table(
 *     name="OAuth2AccessToken"
 * )
 */
class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $identifier;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $expiryDateTime;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=true)
     */
    protected $userIdentifier;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $scopes = [];

    /**
     * @var \League\OAuth2\Server\Entities\ScopeEntityInterface[]
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="client", referencedColumnName="identifier")
     */
    protected $client;

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::setIdentifier()
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::addScope()
     */
    public function addScope(ScopeEntityInterface $scope)
    {
        $this->scopes[] = $scope;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::getScopes()
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::getExpiryDateTime()
     */
    public function getExpiryDateTime()
    {
        return $this->expiryDateTime;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::setExpiryDateTime()
     */
    public function setExpiryDateTime(\DateTimeImmutable $dateTime)
    {
        $this->expiryDateTime = $dateTime;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::setUserIdentifier()
     */
    public function setUserIdentifier($identifier)
    {
        $this->userIdentifier = $identifier;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::getUserIdentifier()
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::getClient()
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\TokenInterface::setClient()
     */
    public function setClient(ClientEntityInterface $client)
    {
        $this->client = $client;
    }
}
