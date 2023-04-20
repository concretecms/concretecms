<?php

namespace Concrete\Core\Entity\Health\Report;

use Concrete\Core\Health\Report\Finding\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Control\ControlInterface;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
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
     * @ORM\ManyToOne(targetEntity="Result", inversedBy="findings")
     */
    protected $result;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $message;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $handle;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $control;

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
        if (!is_null($this->message) && is_array($this->message) && isset($this->message['class'])) {
            $serializer = new Serializer([new CustomNormalizer()], [new JsonEncoder()]);
            return $serializer->denormalize($this->message, $this->message['class']);
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getRawMessage()
    {
        return $this->message;
    }


    /**
     * @param mixed $message
     */
    public function setMessage(MessageInterface $message): void
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
    public function getRawControl()
    {
        return $this->control;
    }

    public function getControl(): ?ControlInterface
    {
        if (!is_null($this->control) && is_array($this->control) && isset($this->control['class'])) {
            $serializer = new Serializer([new CustomNormalizer()], [new JsonEncoder()]);
            return $serializer->denormalize($this->control, $this->control['class']);
        }
        return null;
    }

    /**
     * @param mixed $control
     */
    public function setControl($control): void
    {
        $this->control = $control;
    }

    abstract public function getFormatter(): FormatterInterface;




}
