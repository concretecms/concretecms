<?php
namespace Concrete\Core\Entity\Board\Designer;

use Concrete\Core\Localization\Service\Date;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardDesignerCustomElements")
 */
class CustomElement
{

    const CREATION_METHOD_CUSTOM = 'C';
    const CREATION_METHOD_ITEMS = 'I';

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
     * I = select items and build a stripe that way
     * C = custom designed stripe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $creationMethod;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

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
    public function getCreationMethod()
    {
        return $this->creationMethod;
    }

    /**
     * @param mixed $creationMethod
     */
    public function setCreationMethod($creationMethod): void
    {
        $this->creationMethod = $creationMethod;
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

    public function getDateCreatedDateTime()
    {
        $dateService = new Date();
        $timeZone = $dateService->getTimezoneID('app');
        if ($this->author) {
            $authorTimeZone = $this->author->getUserTimezone();
            if ($authorTimeZone) {
                $timeZone = $authorTimeZone;
            }
        }
        $dateTime = new \DateTime('@' . $this->dateCreated);
        $dateTime->setTimezone(new \DateTimeZone($timeZone));
        return $dateTime;
    }

}
