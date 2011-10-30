<? defined('C5_EXECUTE') or die("Access Denied.");

$types = AttributeType::getList();
$categories = AttributeKeyCategory::getList();
$txt = Loader::helper('text');
$form = Loader::helper('form');
$interface = Loader::helper('concrete/interface');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Attribute Type Associations'), false);?>
<form method="post" id="attribute_type_associations_form" action="<?=$this->action('save_attribute_type_associations')?>">
	<table border="0" cellspacing="1" cellpadding="0" border="0" class="grid-list">
		<tr>
			<td class="header"><?=t('Name')?></td>
			<? foreach($categories as $cat) { ?>
				<td class="header" width="22%"><?=$txt->unhandle($cat->getAttributeKeyCategoryHandle())?></td>
			<? } ?>
		</tr>
		<?php foreach($types as $at) { ?>

			<tr>
				<td><strong><?=$at->getAttributeTypeName()?></strong></td>
				<? foreach($categories as $cat) { ?>
					<td><?=$form->checkbox($cat->getAttributeKeyCategoryHandle() . '[]', $at->getAttributeTypeID(), $at->isAssociatedWithCategory($cat))?></td>
				<? } ?>
			</tr>

		<? } ?>

	</table>
	<br/>
	<?
	$b1 = $interface->submit(t('Save'), 'attribute_type_associations_form', 'right', 'primary');
	print $b1;
	?>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);
echo '<br />';
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Custom Attribute Types'), false);
$types = PendingAttributeType::getList(); ?>
<? if (count($types) == 0) { ?>
	<?=t('There are no available attribute types awaiting installation.')?>
<? } else { ?>
	<table border="0" cellspacing="0" cellpadding="0">
		<? foreach($types as $at) { ?>
			<tr>
				<td style="padding: 0px 10px 10px 0px"><img src="<?=$at->getAttributeTypeIconSRC()?>" /></td>
				<td style="padding:  0px 10px 10px 0px"><?=$at->getAttributeTypeName()?></td>
				<td style="padding:  0px 10px 10px 0px">
					<form id="attribute_type_install_form_<?=$at->getAttributeTypeHandle()?>" method="post" action="<?=$this->action('add_attribute_type')?>"><?
						print $form->hidden("atHandle", $at->getAttributeTypeHandle());
						$b1 = $interface->submit(t('Install'), 'attribute_type_install_form_' . $at->getAttributeTypeHandle(), 'right', 'primary');
						print $b1;
					?>
					</form>
				</td>
			</tr>
		<? } ?>
	</table>
<? } 
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);