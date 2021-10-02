<?php
namespace Concrete\Controller\Panel\Detail\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\View\Preview\ThemeCustomizerRequest;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Compiler\Compiler;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Symfony\Component\HttpFoundation\Response;

class PreviewPreset extends BackendInterfaceController
{
    protected $viewPath = '/panels/details/theme/preview_preset';

    public function canAccess()
    {
        $page = Page::getByPath('/dashboard/pages/themes');
        $checker = new Checker($page);
        return $checker->canViewPage();
    }

    public function view($pThemeID, $presetIdentifier, $pageID)
    {
        $theme = Theme::getByID($pThemeID);
        $page = Page::getByID($pageID);
        $this->set('previewPage', $page);
        $this->set('pThemeID', $pThemeID);
        $this->set('presetIdentifier', $presetIdentifier);
        $this->set('token', $this->app->make('token'));
    }

    public function viewIframe($pThemeID, $presetIdentifier, $pageID)
    {
        if ($this->app->make('token')->validate()) {
            $page = Page::getByID($pageID);
            $checker = new Checker($page);
            if ($checker->canViewPage()) {

                $theme = Theme::getByID($pThemeID);
                $customizer = $theme->getThemeCustomizer();
                $preset = $customizer->getPresetByIdentifier($presetIdentifier);

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
                    $collection = $variableCollectionFactory->createFromPreset($customizer, $preset);
                    $result = $compiler->compileFromPreset($customizer, $preset, $collection);
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
