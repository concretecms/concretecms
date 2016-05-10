<?php
namespace Concrete\Core\Page\Theme\GridFramework\Type;

use Concrete\Core\Page\Theme\GridFramework\GridFramework;

class Bootstrap2 extends GridFramework
{
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

    public function getPageThemeGridFrameworkColumnAdditionalClasses()
    {
        return '';
    }

    public function getPageThemeGridFrameworkColumnOffsetAdditionalClasses()
    {
        return '';
    }

    public function getPageThemeGridFrameworkColumnClasses()
    {
        $columns = array(
            'span1',
            'span2',
            'span3',
            'span4',
            'span5',
            'span6',
            'span7',
            'span8',
            'span9',
            'span10',
            'span11',
            'span12',
        );

        return $columns;
    }

    public function getPageThemeGridFrameworkColumnOffsetClasses()
    {
        $offsets = array(
            'offset1',
            'offset2',
            'offset3',
            'offset4',
            'offset5',
            'offset6',
            'offset7',
            'offset8',
            'offset9',
            'offset10',
            'offset11',
            'offset12',
        );

        return $offsets;
    }
}
