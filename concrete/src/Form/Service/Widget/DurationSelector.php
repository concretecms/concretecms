<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Foundation\Repetition\BasicRepetition;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Punic\Calendar as PunicCalendar;

class DurationSelector
{

    protected function getDefaultRepetition($timezone = null)
    {
        $app = Application::getFacadeApplication();
        if (!$timezone) {
            if (\Config::get('concrete.misc.user_timezones')) {
                $user = $app->make(User::class);
                $userInfo = $user->getUserInfoObject();
                $timezone = $userInfo->getUserTimezone();
            } else {
                $site = \Core::make('site')->getSite();
                $timezone = $site->getConfigRepository()->get('timezone');
            }
        }

        $service = \Core::make('helper/date');
        $now = $service->toDateTime('now', $timezone);
        $startDate = $now->format('Y-m-d H:i:s');

        $repetition = new BasicRepetition($timezone);
        $repetition->setStartDate($startDate);
        return $repetition;
    }

    public function selectDuration($namespace, $repetition = null, $timezone = null, $allowRepeat = true, $allowMultiple = true)
    {
        $repetitions = array();
        $baseRepetition = $this->getDefaultRepetition($timezone);

        if (is_array($repetition)) {
            $repetitions = $repetition;
        } else if (is_object($repetition)) {
            $repetitions[] = $repetition;
        } else {
            $repetitions[] = $baseRepetition;
        }
        
        ob_start();
        View::element('date_time/duration');
        $contents = ob_get_contents();
        ob_end_clean();

        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);

        $date = new Date();
        $format = $date->getJQueryUIDatePickerFormat();

        $args = array(
            'dateFormat' => $format,
            'repetitions' => $repetitions,
            'baseRepetition' => $baseRepetition,
            'allowRepeat' => $allowRepeat,
            'allowMultiple' => $allowMultiple,
            'namespace' => $namespace,
            'timeFormat' => PunicCalendar::has12HoursClock() ? 12 : 24,
        );

        $args = json_encode($args);

        $add = t('Add Date/Time');

        $html = <<<EOL
        {$contents}
        <div data-duration-selector-wrapper="{$identifier}">
            <div data-duration-selector="{$identifier}"></div>
EOL;

        if ($allowMultiple) {
            $html .= '<button data-action="add-duration" type="button" class="btn btn-sm btn-secondary float-end">' . $add . '</button>';
        }

        $html .= <<<EOL
        </div>
        <script type="text/javascript">
        $(function() {
            $('[data-duration-selector-wrapper={$identifier}]').concreteDurationSelector({$args});
        });
        </script>
EOL;

        return $html;
    }


}
