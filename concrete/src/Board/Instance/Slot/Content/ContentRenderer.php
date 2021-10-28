<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Page\Page;

class ContentRenderer implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * @var JsonSerializer
     */
    protected $serializer;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Page
     */
    protected $currentPage;

    /**
     * @var TemplateLocator
     */
    protected $templateLocator;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_CONTENT;
    }

    /**
     * ContentRenderer constructor.
     * @param JsonSerializer $serializer
     */
    public function __construct(Application $app, JsonSerializer $serializer, TemplateLocator $templateLocator, Page $currentPage = null)
    {
        $this->templateLocator = $templateLocator;
        $this->currentPage = $currentPage;
        $this->app = $app;
        $this->serializer = $serializer;
    }

    public function denormalizeIntoCollection($data) : ?ObjectCollection
    {
        return $this->serializer->denormalize($data, ObjectCollection::class, 'json',
            ['app' => $this->app]);
    }

    public function render(ObjectCollection $collection, SlotTemplate $template)
    {
        $file = $this->templateLocator->getFileToRender($template);
        if ($file) {
            $slot = $this->app->make(ContentSlotRenderer::class, ['data' => $collection]);
            ob_start();
            include $file;
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        } else if ($template->getHandle()) {
            $this->logger->notice(t('Error rendering board slot template on page %s - Unable to locate file for template: %s',
                    $this->currentPage->getCollectionID(), $template->getHandle())
            );
        }
    }




}
