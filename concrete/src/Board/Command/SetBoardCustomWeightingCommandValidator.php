<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Command\ValidatorInterface;

class SetBoardCustomWeightingCommandValidator implements ValidatorInterface
{

    /**
     * @var ErrorList 
     */
    protected $errorList;

    
    public function __construct(ErrorList $errorList)
    {
        $this->errorList = new ErrorList();
    }

    /**
     * @param SetBoardCustomWeightingCommand $command
     * @return ErrorList
     */
    public function validate($command)
    {
        $total = 0;
        foreach($command->getWeightings() as $weighting) {
            [$source, $weight] = $weighting;
            $total += $weight;            
        }
        if ($total !== 100) {
            $this->errorList->add(t('Custom weighting rules must add up to exactly 100.'));
        }
        return $this->errorList;
    }
}
