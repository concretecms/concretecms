<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use League\Fractal\TransformerAbstract;

class OptionListOptionTransformer extends TransformerAbstract
{

    public function transform(SelectValueOption $option)
    {
        $data['id'] = $option->getSelectAttributeOptionID();
        $data['value'] = $option->getSelectAttributeOptionValue();
        $data['display_value'] = $option->getSelectAttributeOptionDisplayValue('string');
        return $data;
    }

}
