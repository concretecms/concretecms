<?php
namespace Concrete\Core\Page\Theme\GridFramework\Type;

use Concrete\Core\Page\Theme\GridFramework\GridFramework;

class Bootstrap3 extends GridFramework
{
    /**
     * @since 5.7.5
     */
    public function supportsNesting()
    {
        return true;
    }

    public function getPageThemeGridFrameworkName()
    {
        return t('Twitter Bootstrap');
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
            'col-sm-1',
            'col-sm-2',
            'col-sm-3',
            'col-sm-4',
            'col-sm-5',
            'col-sm-6',
            'col-sm-7',
            'col-sm-8',
            'col-sm-9',
            'col-sm-10',
            'col-sm-11',
            'col-sm-12',
        );

        return $columns;
    }

    public function getPageThemeGridFrameworkColumnOffsetClasses()
    {
        $offsets = array(
            'col-sm-offset-1',
            'col-sm-offset-2',
            'col-sm-offset-3',
            'col-sm-offset-4',
            'col-sm-offset-5',
            'col-sm-offset-6',
            'col-sm-offset-7',
            'col-sm-offset-8',
            'col-sm-offset-9',
            'col-sm-offset-10',
            'col-sm-offset-11',
            'col-sm-offset-12',
        );

        return $offsets;
    }

    /**
     * @since 5.7.2.1
     */
    public function getPageThemeGridFrameworkColumnAdditionalClasses()
    {
        return '';
    }

    /**
     * @since 5.7.2.1
     */
    public function getPageThemeGridFrameworkColumnOffsetAdditionalClasses()
    {
        return '';
    }

    /**
     * @since 5.7.5
     */
    public function getPageThemeGridFrameworkHideOnExtraSmallDeviceClass()
    {
        return 'hidden-xs';
    }

    /**
     * @since 5.7.5
     */
    public function getPageThemeGridFrameworkHideOnSmallDeviceClass()
    {
        return 'hidden-sm';
    }

    /**
     * @since 5.7.5
     */
    public function getPageThemeGridFrameworkHideOnMediumDeviceClass()
    {
        return 'hidden-md';
    }

    /**
     * @since 5.7.5
     */
    public function getPageThemeGridFrameworkHideOnLargeDeviceClass()
    {
        return 'hidden-lg';
    }
}
