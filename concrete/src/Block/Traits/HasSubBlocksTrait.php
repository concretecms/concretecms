<?php

namespace Concrete\Core\Block\Traits;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;

trait HasSubBlocksTrait
{
    /**
     * @var bool
     */
    protected $btCacheBlockOutput = false;

    /**
     * @var int
     */
    protected $btCacheBlockOutputLifetime = 0;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = false;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = false;

    /**
     * @var bool whether we already checked cache settings for sub blocks
     */
    protected $btCacheSettingsInitialized = false;

    /**
     * @var string[]
     */
    protected $requiredFeatures = [];

    /**
     * Whether the cache settings has been initialized.
     *
     * @return bool
     */
    protected function isCacheSettingsInitialized(): bool
    {
        return $this->btCacheSettingsInitialized;
    }

    /**
     * Check block cache settings and register assets for sub blocks.
     *
     * @param \Concrete\Core\Page\Page $page the current page
     * @param Block[] $blocks the sub blocks in this container block
     *
     * @return void
     */
    protected function initializeSubBlockCacheSettings(Page $page, array $blocks): void
    {
        if ($this->btCacheSettingsInitialized || $page->isEditMode()) {
            return;
        }

        $this->btCacheSettingsInitialized = true;

        // First, we assume that we can cache the block output with forever lifetime.
        $btCacheBlockOutput = true;
        $btCacheBlockOutputOnPost = true;
        $btCacheBlockOutputLifetime = 0;

        // Let's go through all the sub blocks in this container block and see if we can cache them.
        foreach ($blocks as $b) {
            // If we have a sub block that overrides area permissions, the output of the container block may change based on the user.
            if ($b->overrideAreaPermissions()) {
                $btCacheBlockOutput = false;
                $btCacheBlockOutputOnPost = false;
                $btCacheBlockOutputLifetime = 0;
                break;
            }

            // If this sub block doesn't allow caching, we can't cache the container block.
            if (!$b->cacheBlockOutput()) {
                $btCacheBlockOutput = false;
                $btCacheBlockOutputOnPost = false;
                $btCacheBlockOutputLifetime = 0;
                break;
            }

            // If this sub block doesn't allow cache on post, we should set the container block to not cache on post.
            $btCacheBlockOutputOnPost = $btCacheBlockOutputOnPost && $b->cacheBlockOutputOnPost();

            //If we have a sub block that has a shorter cache lifetime, use that.
            $expires = $b->getBlockOutputCacheLifetime();
            if ($expires && ($expires < $btCacheBlockOutputLifetime || $btCacheBlockOutputLifetime === 0)) {
                $btCacheBlockOutputLifetime = $expires;
            }
        }

        $this->btCacheBlockOutput = $btCacheBlockOutput;
        $this->btCacheBlockOutputOnPost = $btCacheBlockOutputOnPost;
        $this->btCacheBlockOutputLifetime = $btCacheBlockOutputLifetime;

        foreach ($blocks as $b) {
            // Check if the sub block has any assets to register.
            $objController = $b->getController();
            if (is_callable([$objController, 'registerViewAssets'])) {
                $objController->on_start();
                $objController->outputAutoHeaderItems();
                $objController->registerViewAssets();
                if ($objController instanceof UsesFeatureInterface) {
                    foreach ($objController->getRequiredFeatures() as $feature) {
                        if (!in_array($feature, $this->requiredFeatures, true)) {
                            $this->requiredFeatures[] = $feature;
                        }
                    }
                }
            }
        }
    }
}
