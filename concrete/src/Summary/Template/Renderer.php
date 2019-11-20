<?php

namespace Concrete\Core\Summary\Template;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Serializer;

class Renderer implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var TemplateLocator
     */
    protected $templateLocator;

    /**
     * @var Page
     */
    protected $currentPage;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var RendererFilterer 
     */
    protected $rendererFilterer;
    
    public function __construct(
        Serializer $serializer, 
        RendererFilterer $rendererFilterer,
        EntityManager $entityManager, 
        TemplateLocator $templateLocator, 
        Page $currentPage)
    {
        $this->serializer = $serializer;
        $this->rendererFilterer = $rendererFilterer;
        $this->entityManager = $entityManager;
        $this->templateLocator = $templateLocator;
        $this->currentPage = $currentPage;
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_CONTENT;
    }

    public function renderSummary(CategoryMemberInterface $object, string $templateHandle = null)
    {
        $allTemplates = $object->getSummaryTemplates();
        $customTemplates = null;
        if ($object->hasCustomSummaryTemplates()) {
            $customTemplates = $object->getCustomSelectedSummaryTemplates();
        }
        
        if ($template = $this->rendererFilterer->getMatchingTemplate(
            $allTemplates, $customTemplates, $templateHandle
        )) {
            $file = $this->templateLocator->getFileToRender($this->currentPage, $template->getTemplate());
            if ($file) {
                $data = $template->getData();
                $collection = $this->serializer->denormalize($data, Collection::class, 'json');
                $fields = $collection->getFields();
                extract($fields, EXTR_OVERWRITE);
                include $file;
            } else if ($templateHandle) {
                $this->logger->notice(t('Error rendering summary template on page %s - Unable to locate file for summary template: %s', 
                    $this->currentPage->getCollectionID(), $templateHandle)
                );
            }
        }

    }


}
