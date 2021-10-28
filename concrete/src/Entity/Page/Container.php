<?php
namespace Concrete\Core\Entity\Page;

use Concrete\Core\Entity\PackageTrait;
use Doctrine\ORM\Mapping as ORM;
use HtmlObject\Image;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="PageContainers"
 * )
 */
class Container
{
    use PackageTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $containerID;

    /**
     * @ORM\Column(type="string")
     */
    protected $containerHandle = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $containerIcon = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $containerName = '';

    public function getContainerID(): ?int
    {
        return $this->containerID;
    }

    public function getContainerHandle(): string
    {
        return $this->containerHandle;
    }

    /**
     * @return $this
     */
    public function setContainerHandle(string $containerHandle): self
    {
        $this->containerHandle = $containerHandle;

        return $this;
    }

    public function getContainerName(): string
    {
        return $this->containerName;
    }

    public function getContainerDisplayName(): string
    {
        return tc('PageContainerName', $this->getContainerName());
    }

    /**
     * @return $this
     */
    public function setContainerName(string $containerName): self
    {
        $this->containerName = $containerName;

        return $this;
    }

    public function getContainerIcon(): string
    {
        return $this->containerIcon;
    }

    /**
     * @return $this
     */
    public function setContainerIcon(string $containerIcon): self
    {
        $this->containerIcon = $containerIcon;

        return $this;
    }

    /**
     * @return \HtmlObject\Image|string|null
     */
    public function getContainerIconImage(bool $asTag = true)
    {
        if ($this->getContainerIcon()) {
            $image = ASSETS_URL_IMAGES . '/icons/containers/' . $this->getContainerIcon();
            if ($asTag) {
                $image = new Image($image);
            }
            return $image;
        }
    }

    public function export(\SimpleXMLElement $node): void
    {
        $container = $node->addChild('container');
        $container->addAttribute('handle', $this->getContainerHandle());
        $container->addAttribute('name', h($this->getContainerName()));
        $container->addAttribute('icon', $this->getContainerIcon());
        $container->addAttribute('package', $this->getPackageHandle());
    }
}
