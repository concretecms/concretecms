<?php

namespace Concrete\Core\Page\Container;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Theme\Theme;
use Illuminate\Filesystem\Filesystem;

/**
 * Class TemplateRepository
 * 
 * Responsible for pointing to a list of container templates for a given theme.
 */
class TemplateRepository
{
    const TEMPLATE_DIRECTORY = 'containers';
    
    /**
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * @var Application 
     */
    protected $app;
    
    public function __construct(Filesystem $filesystem, Application $app)
    {
        $this->filesystem = $filesystem;
        $this->app = $app;
    }
    
    public function isValid(Theme $theme, string $templateFile)
    {
        if (!$templateFile) {
            throw new UserMessageException(t('Container template filename cannot be empty.'));
        }
        if ($this->filesystem->extension($templateFile) !== 'php') {
            throw new UserMessageException(t('Container template filename must have a .php extension.'));
        }
        
        $location = new FileLocator\ThemeLocation($theme);
        if (!$this->filesystem->isFile(
            $location->getPath() . DIRECTORY_SEPARATOR . 'containers' . DIRECTORY_SEPARATOR . $templateFile
        )) {
            throw new UserMessageException(t('Container template file does not exist.'));
        }
        
        return true;
    }
    
}
