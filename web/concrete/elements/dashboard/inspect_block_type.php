<?php defined('C5_EXECUTE') or die('Access Denied.');

if ( !is_object($bt) ) $bt = BlockType::getByHandle($bt);
if ( !is_object($bt) ) return;

$ci = \Core::make( 'helper/concrete/urls' );
?>

<div class="row clearfix">
	<div class="col-sm-1"><?php Loader::element( 'dashboard/block_type_icon', array( 'bt' => $bt ) ); ?></div>
	<div class="col-sm-11 ccm-block-type-description" >
		<div class="ccm-block-type-description-title">
			<a href="<?php echo $view->url('/dashboard/blocks/types', 'inspect', $bt->getBlockTypeID()); ?>"><?php echo t($bt->getBlockTypeName()); ?></a> 
			<div class="ccm-block-sets"><?php foreach ( $bt->getBlockTypeSets() as $set ) echo '<span class="badge">' . $set->getBlockTypeSetName() . '</span>'; ?></div>
		</div>
		<div class="ccm-block-type-description-usage">
			<?php echo t('Usage Count: %s (%s%s on active pages %s)', $bt->getCount(), "<strong>", $bt->getCount(true), "</strong>" ) ?>
			<? if ($bt->isBlockTypeInternal()) { ?>
				<span class="label label-danger"><?=t( "internal block type")?></span>
			<? } ?>
		</div>
		<div class="ccm-block-type-description-content"> <?php echo t($bt->getBlockTypeDescription()); ?> </div>
	</div>
</div>
