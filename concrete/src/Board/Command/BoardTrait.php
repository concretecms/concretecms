<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;

trait BoardTrait
{
    /**
     * @var \Concrete\Core\Entity\Board\Board
     */
    protected $board;

    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @return $this
     */
    public function setBoard(Board $board): object
    {
        $this->board = $board;

        return $this;
    }
}
