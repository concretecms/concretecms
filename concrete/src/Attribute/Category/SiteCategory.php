<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\SiteKey;
use Concrete\Core\Entity\Site\Site;

class SiteCategory extends AbstractStandardCategory
{
    public function createAttributeKey()
    {
        return new SiteKey();
    }

    public function getIndexedSearchTable()
    {
        return 'SiteSearchIndexAttributes';
    }

    /**
     * @param $mixed Site
     *
     * @return mixed
     */
    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getSiteID();
    }

    public function getSearchIndexFieldDefinition()
    {
        return [
            'columns' => [
                [
                    'name' => 'siteID',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'default' => 0, 'notnull' => true],
                ],
            ],
            'primary' => ['siteID'],
        ];
    }

    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\SiteKey');
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\SiteValue');
    }

    public function getAttributeValues($site)
    {
        $values = $this->getAttributeValueRepository()->findBy([
            'site' => $site,
        ]);

        return $values;
    }

    public function getAttributeValue(Key $key, $site)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\SiteValue');
        $value = $r->findOneBy([
            'site' => $site,
            'attribute_key' => $key,
        ]);

        return $value;
    }
}
