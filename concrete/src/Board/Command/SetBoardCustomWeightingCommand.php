<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Foundation\Command\CommandInterface;

class SetBoardCustomWeightingCommand implements CommandInterface
{
    use BoardTrait;

    protected $weightings = [];

    /**
     * @return $this
     */
    public function addWeighting(ConfiguredDataSource $dataSource, int $weight): object
    {
        $this->weightings[] = [$dataSource, $weight];

        return $this;
    }

    /**
     * @return array
     */
    public function getWeightings(): array
    {
        return $this->weightings;
    }
}
