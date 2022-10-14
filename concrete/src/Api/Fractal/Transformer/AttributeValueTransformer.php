<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Api\Attribute\FractalTransformableInterface;
use League\Fractal\TransformerAbstract;

class AttributeValueTransformer extends TransformerAbstract
{

    public function transform(AbstractValue $value)
    {
        $key = $value->getAttributeKey();
        $type = $value->getAttributeTypeObject();
        if ($key && $type) {
            $controller = $value->getController();
            if ($controller instanceof FractalTransformableInterface) {
                $transformer = $controller->getApiDataTransformer();
                $attributeValue = $transformer->transform($value);
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
