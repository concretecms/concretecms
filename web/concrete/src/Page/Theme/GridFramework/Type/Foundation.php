<?php 
namespace Concrete\Core\Page\Theme\GridFramework\Type;

use Concrete\Core\Page\Theme\GridFramework\GridFramework;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Foundation extends GridFramework
{

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
            'small-1 ',
            'small-2 ',
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
        );
        return $columns;
    }

    public function getPageThemeGridFrameworkColumnOffsetClasses()
    {
        $offsets = array(
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