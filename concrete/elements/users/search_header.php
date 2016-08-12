<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-header-search-form ccm-ui" data-header="user-search">
    <form method="get" action="<?php echo URL::to('/ccm/system/search/users/basic')?>">
        <a class="ccm-header-reset-search" href="#" data-button-action-url="<?=URL::to('/ccm/system/search/users/clear')?>" data-button-action="clear-search"><?=t('Reset Search')?></a>
        <a class="ccm-header-launch-advanced-search" href="<?php echo URL::to('/ccm/system/dialogs/user/advanced_search')?>" data-launch-dialog="advanced-search"><?=t('Advanced')?></a>
        <div class="input-group">

            <input type="text" class="form-control" autocomplete="off" name="uKeywords" placeholder="<?=t('Search')?>">
              <span class="input-group-btn">'
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
                  <?php if ($showAddButton) { ?>
                      <a href="<?php echo View::url('/dashboard/users/add') ?>"
                         class="btn btn-primary"><?php echo t("Add User") ?></a>

                  <?php } ?>
              </span>
        </div>
    </form>
</div>