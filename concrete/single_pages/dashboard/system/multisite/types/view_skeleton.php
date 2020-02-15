<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php

$type_menu->render();

?>

<div data-tree-site-skeleton="<?=$type->getSiteTypeID()?>">

</div>

<script type="text/javascript">
    $(function() {
        $('div[data-tree-site-skeleton]').concreteSitemap({
            dataSource: '<?=$view->action('get_sitemap', $type->getSiteTypeID())?>'
        });
    });
</script>