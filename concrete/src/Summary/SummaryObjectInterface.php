<?php
namespace Concrete\Core\Summary;

use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Data\Collection;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;

interface SummaryObjectInterface
{

    public function getTemplate(): Template;
    
    public function getData() : Collection;
    
    public function setData(Collection $data);
    
    public function getIdentifier();
    
    public function getDataSourceCategoryHandle() : string;
    


}
