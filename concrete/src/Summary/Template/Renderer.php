<?php
namespace Concrete\Core\Summary\Template;

use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
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
    
    
    public function __construct(EntityManager $entityManager, TemplateLocator $templateLocator, Page $currentPage)
    {
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
     * @return Template|null
     */
    protected function getMatchingTemplate(string $templateHandle, array $templates)
    {
        foreach($templates as $template) {
            if ($template->getTemplate()->getHandle() === $templateHandle) {
                return $template->getTemplate();

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
                $file = $this->templateLocator->getFileToRender($this->currentPage, $template);
                if ($file) {
                    include $file;
                } else {
                    $this->logger->notice(t('Unable to locate file for summary template: %s', $template->getHandle()));
                }
            }
        }
        
    }
    

}
