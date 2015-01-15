<?php
$fm  = \Core::make('helper/concrete/ui/file_manager_menu');

if (!$menu) $menu = $fm->getFileContextMenu();
if (!$scriptName) $scriptName = "search-results-default-file-menu";

?>
<script type="text/template" data-template="<?=$scriptName?>">
    <div class="ccm-ui">
        <div class="ccm-popover-file-menu popover fade" data-search-file-menu="<%=item.fID%>" data-search-menu="<%=item.fID%>">
            <div class="arrow"></div>
            <div class="popover-inner">
                <ul class="dropdown-menu">
                <% if (typeof(displayClear) != 'undefined' && displayClear) { %>
					<li><a href="#" data-file-manager-action="clear"><span><?=t('Clear')?></span></a></li>
                    <li class="divider"></li>
                <% } %>
                <?php 
                foreach ($menu as $item) { 

                    $cnt = $item->getController();
                    if ( !$cnt->displayItem() ) continue;
                    $cnt->registerViewAssets();
                    $perms = $item->getRestrictions();

                    if (count($perms)) echo "<% if ( item." . join( $perms, " || item." ) .") { %>\n";
                    if ($item->isSeparator()) { ?>
                        <li role="presentation" class="divider"></li> 
                    <?php } else { ?>
                        <li role="presentation" <?=$item->isDangerous()?'class="text-danger"':''; ?>><?=$cnt->getMenuItemLinkElement()?></li>
                    <?php 
                    } 

                    if (count($perms)) echo "<% } %>\n";
                }
                ?>
                </ul>
            </div>
        </div>
</script>
