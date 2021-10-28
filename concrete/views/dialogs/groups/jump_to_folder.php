<?php

defined('C5_EXECUTE') or die("Access Denied.");

/** @var int $rootTreeNodeID */
?>

<div class="ccm-ui">
    <div data-select="group-manager-navigation">
        &nbsp;
    </div>
</div>

<script type="text/javascript">
    (function ($) {
        $(function () {
            $('[data-select=group-manager-navigation]').concreteTree({
                ajaxData: {
                    displayOnly: 'group_folder'
                },
                treeNodeParentID: <?php echo $rootTreeNodeID?>,
                onClick: function (node) {
                    jQuery.fn.dialog.closeTop();
                    ConcreteEvent.publish('GroupManagerJumpToFolder', {'folderID': node.key});
                },
            });
        });
    })(jQuery);
</script>
