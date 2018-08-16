<?php

namespace Concrete\Core\Entity\OAuth;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AccessTokenRepository")
 * @ORM\Table(
 *     name="OAuth2AccessToken"
 * )
 */
class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait, EntityTrait, TokenEntityTrait;

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $identifier;

    /**
     * @var ScopeEntityInterface[]
     * @ORM\ManyToMany(targetEntity="Scope", mappedBy="scopes")
     * @ORM\JoinColumn(name="scopes", referencedColumnName="identifier")
     */
    protected $scopes = [];

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $expiryDateTime;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $userIdentifier;

    /**
     * @var ScopeEntityInterface[]
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="client", referencedColumnName="identifier")
     */
    protected $client;

    public function getScopes()
    {
        return iterator_to_array($this->scopes);
    }

}
