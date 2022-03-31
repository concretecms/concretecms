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
     * @var string
     * @ORM\Column(type="string")
     */
    protected $description = '';

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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;
    }

    /**
     * Serialize this scope into a string.
     * This method MUST return a string and must match the scope ID that clients will request
     *
     * @return string
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
