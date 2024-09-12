<?php

namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class CacheSettingField extends AbstractField
{
    protected $requestVariables = [
        'cacheSetting',
    ];

    public function getKey()
    {
        return 'cache_setting';
    }

    public function getDisplayName()
    {
        return t('Cache Setting');
    }

    /**
     * @param ItemList $list
     */
    public function filterList(ItemList $list)
    {
        if (isset($this->data['cacheSetting'])) {
            $list->filterByCacheSettings($this->getData('cacheSetting'));
        }
    }

    public function renderSearchField()
    {
        $settingOptions = [
            '1' => [
                'value' => '-1',
                'label' => t('Use global setting'),
            ],
            '2' => [
                'value' => '0',
                'label' => t('Do not cache'),
            ],
            '3' => [
                'value' => '1',
                'label' => t('Cache page'),
            ],
        ];
        $app = Application::getFacadeApplication();

        $form = $app->make('helper/form');
        $html = '';
        foreach ($settingOptions as $key => $value) {
            $html .= '<div class="form-check">' . $form->radio('cacheSetting', $value['value'], $this->getData('cacheSetting')) . $form->label('cacheSetting' . $key, $value['label'], ['class' => 'form-check-label']) . '</div>';
        }

        return $html;
    }
}
