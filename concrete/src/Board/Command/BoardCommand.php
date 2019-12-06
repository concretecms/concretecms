<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Foundation\Command\CommandInterface;

abstract class BoardCommand implements CommandInterface
{

    /**
     * @var Board
     */
    protected $board;

    /**
     * BoardCommand constructor.
     * @param Board $board
     */
    public function __construct(Board $board)
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
