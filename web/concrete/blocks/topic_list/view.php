<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-topic-list-wrapper">

    <div class="ccm-block-topic-list-header">
        <h5><?php echo h($title); ?></h5>
    </div>

    <?php
    if ($mode == 'S' && is_object($tree)):
        $node = $tree->getRootTreeNodeObject();
        $node->populateChildren();
        if (is_object($node)) {
            if (!isset($selectedTopicID)) {
                $selectedTopicID = null;
            }
            $walk = function ($node) use (&$walk, &$view, $selectedTopicID) {
                echo '<ul class="ccm-block-topic-list-list">';
                foreach ($node->getChildNodes() as $topic) {
                    if ($topic instanceof \Concrete\Core\Tree\Node\Type\TopicCategory) {
                        ?>
                        <li><?=$topic->getTreeNodeDisplayName()?></li>
                    <?php
                    } else {
                        ?>
                        <li><a href="<?=$view->controller->getTopicLink($topic)?>"
                                <?php if (isset($selectedTopicID) && $selectedTopicID == $topic->getTreeNodeID()) {
    ?>
                                    class="ccm-block-topic-list-topic-selected"
                                <?php
}
                        ?> ><?=$topic->getTreeNodeDisplayName()?></a></li>
                    <?php
                    }
                    ?>
                    <?php $walk($topic);
                    ?>
                <?php
                }
                echo '</ul>';
            };
            $walk($node);
        }
    }

    endif;

    if ($mode == 'P'): ?>

        <?php if (count($topics)) {
    ?>
            <ul class="ccm-block-topic-list-page-topics">
            <?php foreach ($topics as $topic) {
    ?>
                <li><a href="<?=$view->controller->getTopicLink($topic)?>"><?=$topic->getTreeNodeDisplayName()?></a></li>
            <?php
}
    ?>
            </ul>
        <?php
} else {
    ?>
            <?=t('No topics.')?>
        <?php
} ?>

    <?php endif; ?>

</div>
