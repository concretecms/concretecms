<?php
namespace Concrete\Controller\Panel\Theme;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Parser\ParserFactory;

class CustomizeSkin extends BackendInterfacePageController
{
    protected $viewPath = '/panels/theme/customize';

    public function canAccess()
    {
        $page = Page::getByPath('/dashboard/pages/themes');
        $checker = new Checker($page);
        return $checker->canViewPage();
    }

    public function view($pThemeID, $skinIdentifier)
    {
        $theme = Theme::getByID($pThemeID);
        if ($theme) {
            $skin = $theme->getSkinByIdentifier($skinIdentifier);
            if ($skin) {
                $styleList = $theme->getThemeCustomizableStyleList();
                $parserFactory = $this->app->make(ParserFactory::class);
                $parser = $parserFactory->createParserFromSkin($skin);
                $valueList = $parser->createStyleValueListFromSkin($styleList, $skin);
                $groupedStyleValueList = $valueList->createGroupedStyleValueList($styleList);
                $this->set('styles', $groupedStyleValueList);
                $this->set('skins', $skin);
                return;
            }
        }
        throw new \RuntimeException(t('Invalid theme ID or skin identifier.'));
    }


}
