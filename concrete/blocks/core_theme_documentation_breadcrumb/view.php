<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Page\Theme\Theme $theme
 * @var \Concrete\Core\Page\Page $currentPage
 */

if ($theme && $currentPage) { ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=\URL::to('/dashboard/pages/themes/preview', $theme->getThemeID())?>" target="_top"><?=$theme->getThemeName()?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$currentPage->getCollectionName()?></a></li>
        </ol>
    </nav>

<?php
} ?>