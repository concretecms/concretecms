<?php

namespace Concrete\Core\StyleCustomizer\Preview;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\CustomStyle;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Customizer\Customizer;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\StyleCustomizer\Traits\ExtractPresetFontsFileStyleFromLegacyPresetTrait;

/**
 * Used by the legacy customizer, this preview handler loops through all customizable style sheets in the theme's root
 * and saves them into the cache, which is then used by the getStylesheet method in the page.
 * @deprecated
 */
class LegacyStylesheetPreviewHandler implements PreviewHandlerInterface
{

    use ExtractPresetFontsFileStyleFromLegacyPresetTrait;

    /**
     * @var StyleValueListFactory
     */
    protected $styleValueListFactory;

    /**
     * @var NormalizedVariableCollectionFactory
     */
    protected $variableCollectionFactory;

    public function __construct(
        StyleValueListFactory $styleValueListFactory,
        NormalizedVariableCollectionFactory $variableCollectionFactory
    ) {
        $this->styleValueListFactory = $styleValueListFactory;
        $this->variableCollectionFactory = $variableCollectionFactory;
    }

    public function getCustomPreviewResponse(
        Customizer $customizer,
        Page $page,
        PresetInterface $preset,
        array $requestData
    ): Response {

        $styles = json_decode($requestData['styles'], true);
        $styleValueList = $this->styleValueListFactory->createFromRequestArray($customizer->getThemeCustomizableStyleList($preset), $styles);
        $this->addPresetFontsFileStyleToStyleValueList($customizer->getType(), $preset, $styleValueList);
        $collection = $this->variableCollectionFactory->createFromStyleValueList($styleValueList);
        return $this->deliverResponse($customizer, $page, $collection);
    }

    protected function deliverResponse(Customizer $customizer, Page $page, NormalizedVariableCollection $collection): Response
    {
        $theme = $customizer->getTheme();
        $theme->enablePreviewRequest();
        $sheets = $theme->getThemeCustomizableStylesheets();
        foreach ($sheets as $sheet) {
            $sheet->setVariableCollection($collection);
            // we save each sheet to the preview location.
            $sheet->output();
        }

        $request = Request::getInstance();
        $request->setCustomRequestUser(-1);
        $request->setCurrentPage($page);

        $controller = $page->getPageController();
        $view = $controller->getViewObject();
        $view->setCustomPageTheme($theme);
        $content = $view->render();

        $response = new Response();
        $response->setContent($content);
        return $response;
    }


    public function getPresetPreviewResponse(Customizer $customizer, Page $page, PresetInterface $preset): Response
    {
        $collection = $this->variableCollectionFactory->createFromPreset($customizer, $preset);
        return $this->deliverResponse($customizer, $page, $collection);
    }

    /**
     * Used when a page level or custom theme customizer set has been saved and now needs to be re-previewed.
     */
    public function getCustomStylePreviewResponse(Customizer $customizer, Page $page, CustomStyle $customStyle): Response
    {
        $collection = $this->variableCollectionFactory->createFromStyleValueList($customStyle->getValueList());
        return $this->deliverResponse($customizer, $page, $collection);
    }

}
