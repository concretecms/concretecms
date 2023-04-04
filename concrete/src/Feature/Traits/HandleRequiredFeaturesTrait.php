<?php

namespace Concrete\Core\Feature\Traits;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Page\Theme\Theme;

trait HandleRequiredFeaturesTrait
{

    /**
     * Given a block or page controller that we're rendering, we request all the features required by that block controller
     * and then add them to the page's header/footer via requireAsset, if they're not already provided by the theme
     * (which we check via getThemeSupportedFeatures)
     *
     * @param mixed $controller
     */
    protected function handleRequiredFeatures($controller, Theme $theme): void
    {
        if ($controller instanceof UsesFeatureInterface) {
            $this->requireFeaturesIfNotPresentInTheme($controller->getRequiredFeatures(), $theme);
        }
    }

    /**
     * Requires one or more features if not present in theme.
     *
     * @param array $features
     * @param Theme $theme
     */
    protected function requireFeaturesIfNotPresentInTheme(array $features, Theme $theme): void
    {
        $assetList = AssetList::getInstance();
        $assetResponse = ResponseAssetGroup::get();
        $logger = app(LoggerFactory::class)->createLogger(Channels::CHANNEL_CONTENT);
        foreach ($features as $feature) {
            if (!in_array($feature, $theme->getThemeSupportedFeatures())) {
                $assetHandle = "feature/{$feature}/frontend";
                if ($assetList->getAssetGroup($assetHandle)) {
                    $assetResponse->requireAsset($assetHandle);
                } else {
                    $logger->info(
                        t(
                            "Block type requested required feature '%s' but it was not registered.",
                            $assetHandle
                        )
                    );
                }
            }
        }

    }

}