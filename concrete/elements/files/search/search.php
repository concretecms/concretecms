<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-header-search-form ccm-ui" data-header="file-manager">

    <form method="get" class="form-inline" action="<?=$view->action('view')?>">

        <a href="#" data-launch-dialog="advanced-search" class="ccm-header-launch-advanced-search"
           <?php if (isset($query)) { ?>
               data-advanced-search-query='<?=$query?>'
           <?php } ?>
           data-advanced-search-dialog-url="<?php echo URL::to('/ccm/system/dialogs/file/advanced_search') ?>">
            <?= t('Advanced') ?>
        </a>

        <div class="ccm-header-search-form-input input-group">
            <?=$form->search('keywords', [
                    'placeholder' => t('Search'),
                    'class' => 'border-right-0',
                    'autocomplete' => 'off']);
            ?>
            <div class="input-group-append">
                    <button type="submit" class="input-group-icon">
                        <svg width="16" height="16"><use xlink:href="#icon-search"/></svg>
                    </button>
            </div>
        </div>

    </form>

</div>

<script type="text/javascript">
    $(function() {
        ConcreteEvent.subscribe('SavedSearchCreated', function(e, data) {
            window.location.reload();
        });
        ConcreteEvent.subscribe('SavedSearchCreated', function(e, data) {
            window.location.reload();
        });
    });
</script>
