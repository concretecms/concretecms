<?php defined('C5_EXECUTE') or die('Access Denied.');

if ( !is_object($pkg) ) $pkg = Package::getByHandle($pkg);
if ( !is_object($pkg) ) return;

$ci  = \Core::make('helper/concrete/urls' );
$txt = \Core::make('helper/text');
$nav = \Core::make('helper/navigation');

$items = $pkg->getPackageItems();


use \Concrete\Core\Attribute\Key\Category as AttributeCategory;
$catList = AttributeCategory::getList();

?>

<table class="table table-bordered table-striped">
	<tr>
		<td class="ccm-marketplace-list-thumbnail"><img src="<?= $ci->getPackageIconURL($pkg); ?>" /></td>
		<td class="ccm-addon-list-description" style="width: 100%"><h3><?= $pkg->getPackageName(); ?> - <?= $pkg->getPackageVersion(); ?></h3><?= $pkg->getPackageDescription(); ?></td>
	</tr>
</table>


<?php if ( count($items['block_types']) > 0 ) { ?>
<div class="form-group">
	<legend><?= $pkg->getPackageItemsCategoryDisplayName('block_types_sets'); ?></legend>
	<dl class="dl-horizontal ccm-block-set-list">
	<?php foreach ($items['block_type_sets'] as $bset ) { ?>
		<dt><span class="badge"><?=$bset->getBlockTypeSetName()?></span></dt>
		<dd>
			<span><?= t('Containing the following blocks') ?></span> 
			<span class="mini"><?php foreach ( $bset->getBlockTypes() as $bt ) Loader::element( 'dashboard/block_type_icon', array( 'bt' => $bt ) ); ?></span>
		</dd>
	<?php } ?>
	</dl>
</div>
<?php } unset($items['block_type_sets'] ); ?>

<?php if (count($items['block_types']) > 0) { ?>
<legend><?= $pkg->getPackageItemsCategoryDisplayName('block_types'); ?></legend>
<div class="form-group">
	<?php 
	foreach ($items['block_types'] as $bt) Loader::element( 'dashboard/inspect_block_type', array( 'bt' => $bt ) );
	unset($items['block_types']) 
	?>
</div>
<?php } ?>

<?php if ( count($items['attribute_types']) > 0 ) { ?>
<div class="form-group">
	<legend><?= $pkg->getPackageItemsCategoryDisplayName('attribute_types'); ?></legend>
	<dl class="dl-horizontal">
		<?php foreach ( $items['attribute_types'] as $at ) { ?>
		<dt><img src="<?=$at->getAttributeTypeIconSRC()?>" alt="<?=t('attribute type icon')?>"/></dt>
		<dd>
			<?=$at->getAttributeTypeName()?>
			<?php foreach ( $catList as $cat ) { if ( !$at->isAssociatedWithCategory($cat) ) continue; ?>
			<span class="badge"><?=$txt->unhandle($cat->getAttributeKeyCategoryHandle())?></span>
			<?php } ?>
		</dd>

		<?php } ?>
	</dl>
</div>
<?php } unset($items['attribute_types'] ); ?>

<?php 
	Loader::element('dashboard/package_element_list', array( 'pkg' => $pkg, 'itemArray' => $items['attribute_keys'], 'key' => 'attribute_keys')  );
	unset($items['attribute_keys'] ); 
?>

<?php if ( count($items['page_themes']) > 0 ) { ?>
<div class="form-group">
	<legend><?= $pkg->getPackageItemsCategoryDisplayName('page_themes'); ?></legend>
	<ul class="list-unstyled">
	<?php foreach( $items['page_themes'] as $theme) { ?>
        <li class="clearfix row">
            <div class="ccm-themes-thumbnail"><?=$theme->getThemeThumbnail()?></div>
            <div class="ccm-theme-description" >
                <div class="ccm-theme-description-title"><a href="<?=$view->url('/dashboard/pages/themes/inspect', $theme->getThemeID())?>"><?=$theme->getThemeDisplayName()?></a></div>
                <div class="ccm-theme-description-content"> <?php echo $theme->getThemeDisplayDescription(); ?> </div>
            </div>
        </li>
	<?php } ?>
	</ul>
</div>
<?php } unset($items['page_themes'] ); ?>

<?php if ( count($items['single_pages']) > 0 ) { ?>
<div class="form-group">
	<legend><?= $pkg->getPackageItemsCategoryDisplayName('single_pages'); ?></legend>
	<ul class="list-unstyled">
	<?php foreach( $items['single_pages'] as $page ) { ?>
        <li class="clearfix row">
            <span class="col-sm-2"><a href="<?=$nav->getLinkToCollection($page)?>"><?=$page->getCollectionName()?></a></span>
            <span class="col-sm-3"><code><?=$page->getCollectionPath()?></code></span>
            <span class="col-sm-5"><?=$page->getCollectionDescription()?></span>
        </li>
	<?php } ?>
	</ul>
</div>
<?php } unset($items['single_pages'] ); ?>


<!-- Show all remaining items that we don't have a better formatting for !-->

<?php foreach ($items as $key => $itemArray) Loader::element('dashboard/package_element_list', array( 'pkg' => $pkg, 'itemArray' => $itemArray, 'key' => $key ) ); ?>
