<?php

namespace Concrete\Core\Entity\Board\DataSource\Configuration;

use Concrete\Core\Utility\Service\Xml;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardConfiguredDataSourceConfigurationPage")
 */
class PageConfiguration extends Configuration
{

    /** @ORM\Embedded(class = "\Concrete\Core\Entity\Search\Query") */
    protected $query;

    /**
     * @return \Concrete\Core\Entity\Search\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query): void
    {
        $this->query = $query;
    }
    
    public function export(\SimpleXMLElement $element)
    {
        if ($this->query) {
            $fields = $this->query->getFields();
            if (count($fields)) {
                $fieldsNode = $element->addChild('fields');
                foreach ($fields as $field) {
                    $field->export($fieldsNode);
                }
            }
        }
    }


}
