<?php

namespace Concrete\Core\StyleCustomizer\Preview;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\View\Preview\ThemeCustomizerRequest;
use Concrete\Core\StyleCustomizer\Compiler\Compiler;
use Concrete\Core\StyleCustomizer\Customizer\Customizer;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;

class StandardPreviewHandler implements PreviewHandlerInterface
{

    /**
     * @var Compiler
     */
    protected $compiler;

    /**
     * @var StyleValueListFactory
     */
    protected $styleValueListFactory;

    /**
     * @var NormalizedVariableCollectionFactory
     */
    protected $variableCollectionFactory;

    public function __construct(
        Compiler $compiler,
        StyleValueListFactory $styleValueListFactory,
        NormalizedVariableCollectionFactory $variableCollectionFactory
    ) {
        $this->compiler = $compiler;
        $this->styleValueListFactory = $styleValueListFactory;
        $this->variableCollectionFactory = $variableCollectionFactory;
    }

    protected function deliverResponse(Page $page, string $css): Response
    {
        $request = Request::getInstance();
        $request->setCustomRequestUser(-1);
        $request->setCurrentPage($page);

        $previewRequest = new ThemeCustomizerRequest();
        $previewRequest->setCustomCss($css);
        $controller = $page->getPageController();
        $view = $controller->getViewObject();
        $view->setCustomPreviewRequest($previewRequest);
        $content = $view->render();

        $response = new Response();
        $response->setContent($content);
        return $response;
    }

    public function getPresetPreviewResponse(Customizer $customizer, Page $page, PresetInterface $preset): Response
    {
        $collection = $this->variableCollectionFactory->createFromPreset($customizer, $preset);
        $result = $this->compiler->compileFromPreset($customizer, $preset, $collection);
        return $this->deliverResponse($page, $result);
    }

    public function getCustomPreviewResponse(Customizer $customizer, Page $page, PresetInterface $preset, array $requestData): Response
    {

        $styles = json_decode($requestData['styles'], true);
        $styleValueList = $this->styleValueListFactory->createFromRequestArray($customizer->getThemeCustomizableStyleList($preset), $styles);
        $collection = $this->variableCollectionFactory->createFromStyleValueList($styleValueList);
        $result = $this->compiler->compileFromPreset($customizer, $preset, $collection);

        if (isset($requestData['customCss'])) {
            // This is custom CSS from the "Custom CSS" button at the bottom of the customizer. This just gets
            // appended to the SCSS generated from the theme.
            $result .= $requestData['customCss'];
        }

        return $this->deliverResponse($page, $result);
    }

}
