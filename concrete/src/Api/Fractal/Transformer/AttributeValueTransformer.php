<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\Attribute\SimpleApiAttributeValueInterface;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use League\Fractal\TransformerAbstract;

class AttributeValueTransformer extends TransformerAbstract
{

    public function transform(AbstractValue $value)
    {
        $key = $value->getAttributeKey();
        $type = $value->getAttributeTypeObject();
        if ($key && $type) {
            $controller = $value->getController();
            if ($controller instanceof ApiResourceValueInterface) {
                $attributeValueResource = $controller->getApiValueResource();
                if ($attributeValueResource) {
                    $attributeValue = $attributeValueResource->getTransformer()->transform(
                        $attributeValueResource->getData()
                    );
                }
            } else if ($controller instanceof SimpleApiAttributeValueInterface) {
                $attributeValue = $controller->getApiAttributeValue();
            } else {
                $attributeValue = $controller->getSearchIndexValue();
            }
            return [
                'id' => $value->getAttributeValueID(),
                'type' => $type->getAttributeTypeHandle(),
                'handle' => $key->getAttributeKeyHandle(),
                'value' => $attributeValue,
            ];
        }
    }

}
