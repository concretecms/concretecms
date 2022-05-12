<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Application\Application;
use Concrete\Core\Design\Tag\Tag;
use Concrete\Core\Design\Tag\TagCollection;
use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverManager;
use Concrete\Core\Summary\Template\Renderer;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Concrete\Core\Summary\SummaryObjectInterface;
use Concrete\Core\Summary\SummaryObject as BaseSummaryObject;

class SummaryObject implements ObjectInterface 
{

    protected $summaryObject;
    
    public function __construct(SummaryObjectInterface $summaryObject = null)
    {
        if ($summaryObject) {
            $this->summaryObject = $summaryObject;
        }
    }

    /**
     * @return SummaryObjectInterface
     */
    public function getSummaryObject(): SummaryObjectInterface
    {
        return $this->summaryObject;
    }
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'templateID' => $this->summaryObject->getTemplate()->getId(),
            'dataSourceCategoryHandle' => $this->summaryObject->getDataSourceCategoryHandle(),
            'title' => $this->getSlotContentObjectTitle(),
            'identifier' => $this->summaryObject->getIdentifier(),
            'data' => $this->summaryObject->getData(),
        ];
    }

    public function getSlotContentObjectTitle(): ?string
    {
        if ($this->summaryObject && $this->summaryObject->getTemplate()) {
            return $this->summaryObject->getTemplate()->getName();
        }
        return null;
    }

    public function refresh(Application $app): void
    {
        $r = $app->make(EntityManager::class)->getRepository(Category::class);
        $category = $r->findOneByHandle($this->summaryObject->getDataSourceCategoryHandle());
        if ($category) {
            $object = $category->getDriver()->getCategoryMemberFromIdentifier($this->summaryObject->getIdentifier());
            if ($object) {
                $driverManager = $app->make(DriverManager::class);
                $driverCollection = $driverManager->getDriverCollection($object);
                $data = $driverCollection->extractData($object);
                $this->summaryObject->setData($data);
            }
        }
    }

    public function getDesignTags(): array
    {
        return $this->summaryObject->getTemplate()->getTags()->toArray();
    }

    public function display(Application $app): void
    {
        $renderer = $app->make(Renderer::class);
        $renderer->render($this->summaryObject);
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $entityManager = $context['app']->make(EntityManager::class);
        $template = $entityManager->find(Template::class, $data['templateID']);
        $identifier = $data['identifier'];
        $dataSourceCategoryHandle = $data['dataSourceCategoryHandle'];
        $data = $denormalizer->denormalize($data['data'], Collection::class, 'json');
        if ($template) {
            $this->summaryObject = new BaseSummaryObject($dataSourceCategoryHandle, $identifier, $template, $data);
        }
    }




}
