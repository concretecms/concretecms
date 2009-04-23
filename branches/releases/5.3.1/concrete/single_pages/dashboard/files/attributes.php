<?php 
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form'); 
$valt = Loader::helper('validation/token');
?>

<?php  if($pageMode!='edit' && $pageMode!='add'){ ?>
	<h1><span><?php echo t('Files Attributes')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
		<?php echo $form->checkbox('showUserAdded', 1, $showUserAdded)?>
		<?php echo t('Show Only User Added Attributes')?>
		
		<script type="text/javascript">
		$(function() {
			$("input[name=showUserAdded]").click(function() {
				if ($(this).get(0).checked) {
					window.location.href='<?php echo $this->url('/dashboard/files/attributes', 'show_user_added')?>';
				} else {
					window.location.href='<?php echo $this->url('/dashboard/files/attributes')?>';
				}
			});
		});
		</script>
		
		<?php echo  Loader::element('dashboard/attributes_table', array( 'attribs'=>$attribs, 'attributeType'=>'file') ); ?>
	
	</div>
<?php  } ?>

<?php  if($pageMode=='edit'){ ?>

	<h1><span><?php echo t('Edit a File Attribute')?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="ccm-add-attribute" action="<?php echo $this->url('/dashboard/files/attributes/-/edit/')?>" onsubmit="return ccmAttributesHelper.doSubmit">
		<input type="hidden" name="submitted" value="1" />
		<input type="hidden" name="edit" value="1" />
		<input type="hidden" name="fakID" value="<?php echo $fak->getAttributeKeyID() ?>" />
		<?php echo $valt->output('add_or_update_attribute')?>
		
		<?php 
		$attributeFormData=array(
				'akType'=>$fak->getAttributeKeyType(),
				'akName'=>$fak->getAttributeKeyName(),
				'akHandle'=>$fak->getAttributeKeyHandle(), 
				'akValues'=>$fak->getAttributeKeyValues(),				
				'akAllowOtherValues'=>$fak->getAllowOtherValues(), 
				'cancelURL'=>'/dashboard/files/attributes',
				'defaultNewOptionNm'=>$defaultNewOptionNm,
				'formId'=>'ccm-add-attribute',
				'submitBtnTxt'=>t('Update'),
				'attributeType' => 'file',
				'noSearchable'=>1
			);
		Loader::element('dashboard/attribute_form', $attributeFormData);
		?>
		
		<br>
		</form>	
	
	</div>
	
<?php  }else{ ?>

	<h1><span><?php echo t('Add a File Attribute')?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="ccm-add-attribute" action="<?php echo $this->url('/dashboard/files/attributes/-/add/')?>" onsubmit="return ccmAttributesHelper.doSubmit">
		<input type="hidden" name="add" value="1" />
		<input type="hidden" name="submitted" value="1" />
		<?php echo $valt->output('add_or_update_attribute')?>
		
		<?php 
		$attributeFormData=array(
				'akType'=>$_POST['akType'],
				'akName'=>$_POST['akName'],
				'akHandle'=>$_POST['akHandle'], 
				'akValues'=>$_POST['akValues'], 
				'akAllowOtherValues'=>$_POST['akAllowOtherValues'],
				'cancelURL'=>'/dashboard/files/attributes',
				'defaultNewOptionNm'=>$defaultNewOptionNm,
				'formId'=>'ccm-add-attribute',
				'submitBtnTxt'=>t('Add'),
				'noSearchable'=>1
			);
		Loader::element('dashboard/attribute_form', $attributeFormData);
		?>
		
		<br>
		</form>	
	
	
	</div>
	
<?php  } ?>