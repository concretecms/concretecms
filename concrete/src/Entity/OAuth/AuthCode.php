<?php

namespace Concrete\Core\Entity\OAuth;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AuthCodeRepository")
 * @ORM\Table(
 *     name="OAuth2AuthCode"
 * )
 */
class AuthCode implements AuthCodeEntityInterface
{

    use EntityTrait, TokenEntityTrait, AuthCodeTrait;

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $identifier;

    /**
     * @ORM\Column(type="json_array")
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

}
