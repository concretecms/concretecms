<?php

namespace Concrete\Core\Entity\OAuth;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="RefreshTokenRepository")
 * @ORM\Table(
 *     name="OAuth2RefreshToken"
 * )
 */
class RefreshToken implements RefreshTokenEntityInterface
{

    use RefreshTokenTrait, EntityTrait;

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
     * @var AccessTokenEntityInterface
     * @ORM\OneToOne(targetEntity="AccessToken")
     * @ORM\JoinColumn(name="client", referencedColumnName="identifier")
     */
    protected $accessToken;
}
