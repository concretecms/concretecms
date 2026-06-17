<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardInstanceLogs")
 */
class InstanceLog implements \JsonSerializable
{

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Instance", inversedBy="log")
     * @ORM\JoinColumn(name="boardInstanceID", referencedColumnName="boardInstanceID")
     */
    protected $instance;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

    /**
     * @ORM\OneToMany(targetEntity="InstanceLogEntry", cascade={"persist", "remove"}, mappedBy="log")
     * @ORM\OrderBy({"timestamp" = "ASC"})
     */
    protected $entries;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
        $this->dateCreated = time();
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param mixed $instance
     */
    public function setInstance($instance): void
    {
        $this->instance = $instance;
    }

    /**
     * @return InstanceLogEntry[]
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     *
     * @return \Concrete\Core\Entity\Board\InstanceLogEntry[]
     */
    public function jsonSerialize(): array
    {
        $entries = $this->getEntries();
        $return = [];
        if ($entries->count()) {
            $return = [];
            foreach ($entries as $entry) {
                if ($entry instanceof InstanceLogEntry) {
                    $return[] = $entry;
                }
            }
        }

        return $return;
    }
}
