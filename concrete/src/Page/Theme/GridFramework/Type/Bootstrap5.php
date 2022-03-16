<?php
namespace Concrete\Core\Page\Theme\GridFramework\Type;

use Concrete\Core\Page\Theme\GridFramework\GridFramework;

class Bootstrap5 extends GridFramework
{
    public function supportsNesting()
    {
        return true;
    }

    public function getPageThemeGridFrameworkName()
    {
        return t('Bootstrap 5');
    }

    public function getPageThemeGridFrameworkRowStartHTML()
    {
        return '<div class="row">';
    }

    public function getPageThemeGridFrameworkRowEndHTML()
    {
        return '</div>';
    }

    public function getPageThemeGridFrameworkContainerStartHTML()
    {
        return '<div class="container">';
    }

    public function getPageThemeGridFrameworkContainerEndHTML()
    {
        return '</div>';
    }

    public function getPageThemeGridFrameworkColumnClasses()
    {
        $columns = array(
            'col-md-1',
            'col-md-2',
            'col-md-3',
            'col-md-4',
            'col-md-5',
            'col-md-6',
            'col-md-7',
            'col-md-8',
            'col-md-9',
            'col-md-10',
            'col-md-11',
            'col-md-12',
        );

        return $columns;
    }

    public function getPageThemeGridFrameworkColumnOffsetClasses()
    {
        $offsets = array(
            'offset-md-1',
            'offset-md-2',
            'offset-md-3',
            'offset-md-4',
            'offset-md-5',
            'offset-md-6',
            'offset-md-7',
            'offset-md-8',
            'offset-md-9',
            'offset-md-10',
            'offset-md-11',
            'offset-md-12',
        );

        return $offsets;
    }

    public function getPageThemeGridFrameworkColumnAdditionalClasses()
    {
        return '';
    }

    public function getPageThemeGridFrameworkColumnOffsetAdditionalClasses()
    {
        return '';
    }

    public function getPageThemeGridFrameworkHideOnExtraSmallDeviceClass()
    {
        return 'd-none d-sm-block';
    }

    public function getPageThemeGridFrameworkHideOnSmallDeviceClass()
    {
        return 'd-sm-none d-md-block';
    }

    public function getPageThemeGridFrameworkHideOnMediumDeviceClass()
    {
        return 'd-md-none d-lg-block';
    }

    public function getPageThemeGridFrameworkHideOnLargeDeviceClass()
    {
        return 'd-lg-none .d-xl-block';
    }
}
