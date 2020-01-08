<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Foundation\Command\CommandInterface;

class SetBoardCustomWeightingCommand implements CommandInterface
{
    
    use BoardTrait;

    protected $weightings = [];
    
    public function addWeighting(ConfiguredDataSource $dataSource, int $weight)
    {
        $this->weightings[] = [$dataSource, $weight];
    }

    /**
     * @return array
     */
    public function getWeightings(): array
    {
        return $this->weightings;
    }
    
    

    
}
