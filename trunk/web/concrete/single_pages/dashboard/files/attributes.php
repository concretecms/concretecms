<?
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form'); 
$valt = Loader::helper('validation/token');
?>

<? if($pageMode!='edit' && $pageMode!='add'){ ?>
	<h1><span><?=t('Files Attributes')?></span></h1>
	
	<div class="ccm-dashboard-inner">
	
		<?=$form->checkbox('showUserAdded', 1, $showUserAdded)?>
		<?=t('Show Only User Added Attributes')?>
		
		<script type="text/javascript">
		$(function() {
			$("input[name=showUserAdded]").click(function() {
				if ($(this).get(0).checked) {
					window.location.href='<?=$this->url('/dashboard/files/attributes', 'show_user_added')?>';
				} else {
					window.location.href='<?=$this->url('/dashboard/files/attributes')?>';
				}
			});
		});
		</script>
		
		<?= Loader::element('dashboard/attributes_table', array( 'attribs'=>$attribs, 'attributeType'=>'file') ); ?>
	
	</div>
<? } ?>

<? if($pageMode=='edit'){ ?>

	<h1><span><?=t('Edit a File Attribute')?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="ccm-add-attribute" action="<?=$this->url('/dashboard/files/attributes/-/edit/')?>" onsubmit="return ccmAttributesHelper.doSubmit">
		<input type="hidden" name="submitted" value="1" />
		<input type="hidden" name="edit" value="1" />
		<input type="hidden" name="fakID" value="<?=$fak->getAttributeKeyID() ?>" />
		<?=$valt->output('add_or_update_attribute')?>
		
		<?
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
	
<? }else{ ?>

	<h1><span><?=t('Add a File Attribute')?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="ccm-add-attribute" action="<?=$this->url('/dashboard/files/attributes/-/add/')?>" onsubmit="return ccmAttributesHelper.doSubmit">
		<input type="hidden" name="add" value="1" />
		<input type="hidden" name="submitted" value="1" />
		<?=$valt->output('add_or_update_attribute')?>
		
		<?
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
	
<? } ?>