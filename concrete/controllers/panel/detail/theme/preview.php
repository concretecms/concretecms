<?php
namespace Concrete\Controller\Panel\Detail\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\View\Preview\SkinPreviewRequest;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Adapter\AdapterFactory;
use Concrete\Core\StyleCustomizer\Compiler\PreviewRequestCompiler;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Symfony\Component\HttpFoundation\Response;

class Preview extends BackendInterfaceController
{
    protected $viewPath = '/panels/details/theme/preview';

    public function canAccess()
    {
        $page = Page::getByPath('/dashboard/pages/themes');
        $checker = new Checker($page);
        return $checker->canViewPage();
    }

    public function view($pThemeID, $skinIdentifier, $pageID)
    {
        $page = Page::getByID($pageID);
        $this->set('previewPage', $page);
        $this->set('pThemeID', $pThemeID);
        $this->set('skinIdentifier', $skinIdentifier);
    }

    public function doPreview($pThemeID, $skinIdentifier, $pageID)
    {
        $page = Page::getByID($pageID);
        $checker = new Checker($page);
        if ($checker->canViewPage()) {

            $theme = Theme::getByID($pThemeID);
            $skin = $theme->getSkinByIdentifier($skinIdentifier);

            $req = Request::getInstance();
            $req->setCurrentPage($page);
            $controller = $page->getPageController();
            $view = $controller->getViewObject();
            $previewRequest = new SkinPreviewRequest();
            $previewRequest->setTheme($theme);
            $previewRequest->setSkin($skin);

            if ($this->request->request->has('styles')) {
                // This is a preview request with custom, changed style data. Let's parse
                // that data and compile it into a temporary CSS file.
                $styles = json_decode($this->request->request->get('styles'), true);
                $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                $adapterFactory = $this->app->make(AdapterFactory::class);
                $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
                $adapter = $adapterFactory->createFromTheme($theme);
                $styleValueList = $styleValueListFactory->createFromRequestArray($theme->getThemeCustomizableStyleList(), $styles);
                $collection = $variableCollectionFactory->createFromStyleValueList($styleValueList);
                $compiler = $this->app->make(PreviewRequestCompiler::class);
                $result = $compiler->compile($adapter, $skin, $collection);
                $previewRequest->setCustomCss($result);
            }

            $view->setCustomPreviewRequest($previewRequest);

            $req->setCustomRequestUser(-1);
            $response = new Response();
            $content = $view->render();
            $response->setContent($content);

            return $response;

        }
    }


}
