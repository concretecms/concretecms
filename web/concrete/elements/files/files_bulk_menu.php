<?php
$fm  = \Core::make('helper/concrete/ui/file_manager_menu');
if ( !$menu ) $menu = $fm->getBulkMenu();

?>

        <div class="dropdown ccm-search-bulk-action form-group">
            <input type="hidden" name="ccm-search-uploaded-fIDs" id="ccm-search-uploaded-fIDs" value=""/>
            <button class="btn btn-default dropdown-toggle" type="button" id="ccm-bulk-action-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="ccm-dropdown-label"><?=t("Bulk Action Menu")?></span><span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu" area-labelledby="ccm-bulk-action-toggle" >
                <li role="presentation" class="ccm-action-target-control ">
                    <div class="btn-group" role="group">
                        <button type="button" class="ccm-target-selected btn btn-default active"><?=t('Selected')?></button>
                        <button type="button" class="ccm-target-uploaded btn btn-default"><?=t('Uploaded')?></button>
                    </div>
                </li>
                <?php 
                foreach ( $menu as $item ) { 

                    $cnt = $item->getController();
                    if ( !$cnt->displayItem() ) continue;
                    $cnt->registerViewAssets();

                    if ( $item->isSeparator() ) { ?>
                        <li role="presentation" class="divider"></li> 
                    <?php } else { ?>
                        <li role="presentation" <?=$item->isDangerous()?'class="text-danger"':''; ?>><?=$cnt->getMenuItemLinkElement()?></li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
