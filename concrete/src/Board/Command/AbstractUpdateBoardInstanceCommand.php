<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Foundation\Command\Command;

abstract class AbstractUpdateBoardInstanceCommand extends Command
{

    /**
     * @var string
     */
    protected $driver;

    /**
     * @var mixed
     */
    protected $object;

    public function __construct(string $driver, $object)
    {
        $this->driver = $driver;
        $this->object = $object;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

}
