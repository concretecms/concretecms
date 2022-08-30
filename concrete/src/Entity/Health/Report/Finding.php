<?php

namespace Concrete\Core\Entity\Health\Report;

use Concrete\Core\Health\Report\Finding\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Details\DetailsInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @ORM\Entity(repositoryClass="FindingRepository")
 * @ORM\Table(name="HealthReportResultFindings")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
abstract class Finding
{

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Result")
     */
    protected $result;

    /**
     * @ORM\Column(type="string")
     */
    protected $message;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $handle;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $details;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result): void
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle): void
    {
        $this->handle = $handle;
    }

    /**
     * @return mixed
     */
    public function getRawDetails()
    {
        return $this->details;
    }

    public function getDetails(): ?DetailsInterface
    {
        if (!is_null($this->details) && is_array($this->details) && isset($this->details['class'])) {
            $serializer = new Serializer([new CustomNormalizer()], [new JsonEncoder()]);
            return $serializer->denormalize($this->details, $this->details['class']);
        }
        return null;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details): void
    {
        $this->details = $details;
    }

    abstract public function getFormatter(): FormatterInterface;




}
