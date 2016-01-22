<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-topic-list-wrapper">

    <div class="ccm-block-topic-list-header">
        <h5><?=h($title)?></h5>
    </div>

    <?
    if ($mode == 'S' && is_object($tree)):
        $node = $tree->getRootTreeNodeObject();
        $node->populateChildren();
        if (is_object($node)) {
            if (!isset($selectedTopicID)) {
                $selectedTopicID = null;
            }
            $walk = function($node) use (&$walk, &$view, $selectedTopicID) {
                print '<ul class="ccm-block-topic-list-list">';
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

    endif;

    if ($mode == 'P'): ?>

        <? if (count($topics)) { ?>
            <ul class="ccm-block-topic-list-page-topics">
            <? foreach($topics as $topic) { ?>
                <li><a href="<?=$view->controller->getTopicLink($topic)?>"><?=$topic->getTreeNodeDisplayName()?></a></li>
            <? } ?>
            </ul>
        <? } else { ?>
            <?=t('No topics.')?>
        <? } ?>

    <? endif; ?>

</div>

