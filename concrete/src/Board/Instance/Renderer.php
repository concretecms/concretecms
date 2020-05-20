<?php
namespace Concrete\Core\Board\Instance;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Board\Instance\Slot\RenderedSlotCollection;
use Concrete\Core\Board\Instance\Slot\RenderedSlotCollectionFactory;
use Concrete\Core\Board\Instance\Slot\SlotRenderer;
use Concrete\Core\Board\Template\TemplateLocator;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Filesystem\FileLocator;
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
     * @var FileLocator
     */
    protected $fileLocator;

    public function __construct(FileLocator $fileLocator, TemplateLocator $templateLocator, Theme $theme)
    {
        $this->fileLocator = $fileLocator;
        $this->templateLocator = $templateLocator;
        $this->theme = $theme;
    }

    public function render(Instance $instance)
    {
        $file = $this->templateLocator->getFileToRender($this->theme, $instance->getBoard()->getTemplate());
        if ($file) {
            include $this->fileLocator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_BOARDS . '/instance_header.php')
                ->getFile();
            $slotCollectionFactory = $this->app->make(RenderedSlotCollectionFactory::class);
            $slotCollection = $slotCollectionFactory->createCollection($instance);
            $slot = $this->app->make(SlotRenderer::class, ['renderedSlotCollection' => $slotCollection]);
            include $file;
            include $this->fileLocator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_BOARDS . '/instance_footer.php')
                ->getFile();
        }
    }


}
