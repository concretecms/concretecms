<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$node = $tree->getRootTreeNodeObject();
$node->populateChildren();
if (is_object($node)) {
    $walk = function($node) use (&$walk, &$view, $selectedTopicID) {
        print '<ul class="ccm-block-topic-list">';
        foreach($node->getChildNodes() as $topic) {
            if ($topic instanceof \Concrete\Core\Tree\Node\Type\TopicCategory) { ?>
                <li><?=$topic->getTreeNodeDisplayName()?></li>
            <? } else { ?>
                <li><a href="<?=$view->controller->getTopicLink($topic)?>"
                        <? if (isset($selectedTopicID) && $selectedTopicID == $topic->getTreeNodeID()) { ?>
                            class="ccm-block-topic-list-topic-selected"
                        <? } ?> ><?=$topic->getTreeNodeDisplayName()?></a></li>
            <? } ?>
            <? $walk($topic); ?>
        <? }
        print '</ul>';
    };
    $walk($node);
}