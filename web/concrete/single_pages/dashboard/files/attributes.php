<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? if (isset($key)) { ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Attribute'), false, false, false)?>
<form method="post" action="<?=$this->action('edit')?>" id="ccm-attribute-key-form">



<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>




<? } else if ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add' || $this->controller->getTask() == 'edit') { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Attributes'), false, false, false)?>

	<? if (isset($type)) { ?>
		<form method="post" action="<?=$this->action('add')?>" id="ccm-attribute-key-form">
	
		<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
	
		</form>	
	<? } ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>



<? } else { ?>

	<?
	$attribs = FileAttributeKey::getList();
	Loader::element('dashboard/attributes_table', array('category' => $category, 'attribs'=> $attribs, 'editURL' => '/dashboard/files/attributes')); ?>

	<form method="get" action="<?=$this->action('select_type')?>" id="ccm-attribute-type-form">
		<label for="atID"><?=t('Add Attribute')?></label>
		<div class="form-inline">
		<div class="form-group">
			<?=$form->select('atID', $types)?>
		</div>
		<button type="submit" class="btn btn-default"><?=t('Go')?></button>
		</div>
	</form>

<? } ?>