<?php
namespace Concrete\Core\Entity\Summary;

use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="SummaryFields"
 * )
 */
class Field implements FieldInterface
{
    use PackageTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $handle = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return tc('SummaryFieldName', $this->getName());
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @return $this
     */
    public function setHandle(string $handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    public function getFieldIdentifier(): string
    {
        $handle = $this->getHandle();
        return $handle;
    }

    public function export(\SimpleXMLElement $node): void
    {
        $container = $node->addChild('field');
        $container->addAttribute('handle', $this->getHandle());
        $container->addAttribute('name', h($this->getName()));
        $container->addAttribute('package', $this->getPackageHandle());
    }
}
