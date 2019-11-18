<?php

namespace Concrete\Core\Entity\Board;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardConfiguredDataSource")
 */
class ConfiguredDataSource
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $configuredDataSourceID;

    /**
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="data_sources")
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
     * @return Board|null
     */
    public function getBoard(): ?Board
    {
        return $this->board;
    }


    
}
