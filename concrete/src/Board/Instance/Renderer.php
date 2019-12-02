<?php
namespace Concrete\Core\Board\Instance;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Board\Instance\Slot\SlotRenderer;
use Concrete\Core\Board\Template\TemplateLocator;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Page\Theme\Theme;

class Renderer implements ApplicationAwareInterface
{
    
    use ApplicationAwareTrait;

    /**
     * @var TemplateLocator
     */
    protected $templateLocator;

    /**
     * @var Theme 
     */
    protected $theme;
    
    /**
     * Renderer constructor.
     * @param TemplateLocator $templateLocator
     */
    public function __construct(TemplateLocator $templateLocator, Theme $theme)
    {
        $this->templateLocator = $templateLocator;
        $this->theme = $theme;
    }
    
    public function render(Instance $instance)
    {
        $file = $this->templateLocator->getFileToRender($this->theme, $instance->getBoard()->getTemplate());
        if ($file) {
            $slot = $this->app->make(SlotRenderer::class, ['instance' => $instance]);
            include $file;
        }
    }


}
