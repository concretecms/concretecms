<?php

namespace Concrete\Core\Summary\Template;

use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\SummaryObject;
use Concrete\Core\Summary\SummaryObjectExtractor;
use Concrete\Core\Summary\SummaryObjectInspector;
use Concrete\Core\Summary\SummaryObjectInterface;
use Doctrine\ORM\EntityManager;

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
     * @var JsonSerializer
     */
    protected $serializer;

    /**
     * @var RendererFilterer
     */
    protected $rendererFilterer;

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var SummaryObjectExtractor
     */
    protected $summaryObjectExtractor;

    /**
     * @var SummaryObjectInspector
     */
    protected $summaryObjectInspector;

    public function __construct(
        JsonSerializer $serializer,
        RendererFilterer $rendererFilterer,
        EntityManager $entityManager,
        TemplateLocator $templateLocator,
        FileLocator $fileLocator,
        SummaryObjectExtractor $summaryObjectExtractor,
        SummaryObjectInspector $summaryObjectInspector,
        Page $currentPage = null
    ) {
        $this->serializer = $serializer;
        $this->rendererFilterer = $rendererFilterer;
        $this->entityManager = $entityManager;
        $this->templateLocator = $templateLocator;
        $this->fileLocator = $fileLocator;
        $this->summaryObjectExtractor = $summaryObjectExtractor;
        $this->summaryObjectInspector = $summaryObjectInspector;
        $this->currentPage = $currentPage;
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_CONTENT;
    }

    /**
     * @param array $summaryObjectFields
     * @param Template $template
     */
    protected function summaryObjectSupportsTemplate($summaryObjectFields, Template $template)
    {
        $summaryObjectFieldIdentifiers = [];
        $templateRequiredFieldIdentifiers = [];
        foreach($summaryObjectFields as $identifier => $summaryObjectField) {
            $summaryObjectFieldIdentifiers[] = $identifier;
        }
        foreach($template->getRequiredFields() as $requiredField) {
            $templateRequiredFieldIdentifiers[] = $requiredField->getFieldIdentifier();
        }
        foreach($templateRequiredFieldIdentifiers as $templateRequiredFieldIdentifier) {
            if (!in_array($templateRequiredFieldIdentifier, $summaryObjectFieldIdentifiers)) {
                return false;
            }
        }
        return true;
    }

    public function render(SummaryObjectInterface $summaryObject)
    {
        $template = $summaryObject->getTemplate();
        $file = $this->templateLocator->getFileToRender($template);
        if ($file) {
            $summaryObjectInspector = $this->summaryObjectInspector; // This is included here for use in the template.
            $summaryObjectFields = $this->summaryObjectExtractor->getData($summaryObject);
            if ($this->summaryObjectSupportsTemplate($summaryObjectFields, $template)) {
                // note: we used to include <span class="ccm-summary-template-header"></span> around this, but it's
                // too prescriptive and annoying, it causes problems with more advanced flexbox styling.

                extract($summaryObjectFields, EXTR_OVERWRITE);
                include $file;
            }
        } else {
            if ($template->getHandle()) {
                if ($this->currentPage) {
                    $this->logger->notice(
                        t(
                            'Error rendering summary template on page %s - Unable to locate file for summary template: %s',
                            $this->currentPage->getCollectionID(),
                            $template->getHandle()
                        )
                    );
                } else {
                    $this->logger->notice(
                        t(
                            'Error rendering summary template - Unable to locate file for summary template: %s',
                            $template->getHandle()
                        )
                    );
                }
            }
        }
    }

    public function renderSummaryForObject(CategoryMemberInterface $object, string $templateHandle = null)
    {
        $categoryTemplate = null;
        if ($templateHandle) {
            $categoryTemplate = $this->rendererFilterer->getSpecificTemplateIfExists($object, $templateHandle);
        } else {
            $categoryTemplate = $this->rendererFilterer->getRandomTemplate($object);
        }
        if ($categoryTemplate) {
            $template = $categoryTemplate->getTemplate();
            if ($template) {
                $collection = $categoryTemplate->getData();

                $object = new SummaryObject(
                    $object->getSummaryCategoryHandle(),
                    $object->getSummaryIdentifier(),
                    $template,
                    $collection
                );
                $this->render($object);
            }
        }
    }


}
