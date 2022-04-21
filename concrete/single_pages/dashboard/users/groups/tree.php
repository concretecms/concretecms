<?php

defined('C5_EXECUTE') or die('Access Denied.');

?>

<div class="group-tree"></div>

<script type="text/javascript">
$(function() {
    $('.group-tree').concreteTree({
        'treeID': '<?php echo $tree->getTreeID()?>'
    });
})
</script>