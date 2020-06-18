<?php
$akID = $attributeKey->getAttributeKeyID();
if (is_object($tree)) {
    if (!is_array($selectedNode)) {
        $selectedNode = [$selectedNode];
    }
    $selectNodesByKey = json_encode($selectedNode);
    ?>

    <script type="text/javascript">
        $(function() {
            $('.tree-view-template-<?=$akID?>').concreteTree({  // run first time around to get default tree if new.
                'treeID': <?php echo $tree->getTreeID();
    ?>,
                'selectMode': 2,
                'chooseNodeInForm': 'multiple',
                <?php if ($selectedNode) {
    ?>
                 'selectNodesByKey': <?=$selectNodesByKey?>,
                 <?php 
}
    ?>
                'onSelect' : function(nodes) {
                    var element = $('div[data-search=<?php echo $akID ?>] .hidden-value-container');
                    element.html('');
                    $.each(nodes, function(i, node) {
                        element.append('<input data-node-id="' + node + '" name="<?=$view->field('treeNodeID')?>[]" type="hidden" value="'+node+'">');
                    });
                }
            });
        });
    </script>
    <div data-search="<?=$akID?>">
        <div class="tree-view-container">
            <div class="tree-view-template-<?=$attributeKey->getAttributeKeyID()?>">
            </div>
        </div>
        <div class="hidden-value-container"></div>
    </div>

<?php 
} ?>