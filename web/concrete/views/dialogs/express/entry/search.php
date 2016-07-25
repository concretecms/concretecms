<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search="express_entries" class="ccm-ui">
    <?php View::element('express/entries/search', array('controller' => $searchController, 'selectMode' => true)) ?>
</div>

<script type="text/javascript">
    $(function () {
        $('div[data-search=express_entries]').concreteAjaxSearch({
            result: <?=$result?>
        });
    });
</script>
