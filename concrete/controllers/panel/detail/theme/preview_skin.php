<?php
namespace Concrete\Controller\Panel\Detail\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\View\Preview\ThemeCustomizerRequest;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\StyleCustomizer\Compiler\Compiler;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Symfony\Component\HttpFoundation\Response;
use Concrete\Core\Error\UserMessageException;

class PreviewSkin extends BackendInterfaceController
{
    protected $viewPath = '/panels/details/theme/preview_skin';

    public function canAccess()
    {
        $pk = PermissionKey::getByHandle('customize_themes');
        return $pk->validate();
    }

    public function view($pThemeID, $skinIdentifier, $pageID)
    {
        $theme = Theme::getByID($pThemeID);
        $page = Page::getByID($pageID);
        $this->set('previewPage', $page);
        $this->set('pThemeID', $pThemeID);
        $this->set('skinIdentifier', $skinIdentifier);
        $this->set('skin' , $theme->getSkinByIdentifier($skinIdentifier));

        $activeSkin = SkinInterface::SKIN_DEFAULT;
        $site = $page->getSite();
        if ($site) {
            if ($site->getThemeSkinIdentifier()) {
                $activeSkin = $site->getThemeSkinIdentifier();
            }
        }
        $this->set('token', $this->app->make('token'));
        $this->set('activeSkin', $activeSkin);
    }

    public function viewIframe($pThemeID, $skinIdentifier, $pageID)
    {
        if ($this->app->make('token')->validate()) {
            $page = Page::getByID($pageID);
            $checker = new Checker($page);
            if ($checker->canViewPage()) {

                $theme = Theme::getByID($pThemeID);
                $customizer = $theme->getThemeCustomizer();
                $skin = $theme->getSkinByIdentifier($skinIdentifier);

                $req = Request::getInstance();
                $req->setCurrentPage($page);
                $controller = $page->getPageController();
                $view = $controller->getViewObject();
                $previewRequest = new ThemeCustomizerRequest();

                $compiler = $this->app->make(Compiler::class);
                $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);

                if ($this->request->request->has('styles')) {
                    // This is a preview request with custom, changed style data. Let's parse
                    // that data and compile it into a temporary CSS file.
                    $styles = json_decode($this->request->request->get('styles'), true);
                    $preset = $customizer->getPresetByIdentifier($skin->getPresetStartingPoint());
                    $styleValueList = $styleValueListFactory->createFromRequestArray($customizer->getThemeCustomizableStyleList($preset), $styles);
                    $collection = $variableCollectionFactory->createFromStyleValueList($styleValueList);
                    $result = $compiler->compileFromPreset($customizer, $preset, $collection);

                    if ($this->request->request->has('customCss')) {
                        // This is custom CSS from the "Custom CSS" button at the bottom of the customizer. This just gets
                        // appended to the SCSS generated from the theme.
                        $result .= $this->request->request->get('customCss');
                    }

                } else {
                    // Let's turn out selected preset into CSS on the fly.
                    $collection = $variableCollectionFactory->createFromCustomSkin($skin);
                    $result = $compiler->compileFromCustomSkin($customizer, $skin, $collection);
                }

                $previewRequest->setCustomCss($result);
                $view->setCustomPreviewRequest($previewRequest);

                $req->setCustomRequestUser(-1);
                $response = new Response();
                $content = $view->render();
                $response->setContent($content);

                return $response;

            }
        }
        throw new UserMessageException(t('Access Denied'));
    }


}
