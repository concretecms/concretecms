<?php

namespace Concrete\TestHelpers\CI;

abstract class State
{
    public const ENGINE_TRAVISCI = 'travis-ci';

    public const ENGINE_APPVEYOR = 'appveyor';

    public const ENGINE_GITHUBACTIONS = 'github-actions';

    public const EVENT_PUSH = 'push';

    public const EVENT_PULLREQUEST = 'pull-request';

    public const EVENT_TAG = 'tag';

    public const EVENT_SCHEDULED = 'scheduled';

    public const EVENT_MANUAL = 'manual';

    /**
     * The current engine (it's the value of one of the State::ENGINE__... constants).
     *
     * @var string
     */
    private $engine;

    /**
     * The event type (it's the value of one of the State::EVENT__... constants).
     *
     * @var string
     */
    private $event;

    /**
     * @param string $engine the current engine (it's the value of one of the State::ENGINE__... constants)
     * @param string $event the event type (it's the value of one of the State::EVENT__... constants)
     */
    protected function __construct(string $engine, string $event)
    {
        $this->engine = $engine;
        $this->event = $event;
    }

    /**
     * Get the current engine (it's the value of one of the State::ENGINE__... constants).
     *
     * @return string
     */
    public function getEngine(): string
    {
        return $this->engine;
    }

    /**
     * Get the event type (it's the value of one of the State::EVENT__... constants).
     *
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }
}
