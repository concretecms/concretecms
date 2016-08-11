<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class DatePublicField extends AbstractField
{

    protected $requestVariables = [
        'date_public_from_dt',
        'date_public_from_h',
        'date_public_from_m',
        'date_public_from_a',
        'date_public_to_dt',
        'date_public_to_h',
        'date_public_to_m',
        'date_public_to_a',
    ];

    public function getKey()
    {
        return 'date_public';
    }

    public function getDisplayName()
    {
        return t('Public Date');
    }

    public function renderSearchField()
    {
        $wdt = \Core::make('helper/form/date_time');
        return $wdt->datetime('date_public_from', $wdt->translate('date_public_from', $this->data)) . t('to') . $wdt->datetime('date_public_to', $wdt->translate('date_public_to', $this->data));

    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $wdt = \Core::make('helper/form/date_time');
        /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
        $dateFrom = $wdt->translate('date_public_from', $this->data);
        if ($dateFrom) {
            $list->filterByPublicDate($dateFrom, '>=');
        }
        $dateTo = $wdt->translate('date_public_to', $this->data);
        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }
            $list->filterByPublicDate($dateTo, '<=');
        }
    }



}
