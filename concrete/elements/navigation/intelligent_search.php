<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\Navigation;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Support\Facade\Application;

/** @var Navigation $navigation */

$app = Application::getFacadeApplication();
/** @var Repository $config */
$config = $app->make(Repository::class);

?>
<svg>
    <use xlink:href="#icon-search"/>
</svg>

<div>
    <!--suppress HtmlFormInputWithoutLabel -->
    <input type="search" autocomplete="off" id="ccm-nav-intelligent-search" tabindex="1"/>

    <div id="ccm-intelligent-search-results">
        <?php foreach ($navigation->getItems() as $searchResult) { ?>
            <?php /** @var PageItem $searchResult */ ?>

            <div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">
                <h1>
                    <?php echo $searchResult->getName(); ?>
                </h1>

                <ul class="ccm-intelligent-search-results-list">
                    <li>
                        <a href="<?php echo $searchResult->getUrl(); ?>"><?php echo t("View All"); ?></a>
                        <span><?php echo $searchResult->getName() . " " . $searchResult->getKeywords(); ?></span>
                    </li>

                    <?php foreach ($searchResult->getChildren() as $childPage) { ?>
                        <li>
                            <a href="<?php echo $childPage->getUrl(); ?>"><?php echo $childPage->getName(); ?></a>
                            <span><?php echo $childPage->getName() . " " . $childPage->getKeywords(); ?></span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <div class="ccm-intelligent-search-results-module">
            <h1>
                <?php echo t('Your Site') ?>
            </h1>

            <div class="loader">
                <div class="dot dot1"></div>
                <div class="dot dot2"></div>
                <div class="dot dot3"></div>
                <div class="dot dot4"></div>
            </div>

            <ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-your-site">

            </ul>
        </div>

        <?php if ($config->get('concrete.external.intelligent_search_help')) { ?>
            <div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite">
                <h1>
                    <?php echo t('Help') ?>
                </h1>

                <div class="loader">
                    <div class="dot dot1"></div>
                    <div class="dot dot2"></div>
                    <div class="dot dot3"></div>
                    <div class="dot dot4"></div>
                </div>

                <ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-help">

                </ul>
            </div>
        <?php } ?>

        <?php if ($config->get('concrete.external.intelligent_search_help')) { ?>
            <div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite">
                <h1>
                    <?php echo t('Add-Ons') ?>
                </h1>

                <div class="loader">
                    <div class="dot dot1"></div>
                    <div class="dot dot2"></div>
                    <div class="dot dot3"></div>
                    <div class="dot dot4"></div>
                </div>

                <ul class="ccm-intelligent-search-results-list"
                    id="ccm-intelligent-search-results-list-marketplace">

                </ul>
            </div>
        <?php } ?>
    </div>
</div>