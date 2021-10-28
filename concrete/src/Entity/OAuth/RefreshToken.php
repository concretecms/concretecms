<?php

namespace Concrete\Core\Entity\OAuth;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="RefreshTokenRepository")
 * @ORM\Table(
 *     name="OAuth2RefreshToken"
 * )
 */
class RefreshToken implements RefreshTokenEntityInterface
{
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
     * @var \League\OAuth2\Server\Entities\AccessTokenEntityInterface
     * @ORM\OneToOne(targetEntity="AccessToken")
     * @ORM\JoinColumn(name="accessToken", referencedColumnName="identifier", onDelete="SET NULL")
     */
    protected $accessToken;

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\RefreshTokenEntityInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\RefreshTokenEntityInterface::setIdentifier()
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\RefreshTokenEntityInterface::setAccessToken()
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\RefreshTokenEntityInterface::getAccessToken()
     * @return \Concrete\Core\Entity\OAuth\AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\RefreshTokenEntityInterface::getExpiryDateTime()
     */
    public function getExpiryDateTime()
    {
        return $this->expiryDateTime;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\RefreshTokenEntityInterface::setExpiryDateTime()
     */
    public function setExpiryDateTime(\DateTimeImmutable $dateTime)
    {
        $this->expiryDateTime = $dateTime;
    }
}
