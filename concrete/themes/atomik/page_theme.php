<?php
namespace Concrete\Theme\Atomik;

use Concrete\Core\Feature\Features;
use Concrete\Core\Page\Theme\BedrockThemeTrait;
use Concrete\Core\Page\Theme\Documentation\DocumentationProvider;
use Concrete\Core\Page\Theme\Documentation\DocumentationProviderInterface;
use Concrete\Core\Page\Theme\Documentation\ThemeDocumentationPage;
use Concrete\Core\Page\Theme\Theme;

class PageTheme extends Theme
{
    
    use BedrockThemeTrait;
    
    public function getThemeName()
    {
        return t('Atomik');
    }

    public function getThemeDescription()
    {
        return t('A Concrete CMS theme built for 2021.');
    }

    public function getThemeSupportedFeatures()
    {
        return [
            Features::BASICS,
            Features::CALENDAR,
            Features::CONVERSATIONS,
            Features::FAQ,
            Features::NAVIGATION,
            Features::IMAGERY,
            Features::FORMS,
            Features::SEARCH,
            Features::TESTIMONIALS,
            Features::TAXONOMY,
        ];
    }

    public function getDocumentationProvider(): ?DocumentationProviderInterface
    {
        $pages = [
        ];
        $pages = array_merge($pages, $this->getDocumentationPages());
        return DocumentationProvider::createFromArray($pages);
    }


}
