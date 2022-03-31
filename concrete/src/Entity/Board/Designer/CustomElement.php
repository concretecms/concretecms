<?php
namespace Concrete\Core\Entity\Board\Designer;

use Concrete\Core\Block\Block;
use Concrete\Core\Localization\Service\Date;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardDesignerCustomElements")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"itemSelector" = "ItemSelectorCustomElement"})
 */
abstract class CustomElement implements \JsonSerializable
{

    const STATUS_DRAFT = 'D';
    const STATUS_READY_TO_PUBLISH = 'R';

    abstract public function createBlock() : Block;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $elementName;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $author;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="guid")
     */
    protected $batchIdentifier;

    /**
     * @ORM\Column(type="string", length=1)
     */
    protected $status = self::STATUS_DRAFT;

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getElementName()
    {
        return $this->elementName;
    }

    /**
     * @param mixed $elementName
     */
    public function setElementName($elementName): void
    {
        $this->elementName = $elementName;
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
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getBatchIdentifier()
    {
        return $this->batchIdentifier;
    }

    /**
     * @param mixed $batchIdentifier
     */
    public function setBatchIdentifier($batchIdentifier): void
    {
        $this->batchIdentifier = $batchIdentifier;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function getDateCreatedDateTime()
    {
        $dateService = new Date();
        $timezone = $dateService->getUserTimeZoneID();
        $dateTime = new \DateTime('@' . $this->dateCreated);
        $dateTime->setTimezone(new \DateTimeZone($timezone));
        return $dateTime;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->getID(),
            'name' => $this->getElementName(),
            'dateCreated' => $this->getDateCreated(),
        ];
    }
}
