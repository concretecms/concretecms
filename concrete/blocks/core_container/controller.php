<?php
namespace Concrete\Block\CoreContainer;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Container\ContainerBlockInstance;
use Concrete\Core\Page\Container\ContainerExporter;
use Concrete\Core\Page\Container\TemplateLocator;
use Doctrine\ORM\EntityManager;

class Controller extends BlockController
{
    protected $btTable = 'btCoreContainer';
    protected $btIsInternal = true;
    protected $btIgnorePageThemeGridFrameworkContainer = true;
    
    public $containerInstanceID;
    
    public function getBlockTypeDescription()
    {
        return t("Proxy block for theme containers added through the UI.");
    }

    public function getBlockTypeName()
    {
        return t("Container");
    }
    
    protected function getContainerInstanceObject()
    {
        $entityManager = $this->app->make(EntityManager::class);
        if ($this->containerInstanceID) {
            $instance = $entityManager->find(Container\Instance::class, $this->containerInstanceID);
            return $instance;
        }
        return null;
    }
    
    public function view()
    {
        $template = null;
        $instance = $this->getContainerInstanceObject();
        if ($instance) {
            $container = $instance->getContainer();
            if ($container) {
                $containerBlockInstance = $this->app->make(ContainerBlockInstance::class, 
                    ['block' => $this->getBlockObject(), 'instance' => $instance]);
                $locator = $this->app->make(TemplateLocator::class);
                // no this is not a typo. Aesthetically it looks nice to pass $container to the container area
                // constructor, but we need the instance object, not just the outer container object.
                $this->set('container', $containerBlockInstance);
                $this->set('fileToRender', $locator->getFileToRender($this->getCollectionObject(), $container));
            }
        }
    }
    
    public function save($data)
    {
        $entityManager = $this->app->make(EntityManager::class);
        if (isset($data['constainerInstanceID'])) {
            
        } else {
            $container = $entityManager->find(Container::class, $data['containerID']);
            if ($container) {
                $instance = new Container\Instance();
                $instance->setContainer($container);
                $entityManager->persist($instance);
                $entityManager->flush();
                $data['containerInstanceID'] = $instance->getContainerInstanceID();
            }
        }
        parent::save($data);
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $instance = $this->getContainerInstanceObject();
        if ($instance) {
            $page = $this->getBlockObject()->getBlockCollectionObject();
            $exporter = new ContainerExporter($page);
            $exporter->export($instance, $blockNode);
        }
    }

}
