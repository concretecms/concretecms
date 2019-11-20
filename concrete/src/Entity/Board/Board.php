<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Boards")
 */
class Board
{
    
    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     */
    protected $site;
    
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $boardName;

    /**
     * @ORM\OneToMany(targetEntity="Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource", cascade={"remove"}, mappedBy="board")
     */
    protected $data_sources;

    /**
     * @ORM\OneToMany(targetEntity="Item", cascade={"remove"}, mappedBy="board", fetch="EXTRA_LAZY")
     */
    protected $items;


    public function __construct()
    {
        $this->data_sources = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getBoardID()
    {
        return $this->boardID;
    }
    
    /**
     * @return mixed
     */
    public function getBoardName()
    {
        return $this->boardName;
    }

    /**
     * @param mixed $boardName
     */
    public function setBoardName($boardName): void
    {
        $this->boardName = $boardName;
    }

    /**
     * @return mixed
     */
    public function getDataSources()
    {
        return $this->data_sources;
    }

    /**
     * @param mixed $data_sources
     */
    public function setDataSources($data_sources): void
    {
        $this->data_sources = $data_sources;
    }
    
    
    





}
