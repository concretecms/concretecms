<?php

namespace Concrete\Core\Api\Attribute;

use Concrete\Core\Attribute\Category\CategoryInterface;

class AttributeValueMapFactory
{

    public function createFromRequestData(CategoryInterface $category, array $body)
    {
        $attributeValueMap = new AttributeValueMap();
        foreach ($body as $key => $data) {
            $attributeKey = $category->getAttributeKeyByHandle($key);
            if ($attributeKey) {
                $controller = $attributeKey->getController();
                if ($controller instanceof SupportsAttributeValueFromJsonInterface) {
                    $value = $controller->createAttributeValueFromNormalizedJson($data);
                } else {
                    $value = $controller->createAttributeValue((string) $data);
                }
                if ($value) {
                    $entry = new AttributeValueMapEntry($attributeKey, $value);
                    $attributeValueMap->addEntry($entry);
                }
            }
        }
        return $attributeValueMap;
    }


}