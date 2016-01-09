<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
if (is_object($currentTopic)) {
    $title = t('Topic Archives: %s', $currentTopic->getTreeNodeDisplayName());
}
?>
<h1 class="page-title"><?=h($title)?></h1>