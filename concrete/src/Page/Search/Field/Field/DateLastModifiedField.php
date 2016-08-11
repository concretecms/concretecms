<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class DateLastModifiedField extends AbstractField
{

    protected $requestVariables = [
        'date_last_modified_from_dt',
        'date_last_modified_from_h',
        'date_last_modified_from_m',
        'date_last_modified_from_a',
        'date_last_modified_to_dt',
        'date_last_modified_to_h',
        'date_last_modified_to_m',
        'date_last_modified_to_a',
    ];

    public function getKey()
    {
        return 'date_last_modified';
    }

    public function getDisplayName()
    {
        return t('Last Modified');
    }

    public function renderSearchField()
    {
        $wdt = \Core::make('helper/form/date_time');
        return $wdt->datetime('date_last_modified_from', $wdt->translate('date_last_modified_from', $this->data)) . t('to') . $wdt->datetime('date_last_modified_to', $wdt->translate('date_last_modified_to', $this->data));

    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $wdt = \Core::make('helper/form/date_time');
        /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
        $dateFrom = $wdt->translate('date_last_modified_from', $this->data);
        if ($dateFrom) {
            $list->filterByDateLastModified($dateFrom, '>=');
        }
        $dateTo = $wdt->translate('date_last_modified_to', $this->data);
        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }
            $list->filterByDateLastModified($dateTo, '<=');
        }
    }



}
