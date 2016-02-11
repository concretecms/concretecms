<?php  defined('C5_EXECUTE') or die("Access Denied.");
if (!isset($selectedTopicID)) {
    $selectedTopicID = null;
}
?>

<div class="ccm-block-topic-list-flat-filter">
<?
if (is_object($tree)) {
    $node = $tree->getRootTreeNodeObject();
    if (is_object($node)) {
        $node->populateDirectChildrenOnly(); ?>
        <ol class="breadcrumb">
            <li><a href="<?=$view->controller->getTopicLink()?>"
                <? if (!$selectedTopicID) { ?>class="ccm-block-topic-list-topic-selected active"<? } ?>><?=t('All')?></a></li>

        <? foreach($node->getChildNodes() as $child) { ?>
            <li><a href="<?=$view->controller->getTopicLink($child)?>"
                    <? if (isset($selectedTopicID) && $selectedTopicID == $child->getTreeNodeID()) { ?>
                        class="ccm-block-topic-list-topic-selected active"
                    <? } ?> ><?=$child->getTreeNodeDisplayName()?></a></li>
        <? } ?>
        </ol>
    <? } ?>
    </div>
<? } ?>