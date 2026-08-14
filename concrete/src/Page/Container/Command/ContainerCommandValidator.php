<?php

namespace Concrete\Core\Page\Container\Command;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Command\ValidatorInterface;
use Concrete\Core\Page\Container\IconRepository;
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

    /**
     * @var IconRepository
     */
    protected $iconRepository;

    public function __construct(ErrorList $errorList, Strings $stringValidator, IconRepository $iconRepository)
    {
        $this->errorList = $errorList;
        $this->stringValidator = $stringValidator;
        $this->iconRepository = $iconRepository;
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
        $icon = $command->getContainer()->getContainerIcon();
        if ($icon !== '') {
            $validIcons = array_map(function ($icon) {
                return $icon->getFilename();
            }, $this->iconRepository->getIcons());
            if (!in_array($icon, $validIcons, true)) {
                $this->errorList->add(t('You must specify a valid icon for this container.'));
            }
        }
        return $this->errorList;
    }
}
