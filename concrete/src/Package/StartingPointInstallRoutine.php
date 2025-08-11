<?php
namespace Concrete\Core\Package;

class StartingPointInstallRoutine
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
}
