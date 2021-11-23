<?php

namespace Concrete\Core\Logging\Search\Field\Field;

use Concrete\Core\Logging\LogList;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class DateField extends AbstractField
{
    protected $requestVariables = [
        'date_from_dt',
        'date_from_h',
        'date_from_m',
        'date_from_a',
        'date_to_dt',
        'date_to_h',
        'date_to_m',
        'date_to_a',
    ];

    public function getKey()
    {
        return 'date';
    }

    public function getDisplayName()
    {
        return t('Date');
    }

    /**
     * @param LogList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $wdt */
        $wdt = $app->make(DateTime::class);

        $dateFrom = $wdt->translate('date_from', $this->data);

        if ($dateFrom) {
            $list->filterByStartTime($dateFrom);
        }

        $dateTo = $wdt->translate('date_to', $this->data);
        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }
            $list->filterByEndTime($dateTo);
        }
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $wdt */
        $wdt = $app->make(DateTime::class);
        return $wdt->datetime('date_from', $wdt->translate('date_from', $this->data)) . t('to') . $wdt->datetime('date_to', $wdt->translate('date_to', $this->data));
    }

}
