<?php

namespace Concrete\Core\Board\Command;

class RegenerateBoardInstanceCommand extends AbstractBoardInstanceCommand
{
    use BoardInstanceTrait;

    /**
     * @var bool
     */
    protected $defer = false;

    public function isDefer(): bool
    {
        return $this->defer;
    }

    public function setDefer(bool $defer): void
    {
        $this->defer = $defer;
    }

}
