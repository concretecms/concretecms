<?php
namespace Concrete\Core\Summary;

use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Data\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SummaryObject implements SummaryObjectInterface 
{

    /**
     * @var string
     */
    protected $dataSourceCategoryHandle;

    /**
     * @var mixed
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var Template
     */
    protected $template;
    
    public function __construct($dataSourceCategoryHandle, $identifier, $template, $data)
    {
        $this->dataSourceCategoryHandle = $dataSourceCategoryHandle;
        $this->data = $data;
        $this->template = $template;
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function getDataSourceCategoryHandle() : string
    {
        return $this->dataSourceCategoryHandle;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return Template
     */
    public function getTemplate() : Template
    {
        return $this->template;
    }

    /**
     * @return Collection
     */
    public function getData() : Collection
    {
        return $this->data;
    }

    /**
     * @param Collection $data
     */
    public function setData(Collection $data): void
    {
        $this->data = $data;
    }
    
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $entityManager = $context['app']->make(EntityManager::class);
        $this->identifier = $data['identifier'];
        $this->dataSourceCategoryHandle = $data['dataSourceCategoryHandle'];
        $template = $entityManager->find(Template::class, $data['templateID']);
        if ($template) {
            $this->template = $template;
        }
        $this->data = $denormalizer->denormalize($data['data'], Collection::class, 'json');
    }
    

}
