<?php

namespace Concrete\TestHelpers\CI;

class TagState extends SingleState
{
    /**
     * The tag.
     *
     * @var string
     */
    private $tag;

    /**
     * @param string $engine the current engine (it's the value of one of the State::ENGINE__... constants)
     * @param string $event the event type (it's the value of one of the State::EVENT__... constants)
     * @param string $sha1 the commit SHA-1
     * @param string $tag the tag
     */
    public function __construct(string $engine, string $event, string $sha1, string $tag)
    {
        parent::__construct($engine, $event, $sha1);
        $this->tag = $tag;
    }

    /**
     * Get the tag.
     */
    public function getTag(): string
    {
        return $this->tag;
    }
}
