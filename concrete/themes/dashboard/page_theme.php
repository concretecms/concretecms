<?php
namespace Concrete\Theme\Dashboard;

use Concrete\Core\Area\Layout\Preset\Provider\ThemeProviderInterface;

/**
 * @since 8.0.0
 */
class PageTheme extends \Concrete\Core\Page\Theme\Theme
{

    protected $pThemeGridFrameworkHandle = 'bootstrap3';

    public function getThemeBlockClasses()
    {
        return array(
            'rss_displayer' => array('concrete5-org-stories')
        );
    }

}
