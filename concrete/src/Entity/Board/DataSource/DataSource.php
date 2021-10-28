<?php
namespace Concrete\Core\Entity\Board\DataSource;

use Concrete\Core\Board\DataSource\Driver\DriverInterface;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Board\DataSource\Driver\Manager;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="BoardDataSources"
 * )
 */
class DataSource
{
    use PackageTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $dataSourceID;

    /**
     * @ORM\Column(type="string")
     */
    protected $handle = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $name = '';

    public function getId(): ?int
    {
        return $this->dataSourceID;
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

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDisplayName(): string
    {
        return tc('BoardDataSourceName', $this->getName());
    }

    public function getDriver(): DriverInterface
    {
        $app = Facade::getFacadeApplication();
        $manager = $app->make(Manager::class);
        return $manager->driver($this->getHandle());
    }
}
