<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-topic-list-wrapper">
	
    <div class="ccm-block-topic-list-header">
        <h5><?php echo h($title); ?></h5>
	</div>
	
    <?php
		if ($mode == 'S' && is_object($tree)) {
			$node = $tree->getRootTreeNodeObject();
			$node->populateChildren();
			if (is_object($node)) {
				if (!isset($selectedTopicID)) {
					$selectedTopicID = null;
				}?>		
				<ul class="ccm-block-topic-list-list">
					<?php
						$walk = function ($node) use (&$walk, &$view, $selectedTopicID) {
							foreach ($node->getChildNodes() as $topic) {
								//Case1: Print Node without child elements!
								if (count($topic->getChildNodes())== 0) {?> 
								<li><a href="<?php echo $view->controller->getTopicLink($topic); ?>" 	
									<?php
										if (isset($selectedTopicID) && $selectedTopicID == $topic->getTreeNodeID()) {
											?> class="ccm-block-topic-list-topic-selected"<?php
										}
										?>><?php echo $topic->getTreeNodeDisplayName(); ?>
									</a>
								</li>
								<?php } 
								//Case2: Node with child elements (nested ul list inside li item!)
								if (count($topic->getChildNodes())) {
								?>
								<li class="ccm-block-topic-list-topic-parent">
									
									<a href="<?php echo $view->controller->getTopicLink($topic); ?>" 	
									<?php
										if (isset($selectedTopicID) && $selectedTopicID == $topic->getTreeNodeID()) {
											?> class="ccm-block-topic-list-topic-selected"<?php
										}
										?>><?php echo $topic->getTreeNodeDisplayName(); ?>
									</a>
									<!-- Open Nested list-->
									<ul class="ccm-block-topic-nested-list-list">
										<?php $walk($topic); ?>
									</ul>
								</li>
								<?php } 
							}
							
						};
						$walk($node);
						//Close outer ul tag
						echo "</ul>";
					}
				}
				
    if ($mode == 'P') {
        if (count($topics)) {
            ?><ul class="ccm-block-topic-list-page-topics"><?php
            foreach ($topics as $topic) {
                ?><li><a href="<?php echo $view->controller->getTopicLink($topic); ?>"><?php echo $topic->getTreeNodeDisplayName(); ?></a></li><?php
            }
            ?></ul><?php
        } else {
            echo t('No topics.');
        }
    }
    ?>

</div>
