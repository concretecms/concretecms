<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;

class SetBoardCustomWeightingCommand extends BoardCommand
{

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
