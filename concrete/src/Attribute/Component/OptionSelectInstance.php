<?php

namespace Concrete\Core\Attribute\Component;

use Concrete\Core\Attribute\View;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;

class OptionSelectInstance
{

    /**
     * @var string
     */
    protected $accessToken;


    /**
     * @var Key
     */
    protected $attributeKey;

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return Key
     */
    public function getAttributeKey(): Key
    {
        return $this->attributeKey;
    }

    /**
     * @param Key $attributeKey
     */
    public function setAttributeKey(Key $attributeKey): void
    {
        $this->attributeKey = $attributeKey;
    }

    /**
     * Returns the URL needed to search these options using the autocomplete component
     *
     * @return string
     */
    public function getDataSourceUrl(): string
    {
        $view = new View($this->getAttributeKey());
        return $view->action('load_autocomplete_values');
    }

    /**
     * Returns the URL needed to selected options when coming to a pre-filled option component
     *
     * @return string
     */
    public function getSelectedOptionsUrl(): string
    {
        $view = new View($this->getAttributeKey());
        return $view->action('select_autocomplete_values');
    }


    public function createResultFromOption(SelectValueOption $option): array
    {
        // We prefix these with SelectAttributeOption: because that's how we can differentiate
        // in the request between items that already exist in the db and new items.
        $data = [
            'id' => 'SelectAttributeOption:' . $option->getSelectAttributeOptionID(),
            'primary_label' => $option->getSelectAttributeOptionDisplayValue('text'),
        ];

        return $data;
    }


}
