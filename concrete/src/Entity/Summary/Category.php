<?php
namespace Concrete\Core\Entity\Summary;

use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Html\Image;
use Concrete\Core\Summary\Category\Driver\DriverInterface;
use Concrete\Core\Summary\Category\Driver\Manager;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="SummaryCategories"
 * )
 */
class Category
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

    /**
     * @ORM\ManyToMany(targetEntity="Template", mappedBy="categories")
     */
    protected $templates;

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
        return tc('SummaryCategoryName', $this->getName());
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

    /**
     * @return mixed
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param mixed $templates
     */
    public function setTemplates($templates): void
    {
        $this->templates = $templates;
    }

    public function export(\SimpleXMLElement $node): void
    {
        $container = $node->addChild('category');
        $container->addAttribute('handle', $this->getHandle());
        $container->addAttribute('name', h($this->getName()));
        $container->addAttribute('package', $this->getPackageHandle());
    }

    public function getDriver() : DriverInterface
    {
        $app = Facade::getFacadeApplication();
        $manager = $app->make(Manager::class);
        return $manager->driver($this->getHandle());
    }
}
