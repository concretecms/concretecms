<?php
namespace Concrete\Core\Package;

/**
 * @deprecated
 */
class StartingPointInstallRoutine implements \JsonSerializable
{
    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var string
     */
    public $method;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var int
     */
    public $progress;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var string
     */
    public $text;

    public function __construct($method, $progress, $text = '')
    {
        $this->method = $method;
        $this->progress = $progress;
        $this->text = $text;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getProgress()
    {
        return $this->progress;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'method' => $this->getMethod(),
            'progress' => $this->getProgress(),
            'text' => $this->getText(),
        ];
    }
}
