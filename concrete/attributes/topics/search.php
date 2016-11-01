<?php if (is_object($tree)) {
    ?>

    <script type="text/javascript">
        $(function() {
            $('.tree-view-template-<?=$attributeKey->getAttributeKeyID()?>').concreteTree({  // run first time around to get default tree if new.
                'treeID': <?php echo $tree->getTreeID();
    ?>,
                'chooseNodeInForm': true,
                <?php if ($selectedNode) {
    ?>
                 'selectNodesByKey': [<?php echo $selectedNode ?>],
                 <?php 
}
    ?>
                'onSelect' : function(nodes) {
                     if (nodes.length) {
                        $('input[data-topic-search-field=<?=$attributeKey->getAttributeKeyID()?>]').val(nodes[0]);
                     } else {
                         $('input[data-topic-search-field=<?=$attributeKey->getAttributeKeyID()?>]').val('');
                     }
                 }
            });
        });
    </script>
    <div class="tree-view-container">
        <div class="tree-view-template-<?=$attributeKey->getAttributeKeyID()?>">
        </div>
    </div>
    <input type="hidden" data-topic-search-field="<?=$attributeKey->getAttributeKeyID()?>"
           name="<?=$view->field('treeNodeID')?>" value="">

<?php 
} ?>