<?php
namespace Concrete\Core\Page\Theme\GridFramework\Type;

use Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\GridFramework\GridFramework;

class Foundation6 extends GridFramework
{
    public function getPageThemeGridFrameworkName()
    {
        return t('Foundation 6');
    }

    public function supportsNesting()
    {
        return true;
    }

    public function isFlex()
    {
        return true;
    }

    public function getPageThemeGridFrameworkRowStartHTML()
    {
        return '<div class="grid-x grid-margin-x">';
    }

    public function getPageThemeGridFrameworkRowEndHTML()
    {
        return '</div>';
    }

    public function getPageThemeGridFrameworkContainerStartHTML()
    {
        return '<div class="grid-container">';
    }

    public function getPageThemeGridFrameworkContainerEndHTML()
    {
        return '</div>';
    }

    public function getPageThemeGridFrameworkColumnClasses()
    {
        return [
            'small-1',
            'small-2',
            'small-3',
            'small-4',
            'small-5',
            'small-6',
            'small-7',
            'small-8',
            'small-9',
            'small-10',
            'small-11',
            'small-12',
        ];
    }

    public function getPageThemeGridFrameworkColumnOffsetClasses()
    {
        return [
            'small-offset-1',
            'small-offset-2',
            'small-offset-3',
            'small-offset-4',
            'small-offset-5',
            'small-offset-6',
            'small-offset-7',
            'small-offset-8',
            'small-offset-9',
            'small-offset-10',
            'small-offset-11',
            'small-offset-12',
        ];
    }

    public function getPageThemeGridFrameworkColumnAdditionalClasses()
    {
        return 'cell';
    }

    public function getPageThemeGridFrameworkColumnOffsetAdditionalClasses()
    {
        return '';
    }

    public function getPageThemeGridFrameworkHideOnSmallDeviceClass()
    {
        return 'hide-for-small-only';
    }

    public function getPageThemeGridFrameworkHideOnMediumDeviceClass()
    {
        return 'hide-for-medium-only';
    }

    public function getPageThemeGridFrameworkHideOnLargeDeviceClass()
    {
        return 'hide-for-large-only';
    }
}