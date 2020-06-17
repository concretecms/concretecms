<?php

namespace Concrete\TestHelpers\CI;

class SingleState extends State
{
    /**
     * The commit SHA-1.
     *
     * @var string
     */
    private $sha1;

    /**
     * @param string $engine the current engine (it's the value of one of the State::ENGINE__... constants)
     * @param string $event the event type (it's the value of one of the State::EVENT__... constants)
     * @param string $sha1 the commit SHA-1
     */
    public function __construct(string $engine, string $event, string $sha1)
    {
        parent::__construct($engine, $event);
        $this->sha1 = $sha1;
    }

    /**
     * Get the commit SHA-1.
     */
    public function getSha1(): string
    {
        return $this->sha1;
    }
}
