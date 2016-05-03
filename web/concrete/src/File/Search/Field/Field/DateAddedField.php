<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class DateAddedField extends AbstractField
{

    public function getKey()
    {
        return 'date_added';
    }

    public function getDisplayName()
    {
        return t('Date Added');
    }

    public function renderSearchField()
    {
        $wdt = \Core::make('helper/form/date_time');
        return $wdt->datetime('date_added_from', $wdt->translate('date_added_from', $searchRequest)) . t('to') . $wdt->datetime('date_added_to', $wdt->translate('date_added_to', $searchRequest));

    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list, $request)
    {
        $wdt = \Core::make('helper/form/date_time');
        /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
        $dateFrom = $wdt->translate('date_added_from', $request);
        if ($dateFrom) {
            $list->filterByDateAdded($dateFrom, '>=');
        }
        $dateTo = $wdt->translate('date_added_to', $request);
        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }
            $list->filterByDateAdded($dateTo, '<=');
        }
    }



}
