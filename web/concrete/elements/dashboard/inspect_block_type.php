<?php defined('C5_EXECUTE') or die('Access Denied.');

if ( !is_object($bt) ) $bt = BlockType::getByHandle($bt);
if ( !is_object($bt) ) return;

$ci = \Core::make( 'helper/concrete/urls' );
$btID = $bt->getBlockTypeID();

?>

<li id="btID_<?=$btID?>"  data-btid="<?=$btID?>">
        <?php foreach ( $bt->getBlockTypeSets() as $set ) echo '<span class="badge pull-right">' . $set->getBlockTypeSetName() . '</span>'; ?>
        <div class="pull-right" style="margin-left: 10px; margin-right: 10px" ><?php echo t('Usage Count: %s (%s%s on active pages %s)', $bt->getCount(), "<strong>", $bt->getCount(true), "</strong>" ) ?></div>
        <a href="<?= $view->url('/dashboard/blocks/types', 'inspect', $bt->getBlockTypeID()); ?>"><img src="<?=$ci->getBlockTypeIconURL($bt)?>" /> <?=t($bt->getBlockTypeName()); ?></a>
</li>

<?php return ?>


	<div class="ccm-block-type-description" >
		<div class="ccm-block-type-description-usage">
			<? if ($bt->isBlockTypeInternal()) { ?>
				<span class="label label-danger"><?=t( "internal block type")?></span>
			<? } ?>
		</div>
		<div class="ccm-block-type-description-content"> <?php echo t($bt->getBlockTypeDescription()); ?> </div>
	</div>
