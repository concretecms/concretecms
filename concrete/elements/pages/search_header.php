<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-header-search-form ccm-ui" data-header="page-search">
    <?php if (isset($includeBreadcrumb) && $includeBreadcrumb) { ?>
        <div class="ccm-search-results-breadcrumb">
        </div>
    <?php } ?>

    <form method="get" action="<?php echo URL::to('/ccm/system/search/pages/basic')?>">
        <div class="input-group">

            <div class="ccm-header-search-form-input">
                <a class="ccm-header-reset-search" href="#" data-button-action-url="<?=URL::to('/ccm/system/search/pages/clear')?>" data-button-action="clear-search"><?=t('Reset Search')?></a>
                <a class="ccm-header-launch-advanced-search" href="<?php echo URL::to('/ccm/system/dialogs/page/advanced_search')?>" data-launch-dialog="advanced-search"><?=t('Advanced')?></a>
                <input type="text" class="form-control" autocomplete="off" name="cKeywords" placeholder="<?=t('Search')?>">
            </div>
              <span class="input-group-btn">'
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
              </span>
        </div>
    </form>
</div>