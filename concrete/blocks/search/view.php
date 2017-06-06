<?php defined('C5_EXECUTE') or die('Access Denied.');

if (isset($error)) {
    ?><?=$error?><br/><br/><?php

}

if (!isset($query) || !is_string($query)) {
    $query = '';
}

?><form action="<?=$view->url($resultTarget)?>" method="get" class="ccm-search-block-form"><?php
    if (isset($title) && ($title !== '')) {
        ?><h3><?=h($title)?></h3><?php

    }
    if ($query === '') {
        ?><input name="search_paths[]" type="hidden" value="<?=htmlentities($baseSearchPath, ENT_COMPAT, APP_CHARSET) ?>" /><?php

    } elseif (isset($_REQUEST['search_paths']) && is_array($_REQUEST['search_paths'])) {
        foreach ($_REQUEST['search_paths'] as $search_path) {
            ?><input name="search_paths[]" type="hidden" value="<?=htmlentities($search_path, ENT_COMPAT, APP_CHARSET) ?>" /><?php

        }
    }
    ?><input name="query" type="text" value="<?=htmlentities($query, ENT_COMPAT, APP_CHARSET)?>" class="ccm-search-block-text" /><?php
    if (isset($buttonText) && ($buttonText !== '')) {
        ?> <input name="submit" type="submit" value="<?=h($buttonText)?>" class="btn btn-default ccm-search-block-submit" /><?php

    }

    if (isset($do_search) && $do_search) {
        if (count($results) == 0) {
            ?><h4 style="margin-top:32px"><?=t('There were no results found. Please try another keyword or phrase.')?></h4><?php

        } else {
            $tt = Core::make('helper/text');
            ?><div id="searchResults"><?php
                foreach ($results as $r) {
                    $currentPageBody = $this->controller->highlightedExtendedMarkup($r->getPageIndexContent(), $query);
                    ?><div class="searchResult">
                        <h3><a href="<?=$r->getCollectionLink()?>"><?=$r->getCollectionName()?></a></h3>
                        <p><?php
                            if ($r->getCollectionDescription()) {
                                echo $this->controller->highlightedMarkup($tt->shortText($r->getCollectionDescription()), $query);
                                ?><br/><?php

                            }
                            echo $currentPageBody;
                            ?> <br/><a href="<?=$r->getCollectionLink()?>" class="pageLink"><?=$this->controller->highlightedMarkup($r->getCollectionLink(), $query)?></a>
                        </p>
                    </div><?php

                }
            ?></div><?php
            $pages = $pagination->getCurrentPageResults();
            if ($pagination->haveToPaginate()) {
                $showPagination = true;
                echo $pagination->renderDefaultView();
            }
        }
    }
?></form><?php
