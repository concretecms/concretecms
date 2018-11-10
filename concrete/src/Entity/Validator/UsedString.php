<?php

namespace Concrete\Core\Entity\Validator;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="UsedStringLog"
 * )
 */
class UsedString
{

    /**
     * The subject this string was used for
     *
     * @var int
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The used string
     *
     * @var string The hashed string that was used
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $usedString;

    /**
     * The subject this string was used for
     *
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $subject;

    /**
     * The DateTime the string was used
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $date;

    /**
     * Get the used string
     *
     * @return string
     */
    public function getUsedString()
    {
        return $this->usedString;
    }

    /**
     * Set the used string
     *
     * @param string $usedString
     *
     * @return \Concrete\Core\Entity\Validator\UsedString
     */
    public function setUsedString($usedString)
    {
        $this->usedString = $usedString;
        return $this;
    }

    /**
     * Get the subject the string was used with
     *
     * @return int
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the subject the string was used with
     *
     * @param int $subject
     *
     * @return \Concrete\Core\Entity\Validator\UsedString
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Get the date this string was used
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the date this string was used
     *
     * @param \DateTime $date
     *
     * @return \Concrete\Core\Entity\Validator\UsedString
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }
}
