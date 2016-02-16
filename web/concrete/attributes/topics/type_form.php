<?php if (is_object($tree)) {
    ?>

    <script type="text/javascript">
        $(function() {
            $('.tree-view-template').ccmtopicstree({  // run first time around to get default tree if new.
                'treeID': <?php echo $tree->getTreeID();
    ?>,
                'chooseNodeInForm': true,
                'noDrag' : true,
                //'selectMode': 2,
                <?php if ($parentNode) {
    ?>
                 'selectNodesByKey': [<?php echo $parentNode ?>],
                 <?php 
}
    ?>
                'onSelect' : function(select, node) {
                     if (select) {
                        $('input[name=akTopicParentNodeID]').val(node.data.key);
                     } else {
                        $('input[name=akTopicParentNodeID]').val('');
                     }
                 }
            });

            var treeViewTemplate = $('.tree-view-template');
            $('select[name=topicTreeIDSelect]').on('change', function() {
                $('input[name="akTopicTreeID"]').val($(this).find(':selected').val());
                $('.tree-view-template').remove();
                $('.tree-view-container').append(treeViewTemplate);
                var toolsURL = '<?php echo Loader::helper('concrete/urls')->getToolsURL('tree/load');
    ?>';
                var chosenTree = $(this).val();
                $('.tree-view-template').ccmtopicstree({
                    'treeID': chosenTree,
                    'chooseNodeInForm': true,
                    'onSelect' : function(select, node) {
                         if (select) {
                            $('input[name=akTopicParentNodeID]').val(node.data.key);
                         } else {
                            $('input[name=akTopicParentNodeID]').val('');
                         }
                     }
                });
            });
        });
        </script>
    <fieldset>
    <legend><?=t('Topic Tree')?></legend>
    <div class="clearfix"></div>
        <div class="form-group">
        <select class="form-control" name="topicTreeIDSelect">
            <?php foreach ($trees as $stree) {
    ?>
                <option value="<?=$stree->getTreeID()?>" <?php if ($tree->getTreeID() == $stree->getTreeID()) {
    ?>selected<?php 
}
    ?>><?=$stree->getTreeDisplayName()?></option>
            <?php 
}
    ?>
        </select>
        </div>
    <div class="tree-view-container">
        <div class="tree-view-template">
            <legend><?=t('Topic Default Parent Node')?></legend>
        </div>
    </div>
    <input type="hidden" name="akTopicParentNodeID" value="<?php echo $parentNode ?>">
    <input type="hidden" name="akTopicTreeID" value="<?php echo $tree->getTreeID();
    ?>">
    </fieldset>

<?php 
} else {
    ?>

    <div class="alert alert-danger"><?=t('You have not created a topic tree.
You must create a topic tree from the <a href="%s">Topics Page</a>
before you can use this attribute type.', URL::to('/dashboard/system/attributes/topics'))?></div>

<?php 
} ?>