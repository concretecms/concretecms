<?php

namespace Concrete\Core\StyleCustomizer\Preview;

use Concrete\Core\Http\Response;
use Concrete\Core\Page\Page;
use Concrete\Core\StyleCustomizer\Customizer\Customizer;
use Concrete\Core\StyleCustomizer\Preset\PresetInterface;

interface PreviewHandlerInterface
{

    /**
     * Method used by the customizer when choosing one or more custom values from the customizer on the left
     * panel, and allowing it to submit the preview on the righthand content side.
     */
    public function getCustomPreviewResponse(Customizer $customizer, Page $page, PresetInterface $preset, array $requestData): Response;

    /**
     * Method used by the customizer when customizing a theme and clicking on a preset but not modifying any of the
     * values.
     */
    public function getPresetPreviewResponse(Customizer $customizer, Page $page, PresetInterface $preset): Response;

}
