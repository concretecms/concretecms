
<? if ($this->controller->getTask() != 'select_type' && $this->controller->getTask() != 'add' && $this->controller->getTask() != 'edit') { ?>
	<h1><a class="ccm-dashboard-header-option" href="<?=$this->url('/dashboard/settings/', 'manage_attribute_types')?>"><?=t('Manage Attribute Types')?></a><span><?=t('Attributes')?></span></h1>
	<div class="ccm-dashboard-inner">
	<?
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
			$.post('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/user_attributes_update.php', ualist, function(r) {

			});
		}
	});
});

</script>

<style type="text/css">
div.ccm-attributes-list img.ccm-attribute-icon:hover {cursor: move}
</style>

<? } ?>


<? if (isset($key)) { ?>

<? $valt = Loader::helper('validation/token'); ?>
<? $ih = Loader::helper('concrete/interface'); ?>

<h1><span><?=t('Edit Attribute')?></span></h1>
<div class="ccm-dashboard-inner">

<? if ($key->isAttributeKeyActive()) { ?>
	<? print $ih->button(t('Deactivate'), $this->url('/dashboard/users/attributes', 'deactivate', $key->getAttributeKeyID(), $valt->generate('attribute_deactivate')));?>
<? } else { ?>
	<? print $ih->button(t('Activate'), $this->url('/dashboard/users/attributes', 'activate', $key->getAttributeKeyID(), $valt->generate('attribute_activate')));?>
<? } ?>


<h2><?=t('Attribute Type')?></h2>

<strong><?=$type->getAttributeTypeName()?></strong>
<br/><br/>


<form method="post" action="<?=$this->action('edit')?>" id="ccm-attribute-key-form">

<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>	

</div>

<h1><span><?=t('Delete Attribute')?></span></h1>

<div class="ccm-dashboard-inner">
	<div class="ccm-spacer"></div>
	<?
	$delConfirmJS = t('Are you sure you want to remove this attribute?');
	?>
	<script type="text/javascript">
	deleteAttribute = function() {
		if (confirm('<?=$delConfirmJS?>')) { 
			location.href = "<?=$this->url('/dashboard/users/attributes', 'delete', $key->getAttributeKeyID(), $valt->generate('delete_attribute'))?>";				
		}
	}
	</script>
	<? print $ih->button_js(t('Delete Attribute'), "deleteAttribute()", 'left');?>

	<div class="ccm-spacer"></div>
</div>


<? } else { ?>

<h1><span><?=t('Add User Attribute')?></span></h1>
<div class="ccm-dashboard-inner">

<h2><?=t('Choose Attribute Type')?></h2>

<form method="get" action="<?=$this->action('select_type')?>" id="ccm-attribute-type-form">

<?=$form->select('atID', $types)?>
<?=$form->submit('submit', t('Go'))?>

</form>

<? if (isset($type)) { ?>
	<br/>

	<form method="post" action="<?=$this->action('add')?>" id="ccm-attribute-key-form">

	<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>

	</form>	
<? } ?>

</div>

<? } ?>