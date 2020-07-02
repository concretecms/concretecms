<?php
namespace Concrete\Core\Entity\Board\Designer;

use Concrete\Core\Localization\Service\Date;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardDesignerCustomElements")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"itemSelector" = "ItemSelectorCustomElement"})
 */
abstract class CustomElement
{

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
