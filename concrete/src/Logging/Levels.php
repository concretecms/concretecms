<?php
namespace Concrete\Core\Logging;

use Monolog\Logger as Monolog;

class Levels
{

    /**
     * Gets the name of the logging level.
     *
     * @param  int $level
     *
     * @return string
     */
    public static function getLevelDisplayName($level)
    {
        $levels = Monolog::getLevels();
        if (in_array($level, $levels)) {
            $level = array_search($level, $levels);
            switch($level) {
                case 'DEBUG':
                    return tc(/*i18n: Detailed debug information */ 'Log level', 'Debug');
                case 'INFO':
                    return tc(/*i18n: Interesting events */ 'Log level', 'Info');
                case 'NOTICE':
                    return tc(/*i18n: Uncommon events */ 'Log level', 'Notice');
                case 'WARNING':
                    return tc(/*i18n: Exceptional occurrences that are not errors */ 'Log level', 'Warning');
                case 'ERROR':
                    return tc(/*i18n: Runtime errors */ 'Log level', 'Error');
                case 'CRITICAL':
                    return tc(/*i18n: Critical conditions */ 'Log level', 'Critical');
                case 'ALERT':
                    return tc(/*i18n: Action must be taken immediately */ 'Log level', 'Alert');
                case 'EMERGENCY':
                    return tc(/*i18n: Urgent alert */ 'Log level', 'Emergency');
            }
        }
    }

}



