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
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var bool
     */
    protected $enableEditing = false;

    public function __construct(FileLocator $fileLocator, TemplateLocator $templateLocator)
    {
        $this->fileLocator = $fileLocator;
        $this->templateLocator = $templateLocator;
    }

    /**
     * @param bool $enableEditing
     */
    public function setEnableEditing($enableEditing)
    {
        $this->enableEditing = $enableEditing;
    }

    public function render(Instance $instance)
    {
        $site = $instance->getsite();
        $home = $site->getSiteHomePageObject();
        $theme = $home->getCollectionThemeObject();

        $file = $this->templateLocator->getFileToRender($theme, $instance->getBoard()->getTemplate());
        if ($file) {
            if ($this->enableEditing) {
                include $this->fileLocator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_BOARDS . '/instance_header.php')
                    ->getFile();
            }
            $slotCollectionFactory = $this->app->make(RenderedSlotCollectionFactory::class);
            $slotCollection = $slotCollectionFactory->createCollection($instance);
            $slot = $this->app->make(SlotRenderer::class, ['renderedSlotCollection' => $slotCollection]);
            $slot->setEnableEditing($this->enableEditing);

            include $file;

            if ($this->enableEditing) {
                include $this->fileLocator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_BOARDS . '/instance_footer.php')
                    ->getFile();
            }
        }

    }


}
