<?php

namespace Concrete\Theme\Elemental;

use Concrete\Core\Area\Layout\Preset\Provider\ThemeProviderInterface;

class PageTheme extends \Concrete\Core\Page\Theme\Theme implements ThemeProviderInterface
{
    public function registerAssets()
    {
        //$this->providesAsset('javascript', 'bootstrap/*');
        $this->providesAsset('css', 'bootstrap/*');
        $this->providesAsset('css', 'blocks/form');
        $this->providesAsset('css', 'blocks/social_links');
        $this->providesAsset('css', 'blocks/share_this_page');
        $this->providesAsset('css', 'blocks/feature');
        $this->providesAsset('css', 'blocks/testimonial');
        $this->providesAsset('css', 'blocks/date_navigation');
        $this->providesAsset('css', 'blocks/topic_list');
        $this->providesAsset('css', 'blocks/faq');
        $this->providesAsset('css', 'blocks/tags');
        $this->providesAsset('css', 'core/frontend/*');
        $this->providesAsset('css', 'blocks/feature/templates/hover_description');
        $this->requireAsset('css', 'font-awesome');
        $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('javascript', 'picturefill');
        $this->requireAsset('javascript-conditional', 'html5-shiv');
        $this->requireAsset('javascript-conditional', 'respond');
    }

    protected $pThemeGridFrameworkHandle = 'bootstrap3';

    public function getThemeName()
    {
        return t('Elemental');
    }

    public function getThemeDescription()
    {
        return t('Elegant, spacious theme with support for blogs, portfolios, layouts and more.');
    }

    public function getThemeBlockClasses()
    {
        return array(
            'feature' => array('feature-home-page'),
            'page_list' => array(
                'recent-blog-entry',
                'blog-entry-list',
                'page-list-with-buttons',
                'block-sidebar-wrapped',
            ),
            'next_previous' => array('block-sidebar-wrapped'),
            'share_this_page' => array('block-sidebar-wrapped'),
            'content' => array(
                'block-sidebar-wrapped',
                'block-sidebar-padded',
            ),
            'date_navigation' => array('block-sidebar-padded'),
            'topic_list' => array('block-sidebar-wrapped'),
            'testimonial' => array('testimonial-bio'),
            'image' => array(
                'image-right-tilt',
                'image-circle',
            ),
        );
    }

    public function getThemeAreaClasses()
    {
        return array(
            'Page Footer' => array('area-content-accent'),
        );
    }

    public function getThemeDefaultBlockTemplates()
    {
        /*
        return array(
            'image' => 'some_special_image_template'
        );
        */
    }

    public function getThemeResponsiveImageMap()
    {
        return array(
            'large' => '900px',
            'medium' => '768px',
            'small' => '0',
        );
    }

    public function getThemeEditorClasses()
    {
        return array(
            array('title' => t('Title Thin'), 'menuClass' => 'title-thin', 'spanClass' => 'title-thin'),
            array('title' => t('Title Caps Bold'), 'menuClass' => 'title-caps-bold', 'spanClass' => 'title-caps-bold'),
            array('title' => t('Title Caps'), 'menuClass' => 'title-caps', 'spanClass' => 'title-caps'),
            array('title' => t('Image Caption'), 'menuClass' => 'image-caption', 'spanClass' => 'image-caption'),
            array('title' => t('Standard Button'), 'menuClass' => '', 'spanClass' => 'btn btn-default'),
            array('title' => t('Success Button'), 'menuClass' => '', 'spanClass' => 'btn btn-success'),
            array('title' => t('Primary Button'), 'menuClass' => '', 'spanClass' => 'btn btn-primary'),
        );
    }

    public function getThemeAreaLayoutPresets()
    {
        $presets = array(
            array(
                'handle' => 'left_sidebar',
                'name' => 'Left Sidebar',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<div class="col-sm-4"></div>',
                    '<div class="col-sm-8"></div>'
                ),
            ),
            array(
                'handle' => 'right_sidebar',
                'name' => 'Right Sidebar',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<div class="col-sm-8"></div>',
                    '<div class="col-sm-4"></div>'
                ),
            ),
            array(
                'handle' => 'three_column_centered',
                'name' => 'Three Column Centered',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<div class="col-sm-3"></div>',
                    '<div class="col-sm-4 col-sm-offset-1"></div>',
                    '<div class="col-sm-3 col-sm-offset-1"></div>',
                ),
            )
        );
        return $presets;
    }
}
