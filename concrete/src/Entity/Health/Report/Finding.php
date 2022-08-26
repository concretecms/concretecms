<?php

namespace Concrete\Core\Entity\Health\Report;

use Concrete\Core\Health\Report\Finding\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\SettingsLocation\SettingsLocationInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
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
     * @ORM\Column(type="json")
     */
    protected $settingsLocation;

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
    public function getRawSettingsLocation()
    {
        return $this->settingsLocation;
    }

    public function getSettingsLocation(): ?SettingsLocationInterface
    {
        if (is_array($this->settingsLocation) && isset($this->settingsLocation['class'])) {
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            return $serializer->denormalize($this->settingsLocation, $this->settingsLocation['class']);
        }
        return null;
    }

    /**
     * @param mixed $settingsLocation
     */
    public function setSettingsLocation($settingsLocation): void
    {
        $this->settingsLocation = $settingsLocation;
    }

    abstract public function getFormatter(): FormatterInterface;




}
