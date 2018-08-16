<?php

namespace Concrete\Core\Entity\OAuth;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ScopeRepository")
 * @ORM\Table(
 *     name="OAuth2Scope"
 * )
 */
class Scope implements ScopeEntityInterface
{

    use EntityTrait;

    /**
     * @var string
     * @ORM\Id @ORM\Column(type="string")
     */
    protected $identifier;

    /**
     * Serialize into a string
     */
    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
