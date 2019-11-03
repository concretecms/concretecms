<?php

namespace Concrete\Core\Page\Container\Command;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\Foundation\Command\ValidatorInterface;
use Concrete\Core\Page\Container\TemplateRepository;
use Concrete\Core\Page\Theme\Theme;

class ContainerCommandValidator implements ValidatorInterface
{

    /**
     * @var ErrorList 
     */
    protected $errorList;

    /**
     * @var TemplateRepository 
     */
    protected $templateRepository;
    
    public function __construct(TemplateRepository $templateRepository, ErrorList $errorList)
    {
        $this->templateRepository = $templateRepository;
        $this->errorList = new ErrorList();
    }

    /**
     * @param ContainerCommand $command
     * @return ErrorList
     */
    public function validate(CommandInterface $command)
    {
        if (empty($command->getContainer()->getContainerName())) {
            $this->errorList->add(t('You must give your container a valid name.'));
        }
        $themeID = $command->getContainer()->getContainerThemeID();
        $theme = null;
        if ($themeID) {
            $theme = Theme::getByID($themeID);
        }
        if (!$theme) {
            $this->errorList->add(t('You must specify a valid theme for your container.'));
        } else {
            try {
                $valid = $this->templateRepository->isValid(
                    $theme, $command->getContainer()->getContainerTemplateFile()
                );
            } catch (\Exception $e) {
                $this->errorList->add($e->getMessage());
            }
        }
        
        return $this->errorList;
    }
}
