<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<ul class="list-unstyled">
<?
if (is_object($tree)) {
    $node = $tree->getRootTreeNodeObject();
    if (is_object($node)) {
        $node->populateDirectChildrenOnly();
        foreach($node->getChildNodes() as $topic) { ?>
            <li><a href="<?=$view->action('search_by_topic', $topic->getTreeNodeID())?>"><?=$topic->getTreeNodeDisplayName()?></a></li>
        <? } ?>
    <? }
} ?>
</ul>