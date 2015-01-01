<?php defined('C5_EXECUTE') or die('Access Denied.');

if ( !is_object($bt) ) $bt = BlockType::getByHandle($bt);
if ( !is_object($bt) ) return;

$ci = \Core::make( 'helper/concrete/urls' );
?>

<div class="ccm-block-type-display <?=$class?>">
	<img src="<?php echo $ci->getBlockTypeIconURL($bt) ?>" alt="<?php echo t('block type icon')?>"/>
	<span>
    <?php if (!$nolink) { ?><a href="<?php echo $view->url('/dashboard/blocks/types', 'inspect', $bt->getBlockTypeID()); ?>"><?php } ?><?php echo t($bt->getBlockTypeName()); ?><?php if (!$nolink){?></a><?php } ?>
	</span>
</div>
