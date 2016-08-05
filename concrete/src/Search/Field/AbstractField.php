<?php
namespace Concrete\Core\Search\Field;

use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractField implements FieldInterface
{

    protected $data = array();
    protected $requestVariables = array();

    public function renderSearchField()
    {
        return '';
    }

    public function jsonSerialize()
    {
        return [
            'key' => $this->getKey(),
            'label' => $this->getDisplayName(),
            'element' => $this->renderSearchField(),
            'data' => $this->data
        ];
    }

    public function loadDataFromRequest(array $request)
    {
        foreach($request as $key => $value) {
            if (in_array($key, $this->requestVariables)) {
                $this->data[$key] = $value;
            }
        }
    }
}
