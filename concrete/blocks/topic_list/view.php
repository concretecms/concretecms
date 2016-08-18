<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php	
	
	// Set your framework markup(or create you own)
	$classes = array( 
	"concrete5" => array (
	"div_list_wrapper" => 'ccm-block-topic-list-wrapper',
	"js_plugin_attribute" => 'some_js_attribute',
	"ul_level_1" => 'ccm-block-topic-list-list',
	"li_active_topic" => '',
	"a_active_topic" => 'ccm-block-topic-list-topic-selected',
	"li_parent" => 'ccm-block-topic-list-topic-parent',
	"ul_nested" => 'ccm-block-topic-list-wrapper',
	),
	"uikit_accordion" => array (
	"div_list_wrapper" => 'uk-panel uk-panel-box',
	"js_plugin_attribute" => 'data-uk-dnav',
	"ul_level_1" => 'uk-nav uk-nav-side uk-nav-parent-icon',	
	"li_active_topic" => 'uk-active',
	"a_active_topic" => '',
	"li_parent" => 'uk-parent',
	"ul_nested" => 'uk-nav-sub ',
	),
	"bootstrap_vertical_pills" => array (
	"div_list_wrapper" => '',
	"js_plugin_attribute" => '',
	"ul_level_1" => 'nav nav-pills ',	
	"li_active_topic" => 'active',
	"a_active_topic" => '',
	"li_parent" => '',
	"ul_nested" => 'nav nav-pills',
	),
	"foundation_vertical_dropdown_menu" => array (
	"div_list_wrapper" => '',
	"js_plugin_attribute" => 'data-dropdown-menu',
	"ul_level_1" => 'vertical dropdown menu',	
	"li_active_topic" => '',
	"a_active_topic" => 'active',
	"li_parent" => 'is-dropdown-submenu-parent',
	"ul_nested" => 'menu',
	),
	"custom_template" => array (
	"div_list_wrapper" => '',
	"js_plugin_attribute" => '',
	"ul_level_1" => '',	
	"li_active_topic" => '',
	"a_active_topic" => '',
	"li_parent" => '',
	"ul_nested" => '',
	),
	);//end outer array
	
	//Select your markup array
	$framework = "concrete5";
	
	/* 
		// Style guide (mode [S]Search – Display a list of all topics for use on a search sidebar)
		========================================================================	
		1/7. List <div> wrapper:
		'div_list_wrapper'
		
		2/7. Add js attribute ("Activate" the JS plugin):
		'js_plugin_attribute'
		
		3/7. class for the outer topics <ul> unordered list (level 1):
		'ul_level_1'
		
		4/7. class for the active <li> item:
		'a_active_topic'
		
		5/7. class for the active <a> item:
		'li_active_topic'
		
		6/7. class for the parent <li> list item: 
		'li_parent_category'
		
		7/7. class for the nested <ul> (level > 1,lists inside "parent_list_item"):	
		'ul_nested' 
		
		// Generic Markup example (no href her)
		========================================================================
		<div class="div_list_wrapper">
		<ul class="ul_level_1" 	js_plugin_attribute>
		<li class="li_active_topic"><a class="a_active_topic">Topic A</a></li>
		<li><a>Topic B</a></li>
		<li><a class="li_parent_category">Category D</a>
		<ul class="ul_nested">
		<li><a>Topic D.1</a></li>
		<li><a>Topic D.2</a></li>			
		</ul>
		</li>
		</ul>
		</div>
	*/
?>
<div class="<?php echo $classes[$framework]['div_list_wrapper']; ?>">	
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
				}		
				?><ul class="<?php echo $classes[$framework]['ul_level_1']; ?>" <?php echo $classes[$framework]['js_plugin_attribute']; ?>><?php
					$walk = function ($node) use (&$walk, &$view, $selectedTopicID,$classes,$framework) {
						foreach ($node->getChildNodes() as $topic) {
							//Case1: Node without any child elements at level 0 or greater;
							if (count($topic->getChildNodes())== 0) {?> 
							<li <?php if(isset($selectedTopicID) && $selectedTopicID == $topic->getTreeNodeID()){?> class="<?php echo $classes[$framework]['li_active_topic']; ?>"<?php }?>>
								<a href="<?php echo $view->controller->getTopicLink($topic); ?>"<?php	
									if (isset($selectedTopicID) && $selectedTopicID == $topic->getTreeNodeID()){?> 
								class="<?php echo $classes[$framework]['a_active_topic'] ?>"<?php }?>><?php echo $topic->getTreeNodeDisplayName(); ?></a>
							</li><?php }
							//Case2: Parent node with child elements(Nested ul inside li item)
							if (count($topic->getChildNodes())) {?>
							<li class="<?php echo $classes[$framework]['li_parent']; ?>">
								<a href="#"><?php echo $topic->getTreeNodeDisplayName(); ?></a>
								<ul class="<?php echo $classes[$framework]['ul_nested']; ?>">
									<?php $walk($topic); ?>
								</ul>
							</li>	
							<?php } 
						}
					};
				$walk($node);				
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
