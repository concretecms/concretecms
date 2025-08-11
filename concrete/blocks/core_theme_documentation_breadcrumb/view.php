<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Page\Theme\Theme|null $theme
 * @var \Concrete\Core\Page\Page|null $currentPage
 * @var \Concrete\Core\Page\Page[] $parents
 */

use Concrete\Core\Support\Facade\Url;

if ($theme && $currentPage) { ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=Url::to('/dashboard/pages/themes/preview', $theme->getThemeID())?>" target="_top"><?=$theme->getThemeName()?></a></li>
            <?php foreach ($parents as $parent) { ?>
                <li class="breadcrumb-item text-secondary" aria-current="page"><?=$parent->getCollectionName()?></li>
            <?php } ?>
            <li class="breadcrumb-item active" aria-current="page"><?=$currentPage->getCollectionName()?></li>
        </ol>
    </nav>

<?php
} ?>