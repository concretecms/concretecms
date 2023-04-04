<?php
namespace Concrete\Theme\OxfordShirt ;

use Concrete\Core\Feature\Features;
use Concrete\Core\Page\Theme\BedrockThemeTrait;
use Concrete\Core\Page\Theme\Color\Color;
use Concrete\Core\Page\Theme\Color\ColorCollection;
use Concrete\Core\Page\Theme\Documentation\OxfordShirtDocumentationProvider;
use Concrete\Core\Page\Theme\Documentation\DocumentationProvider;
use Concrete\Core\Page\Theme\Documentation\DocumentationProviderInterface;
use Concrete\Core\Page\Theme\Documentation\ThemeDocumentationPage;
use Concrete\Core\Page\Theme\Theme;

class PageTheme extends Theme
{
    
    use BedrockThemeTrait {
        getColorCollection as getBedrockColorCollection;
    }

    public function getThemeName()
    {
        return t('Oxford Shirt');
    }

    public function getThemeDescription()
    {
        return t('A Concrete CMS theme built for version 9.');
    }

    public function getThemeSupportedFeatures()
    {
        return [
            Features::ACCOUNT,
            Features::ACCORDIONS,
            Features::DESKTOP,
            Features::BASICS,
            Features::TYPOGRAPHY,
            Features::DOCUMENTS,
            Features::CONVERSATIONS,
            Features::FAQ,
            Features::PROFILE,
            Features::NAVIGATION,
            Features::IMAGERY,
            Features::FORMS,
            Features::SEARCH,
            Features::TESTIMONIALS,
            Features::TAXONOMY,
        ];
    }

    /**
     * @return array
     */
    public function getThemeResponsiveImageMap()
    {
        return [
            'xl' => '1200px',
            'lg' => '992px',
            'md' => '768px',
            'sm' => '576px',
            'xs' => '0',
        ];
    }

    /**
     * @return array
     */
    public function getThemeBlockClasses()
    {
        return [
            '*' => [
                'mt-0',
                'mt-1',
                'mt-2',
                'mt-3',
                'mt-4',
                'mt-5',
                'mb-0',
                'mb-1',
                'mb-2',
                'mb-3',
                'mb-4',
                'mb-5',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getThemeEditorClasses()
    {
        return [
            [
                'title' => t('Display 1'),
                'element' => array('h1','p','div'),
                'attributes' => array('class' => 'display-1')
            ],
            [
                'title' => t('Display 2'),
                'element' => array('h2','p','div'),
                'attributes' => array('class' => 'display-2')
            ],
            [
                'title' => t('Display 3'),
                'element' => array('h3','p','div'),
                'attributes' => array('class' => 'display-3')
            ],
            [
                'title' => t('Display 4'),
                'element' => array('h4','p','div'),
                'attributes' => array('class' => 'display-4')
            ],
            [
                'title' => t('Display 5'),
                'element' => array('h5','p','div'),
                'attributes' => array('class' => 'display-5')
            ],
            [
                'title' => t('Display 6'),
                'element' => array('h6','p','div'),
                'attributes' => array('class' => 'display-6')
            ],
            [
                'title' => t('Lead'),
                'element' => array('p'),
                'attributes' => array('class' => 'lead')
            ],
            [
                'title' => t('Muted'),
                'element' => array('p'),
                'attributes' => array('class' => 'muted')
            ],
            [
                'title' => t('Basic Table'),
                'element' => array('table'),
                'attributes' => array('class' => 'table')
            ],
            [
                'title' => t('Striped Table'),
                'element' => array('table'),
                'attributes' => array('class' => 'table table-striped')
            ],
            [
                'title' => t('Subtitle Big'),
                'element' => array('h1','h2','h3','h4','h5','h6','p','div'),
                'attributes' => array('class' => 'subtitle-big')
            ],[
                'title' => t('Subtitle Small'),
                'element' => array('h1','h2','h3','h4','h5','h6','p','div'),
                'attributes' => array('class' => 'subtitle-small')
            ],
        ];
    }


    public function getDocumentationProvider(): ?DocumentationProviderInterface
    {
        return new OxfordShirtDocumentationProvider($this);
    }

    public function getColorCollection(): ?ColorCollection
    {
        $collection = $this->getBedrockColorCollection();
        $collection->add(new Color('light-accent', t('Light Accent')));
        $collection->add(new Color('grey-accent', t('Grey Accent')));
        $collection->add(new Color('dim', t('Dim')));
        return $collection;
    }


}
