<?php

namespace Concrete\Core\Page\Container\Command;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Command\ValidatorInterface;
use Concrete\Core\Utility\Service\Validation\Strings;

class ContainerCommandValidator implements ValidatorInterface
{

    /**
     * @var ErrorList 
     */
    protected $errorList;

    /**
     * @var Strings 
     */
    protected $stringValidator;
    
    public function __construct(ErrorList $errorList, Strings $stringValidator)
    {
        $this->errorList = new ErrorList();
        $this->stringValidator = $stringValidator;
    }

    /**
     * @param ContainerCommand $command
     * @return ErrorList
     */
    public function validate($command)
    {
        if (empty($command->getContainer()->getContainerName())) {
            $this->errorList->add(t('You must give your container a valid name.'));
        }
        $handle = $command->getContainer()->getContainerHandle();
        if (!$this->stringValidator->handle($handle)) {
            $this->errorList->add(t('You must specify a valid handle for this container.'));
        }
        return $this->errorList;
    }
}
