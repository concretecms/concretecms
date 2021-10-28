<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Types $controller
 * @var Concrete\Core\Entity\Site\Skeleton $skeleton
 * @var Concrete\Core\Entity\Site\Type $type
 * @var Concrete\Core\Filesystem\Element $typeMenu
 */

$typeMenu->render();
?>

<div data-tree-site-skeleton="<?= $type->getSiteTypeID() ?>"></div>

<script>
$(document).ready(function() {
    $('div[data-tree-site-skeleton]').concreteSitemap({
        dataSource: <?= json_encode((string) $controller->action('get_sitemap', $type->getSiteTypeID())) ?>
    });
});
</script>
