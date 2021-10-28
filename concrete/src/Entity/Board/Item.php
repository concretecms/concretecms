<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardItems", indexes={
 * @ORM\Index(name="uniqueItemId", columns={"uniqueItemId"})
 * })
 */
class Item
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardItemID;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $relevantDate;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\File\File")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID")
     */
    protected $relevantThumbnail;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Board\DataSource\DataSource")
     * @ORM\JoinColumn(name="dataSourceID", referencedColumnName="dataSourceID")
     **/
    protected $data_source;

    /**
     * Note: this is not fully unique in the table, but it is unique across data sources (e.g. calendar event IDs
     * and page IDs can be dupes)
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $uniqueItemId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="json")
     */
    protected $data;

    /**
     * @ORM\OneToMany(targetEntity="ItemCategory", cascade={"persist", "remove"}, mappedBy="item", fetch="EXTRA_LAZY")
     *
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="ItemTag", cascade={"persist", "remove"}, mappedBy="item", fetch="EXTRA_LAZY")
     */
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->data_source;
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
    public function getUniqueItemId()
    {
        return $this->uniqueItemId;
    }

    /**
     * @param mixed $uniqueItemId
     */
    public function setUniqueItemId($uniqueItemId): void
    {
        $this->uniqueItemId = $uniqueItemId;
    }

    /**
     * @return mixed
     */
    public function getBoardItemID()
    {
        return $this->boardItemID;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getRelevantDate()
    {
        return $this->relevantDate;
    }

    /**
     * @param mixed $relevantDate
     */
    public function setRelevantDate($relevantDate): void
    {
        $this->relevantDate = $relevantDate;
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
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getRelevantThumbnail()
    {
        return $this->relevantThumbnail;
    }

    /**
     * @param mixed $relevantThumbnail
     */
    public function setRelevantThumbnail($relevantThumbnail): void
    {
        $this->relevantThumbnail = $relevantThumbnail;
    }


}
