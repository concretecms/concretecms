<?php

namespace Concrete\Core\Summary\Template;

use Concrete\Core\Entity\Summary\Template;
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

    public function __construct(Serializer $serializer, EntityManager $entityManager, TemplateLocator $templateLocator, Page $currentPage)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->templateLocator = $templateLocator;
        $this->currentPage = $currentPage;
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_CONTENT;
    }

    /**
     * @param string $templateHandle
     * @param RenderableTemplateInterface[] $templates
     * @return RenderableTemplateInterface
     */
    protected function getMatchingTemplate(string $templateHandle, array $templates): ?RenderableTemplateInterface
    {
        foreach ($templates as $template) {
            if ($template->getTemplate()->getHandle() === $templateHandle) {
                return $template;

            }
        }
        return null;
    }

    public function renderTemplate(string $templateHandle, CategoryMemberInterface $object)
    {
        $template = $this->entityManager->getRepository(Template::class)
            ->findOneByHandle($templateHandle);
        if (!$template) {
            throw new \RuntimeException(t('Unable to load summary template object by handle: %s', $templateHandle));
        }

        $templates = $object->getSummaryTemplates();
        if ($templates && count($templates) > 0) {
            if ($template = $this->getMatchingTemplate($templateHandle, $templates)) {
                $file = $this->templateLocator->getFileToRender($this->currentPage, $template->getTemplate());
                if ($file) {
                    $data = $template->getData();
                    $collection = $this->serializer->denormalize($data, Collection::class, 'json');
                    $fields = $collection->getFields();
                    extract($fields, EXTR_OVERWRITE);
                    include $file;
                } else {
                    $this->logger->notice(t('Unable to locate file for summary template: %s', $template->getHandle()));
                }
            }
        }

    }


}
