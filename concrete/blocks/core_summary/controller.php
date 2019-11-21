<?php
namespace Concrete\Block\CoreSummary;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Template\Renderer;
use Doctrine\ORM\EntityManager;

class Controller extends BlockController
{
    protected $btTable = 'btCoreSummary';
    protected $btIsInternal = true;
    protected $btIgnorePageThemeGridFrameworkContainer = true;
    
    public $data;
    
    public $templateID;
    
    public function getBlockTypeDescription()
    {
        return t("Proxy block for summary blocks.");
    }

    public function getBlockTypeName()
    {
        return t("Summary");
    }
    
    public function view()
    {
        $template = null;
        if ($this->templateID) {
            $template = $this->app->make(EntityManager::class)
                ->find(Template::class, $this->templateID);
            $renderer = $this->app->make(Renderer::class);
            $collection = $renderer->denormalizeIntoCollection(json_decode($this->data, true));
            $this->set('dataCollection', $collection);
            $this->set('renderer', $renderer);
            $this->set('template', $template);
        }
    }
    
}
