<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardInstanceLogEntries")
 */
class InstanceLogEntry implements \JsonSerializable
{

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="InstanceLog", inversedBy="entries")
     */
    protected $log;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="text")
     */
    protected $message;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $data;

    public function __construct()
    {
        $this->timestamp = time();
    }

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
     * @return int|string
     */
    public function getTimestamp(?string $format = null)
    {
        if ($format !== null) {
            $datetime = new \DateTime();
            $datetime->setTimestamp($this->timestamp);
            return $datetime->format($format);
        }
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param mixed $log
     */
    public function setLog($log): void
    {
        $this->log = $log;
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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = $this->getData();
        $displayData = null;
        if ($data) {
            $displayData = print_r($data, true);
        }
        return [
            'id' => $this->getId(),
            'timestamp' => $this->getTimestamp(),
            'timestampDisplay' => $this->getTimestamp('n/j/Y g:i A'),
            'message' => $this->getMessage(),
            'data' => $data,
            'displayData' => $displayData,
        ];
    }
}
