<?php

namespace Concrete\Core\Board\DataSource\Populator;

use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;

defined('C5_EXECUTE') or die("Access Denied.");

interface PopulatorInterface
{

    public function getDataSourceObjects(Board $board, Configuration $configuration) : array;

    public function getObjectRelevantDate($mixed) : int;
    
    public function createBoardItemBlock($mixed) : Block;
    
}
