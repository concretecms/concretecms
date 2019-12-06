<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Template\RenderableTemplateInterface;
use Concrete\Core\Summary\Template\Renderer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SummaryObject implements ObjectInterface 
{

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var array
     */
    protected $data;
    
    /**
     * SummaryObject constructor.
     * @param RenderableTemplateInterface $template
     * @param Collection $data
     */
    public function __construct(RenderableTemplateInterface $template = null)
    {
        if ($template) {
            $this->template = $template->getTemplate();
            $this->data = $template->getData();
        }
    }

    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'templateID' => $this->template->getId(),
            'data' => $this->data
        ];
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    
    public function display(Application $app): void
    {
        $renderer = $app->make(Renderer::class);
        $renderer->render($this->data, $this->template);
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $entityManager = $context['app']->make(EntityManager::class);
        $template = $entityManager->find(Template::class, $data['templateID']);
        if ($template) {
            $this->template = $template;
        }
        $this->data = $denormalizer->denormalize($data['data'], Collection::class, 'json');
    }




}
