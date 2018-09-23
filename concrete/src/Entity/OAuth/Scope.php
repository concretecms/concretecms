<?php

namespace Concrete\Core\Entity\OAuth;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * @ORM\Entity(repositoryClass="ScopeRepository")
 * @ORM\Table(
 *     name="OAuth2Scope"
 * )
 */
class Scope implements ScopeEntityInterface
{
    /**
     * @var string
     * @ORM\Id @ORM\Column(type="string")
     */
    protected $identifier;

    /**
     * @ORM\ManyToMany(targetEntity="AuthCode", mappedBy="scopes")
     */
    protected $codes;

    /**
     * @ORM\ManyToMany(targetEntity="AccessToken", mappedBy="scopes")
     */
    protected $tokens;

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Server\Entities\ScopeEntityInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
