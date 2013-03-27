<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$c = Page::getByPath('/dashboard/composer/list/form');
$cp = new Permissions($c);
$ih = Loader::helper('concrete/interface');
$set = ComposerFormLayoutSet::getByID($_REQUEST['cmpFormLayoutSetID']);
if (!is_object($set)) {
	die(t('Invalid set'));
}
if ($cp->canViewPage()) { 

	if ($_POST['cmpControlTypeID'] && $_POST['cmpControlIdentifier']) {
		$type = ComposerControlType::getByID($_POST['cmpControlTypeID']);
		$control = $type->getComposerControlByIdentifier($_POST['cmpControlIdentifier']);
		$layoutSetControl = $set->addComposerControl($control);
		print Loader::helper('json')->encode($layoutSetControl);
		exit;
	}

	?>

	<div class="ccm-ui">
	<?
	$tabs = array();
	$types = ComposerControlType::getList();
	for ($i = 0; $i < count($types); $i++) {
		$type = $types[$i];
		$tabs[] = array($type->getComposerControlTypeHandle(), $type->getComposerControlTypeName(), $i == 0);
	}

	print $ih->tabs($tabs);

	foreach($types as $t) { ?>

	<div class="ccm-tab-content" id="ccm-tab-content-<?=$t->getComposerControlTypeHandle()?>">
	<ul data-list="composer-control-type" class="item-select-list">
		<? 
		$controls = $t->getComposerControlObjects();
		foreach($controls as $cnt) { ?>
			<li><a href="#" data-control-type-id="<?=$t->getComposerControlTypeID()?>" data-control-identifier="<?=$cnt->getComposerControlIdentifier()?>" style="background-image: url('<?=$cnt->getComposerControlIconSRC()?>')"><?=$cnt->getComposerControlName()?></a></li>
		<? } ?>
	</ul>
	</div>


	<? } ?>

	</div>

<style type="text/css">
	ul.item-select-list li a {
		background-size: 16px 16px;
	}
</style>

<script type="text/javascript">
$(function() {
	$('ul[data-list=composer-control-type] a').on('click', function() {
		var cmpControlTypeID = $(this).attr('data-control-type-id');
		var cmpControlIdentifier = $(this).attr('data-control-identifier');
		var formData = [{
			'name': 'cmpControlTypeID',
			'value': cmpControlTypeID
		},{
			'name': 'cmpControlIdentifier',
			'value': cmpControlIdentifier
		},{
			'name': 'cmpFormLayoutSetID',
			'value': '<?=$set->getComposerFormLayoutSetID()?>'
		}];
		$.ajax({
			type: 'post',
			data: formData,
			dataType: 'json',
			url: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/composer/form/add_control',
			success: function(r) {
				ccm_parseJSON(r, function() {

				});
			}
		});

	});
});
</script>


<?

}