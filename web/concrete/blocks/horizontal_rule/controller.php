<?php
namespace Concrete\Block\HorizontalRule;
use \Concrete\Core\Block\BlockController;

class Controller extends BlockController {

    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btIgnorePageThemeGridFrameworkContainer = true;

    public function getBlockTypeDescription() {
        return t("Adds a thin hairline horizontal divider to the page.");
    }

    public function getBlockTypeName() {
        return t("Horizontal Rule");
    }

}