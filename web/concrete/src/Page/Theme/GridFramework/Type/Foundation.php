<?php
namespace Concrete\Core\Page\Theme\GridFramework\Type;

use Concrete\Core\Page\Theme\GridFramework\GridFramework;

defined('C5_EXECUTE') or die('Access Denied.');

class Foundation extends GridFramework
{
    public function supportsNesting()
    {
        return true;
    }

    public function getPageThemeGridFrameworkName()
    {
        return t('Foundation');
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
        return '';
    }

    public function getPageThemeGridFrameworkContainerEndHTML()
    {
        return '';
    }

    public function getPageThemeGridFrameworkColumnClasses()
    {
        $columns = array(
            'medium-1 ',
            'medium-2 ',
            'medium-3',
            'medium-4',
            'medium-5',
            'medium-6',
            'medium-7',
            'medium-8',
            'medium-9',
            'medium-10',
            'medium-11',
            'medium-12',
        );

        return $columns;
    }

    public function getPageThemeGridFrameworkColumnOffsetClasses()
    {
        $offsets = array(
            'medium-offset-1',
            'medium-offset-2',
            'medium-offset-3',
            'medium-offset-4',
            'medium-offset-5',
            'medium-offset-6',
            'medium-offset-7',
            'medium-offset-8',
            'medium-offset-9',
            'medium-offset-10',
            'medium-offset-11',
            'medium-offset-12',
        );

        return $offsets;
    }

    public function getPageThemeGridFrameworkColumnAdditionalClasses()
    {
        return 'columns';
    }

    public function getPageThemeGridFrameworkColumnOffsetAdditionalClasses()
    {
        return 'columns';
    }
}
