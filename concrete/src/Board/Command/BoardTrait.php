<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;

trait BoardTrait
{

    /**
     * @var Board
     */
    protected $board;

    /**
     * @param Board $board
     */
    public function setBoard(Board $board): void
    {
        $this->board = $board;
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }



    
}
