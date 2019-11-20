<?php

namespace Concrete\Core\Entity\Board\DataSource;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
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
     */
    protected $data_source;

    /**
     * @ORM\OneToOne(targetEntity="Concrete\Core\Entity\Board\DataSource\Configuration\Configuration", 
     *     mappedBy="data_source",  cascade={"persist","remove"})
     **/
    protected $configuration;

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
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->data_source;
    }
    
    

    

    
}
