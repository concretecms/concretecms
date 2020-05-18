<?php

namespace Concrete\Core\Entity\Board\DataSource;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardConfiguredDataSources")
 */
class ConfiguredDataSource
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $configuredDataSourceID;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Board\Board", inversedBy="data_sources")
     * @ORM\JoinColumn(name="boardID", referencedColumnName="boardID")
     * @var Board
     */
    protected $board;

    /**
     * @ORM\ManyToOne(targetEntity="DataSource")
     * @ORM\JoinColumn(name="dataSourceID", referencedColumnName="dataSourceID")
     * @var DataSource
     */
    protected $data_source;

    /**
     * @ORM\OneToMany(targetEntity="Concrete\Core\Entity\Board\InstanceItem", cascade={"remove"}, mappedBy="data_source", fetch="EXTRA_LAZY")
     */
    protected $items;

    /**
     * @ORM\Column(type="string")
     */
    protected $name = '';

    /**
     * @ORM\Column(type="integer")
     */
    protected $customWeight = 0;

    /**
     * @ORM\OneToOne(targetEntity="Concrete\Core\Entity\Board\DataSource\Configuration\Configuration",
     *     mappedBy="data_source",  cascade={"persist","remove"})
     **/
    protected $configuration;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return Board|null
     */
    public function getBoard(): ?Board
    {
        return $this->board;
    }

    /**
     * @return mixed
     */
    public function getConfiguration() : ?Configuration
    {
        return $this->configuration;
    }

    /**
     * @param Board $board
     */
    public function setBoard(Board $board): void
    {
        $this->board = $board;
    }

    /**
     * @param mixed $data_source
     */
    public function setDataSource($data_source): void
    {
        $this->data_source = $data_source;
    }

    /**
     * @return mixed
     */
    public function getConfiguredDataSourceID()
    {
        return $this->configuredDataSourceID;
    }

    /**
     * @return DataSource
     */
    public function getDataSource()
    {
        return $this->data_source;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCustomWeight()
    {
        return $this->customWeight;
    }

    /**
     * @param mixed $customWeight
     */
    public function setCustomWeight($customWeight): void
    {
        $this->customWeight = $customWeight;
    }


}
