
<?php  if ($this->controller->getTask() != 'select_type' && $this->controller->getTask() != 'add' && $this->controller->getTask() != 'edit') { ?>
	<h1><a class="ccm-dashboard-header-option" href="<?php echo $this->url('/dashboard/settings/', 'manage_attribute_types')?>">Manage Attribute Types</a><span><?php echo t('Attributes')?></span></h1>
	<div class="ccm-dashboard-inner">
	<?php 
	$attribs = UserAttributeKey::getList();
	Loader::element('dashboard/attributes_table', array('category' => $category, 'attribs'=> $attribs, 'editURL' => '/dashboard/users/attributes')); ?>

	</div>

<script type="text/javascript">
$(function() {
	$("div.ccm-attributes-list").sortable({
		handle: 'img.ccm-attribute-icon',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			$.post('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/user_attributes_update.php', ualist, function(r) {

			});
		}
	});
});

</script>

<style type="text/css">
div.ccm-attributes-list img.ccm-attribute-icon:hover {cursor: move}
</style>

<?php  } ?>


<?php  if (isset($key)) { ?>

<?php  $valt = Loader::helper('validation/token'); ?>
<?php  $ih = Loader::helper('concrete/interface'); ?>

<h1><span><?php echo t('Edit Attribute')?></span></h1>
<div class="ccm-dashboard-inner">

<?php  if ($key->isAttributeKeyActive()) { ?>
	<?php  print $ih->button(t('Deactivate'), $this->url('/dashboard/users/attributes', 'deactivate', $key->getAttributeKeyID(), $valt->generate('attribute_deactivate')));?>
<?php  } else { ?>
	<?php  print $ih->button(t('Activate'), $this->url('/dashboard/users/attributes', 'activate', $key->getAttributeKeyID(), $valt->generate('attribute_activate')));?>
<?php  } ?>


<h2><?php echo t('Attribute Type')?></h2>

<strong><?php echo $type->getAttributeTypeName()?></strong>
<br/><br/>


<form method="post" action="<?php echo $this->action('edit')?>" id="ccm-attribute-key-form">

<?php  Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>	

</div>

<h1><span><?php echo t('Delete Attribute')?></span></h1>

<div class="ccm-dashboard-inner">
	<div class="ccm-spacer"></div>
	<?php 
	$delConfirmJS = t('Are you sure you want to remove this attribute?');
	?>
	<script type="text/javascript">
	deleteAttribute = function() {
		if (confirm('<?php echo $delConfirmJS?>')) { 
			location.href = "<?php echo $this->url('/dashboard/users/attributes', 'delete', $key->getAttributeKeyID(), $valt->generate('delete_attribute'))?>";				
		}
	}
	</script>
	<?php  print $ih->button_js(t('Delete Attribute'), "deleteAttribute()", 'left');?>

	<div class="ccm-spacer"></div>
</div>


<?php  } else { ?>

<h1><span><?php echo t('Add User Attribute')?></span></h1>
<div class="ccm-dashboard-inner">

<h2><?php echo t('Choose Attribute Type')?></h2>

<form method="get" action="<?php echo $this->action('select_type')?>" id="ccm-attribute-type-form">

<?php echo $form->select('atID', $types)?>
<?php echo $form->submit('submit', t('Go'))?>

</form>

<?php  if (isset($type)) { ?>
	<br/>

	<form method="post" action="<?php echo $this->action('add')?>" id="ccm-attribute-key-form">

	<?php  Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>

	</form>	
<?php  } ?>

</div>

<?php  } ?>