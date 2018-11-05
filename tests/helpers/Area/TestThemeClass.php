<?php

namespace Concrete\TestHelpers\Area;

use Concrete\Core\Area\Layout\Preset\Provider\ThemeProviderInterface;

class TestThemeClass implements ThemeProviderInterface
{
    public function getThemeHandle()
    {
        return 'test_theme';
    }

    public function getThemeName()
    {
        return 'Test Theme';
    }

    public function getThemeAreaLayoutPresets()
    {
        $presets = [
            [
                'handle' => 'left_sidebar',
                'name' => 'Left Sidebar',
                'container' => '<div class="row"></div>',
                'columns' => [
                    '<div class="col-sm-4"></div>',
                    '<div class="col-sm-8"></div>',
                ],
            ],
            [
                'handle' => 'exciting',
                'name' => 'Exciting',
                'container' => '<div class="row"></div>',
                'columns' => [
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"></div>',
                    '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 visible-lg"></div>',
                ],
            ],
            [
                'handle' => 'three_column',
                'name' => 'Three Column',
                'container' => '<div class="row"></div>',
                'columns' => [
                    '<div class="col-md-4"></div>',
                    '<div class="col-md-4"></div>',
                    '<div class="col-md-4"></div>',
                ],
            ],
            [
                'handle' => 'test_layout',
                'name' => 'Test Layout',
                'container' => '<div class="row" data-testing="top-row"></div>',
                'columns' => [
                    '<div data-foo="foo" class="col-md-2 col-sm-3"></div>',
                    '<div data-bar="bar" class="col-md-10 col-sm-9"></div>',
                ],
            ],
        ];

        return $presets;
    }
}
