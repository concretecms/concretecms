<?php
namespace Concrete\Core\Page\Theme\GridFramework\Type;

use Loader;
use Concrete\Core\Page\Theme\GridFramework\GridFramework;

class NineSixty extends GridFramework
{

    public function getPageThemeGridFrameworkName()
    {
        return t('Nine Sixty Grid System');
    }

    public function getPageThemeGridFrameworkContainerStartHTML()
    {
        return '<div class="container_12">';
    }

    public function getPageThemeGridFrameworkContainerEndHTML()
    {
        return '</div>';
    }

    public function getPageThemeGridFrameworkRowStartHTML()
    {
        return '<div class="row">';
    }

    public function getPageThemeGridFrameworkRowEndHTML()
    {
        return '</div>';
    }

    public function getPageThemeGridFrameworkColumnClasses()
    {
        $columns = array(
            'grid_1',
            'grid_2',
            'grid_3',
            'grid_4',
            'grid_5',
            'grid_6',
            'grid_7',
            'grid_8',
            'grid_9',
            'grid_10',
            'grid_11',
            'grid_12',
            'grid_13',
            'grid_14',
            'grid_15',
            'grid_16',
            'grid_17',
            'grid_18',
            'grid_19',
            'grid_20',
            'grid_21',
            'grid_22',
            'grid_23',
            'grid_24'
        );
        return $columns;
    }

    public function getPageThemeGridFrameworkColumnOffsetClasses()
    {
        $offsets = array( // this is used as suffix / prefix in this framework. Just using suffixes for now.
            'suffix_1',
            'suffix_2',
            'suffix_3',
            'suffix_4',
            'suffix_5',
            'suffix_6',
            'suffix_7',
            'suffix_8',
            'suffix_9',
            'suffix_10',
            'suffix_11',
            'suffix_12',
            'suffix_13',
            'suffix_14',
            'suffix_15',
            'suffix_16',
            'suffix_17',
            'suffix_18',
            'suffix_19',
            'suffix_20',
            'suffix_21',
            'suffix_22',
            'suffix_23',
            'suffix_24'
        );
        return $offsets;
    }

    public function getAggregatorGridItemMargin()
    {
        return 5;
    }

    public function getAggregatorGridItemWidth()
    {
        return 30; // not sure
    }

    public function getAggregatorGridItemHeight()
    {
        return 120; // not sure
    }

    public function getPageThemeGridFrameworkColumnAdditionalClasses()
    {
        return '';
    }

    public function getPageThemeGridFrameworkColumnOffsetAdditionalClasses()
    {
        return '';
    }

}
