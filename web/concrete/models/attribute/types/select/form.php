<?php defined('C5_EXECUTE') or die("Access Denied."); 

$form = Loader::helper('form');
$json = Loader::helper('json');

// Set's helpful, accessible instruction title attribute text on our UI buttons.
// Saves us battling with some of the convoluted JS string concatenation below
// where the t() function is involved. Saves hitting the function multiple times too.
// @var String
$removeOptionText = t('Remove Option');

if ($akSelectAllowMultipleValues && $akSelectAllowOtherValues) { // display autocomplete form
	$attrKeyID = $this->attributeKey->getAttributeKeyID();
	?>

	<style type="text/css">
		.ccm-ui .ccm-attribute-type-select-autocomplete .newAttrValue,
		.ccm-ui .ccm-attribute-type-select-autocomplete .existingAttrValue {
			padding-top: 3px;
			margin-right: 12px;
			float: left;
			line-height: 20px;
		}
		.ccm-ui .ccm-attribute-type-select-autocomplete h6 {
			margin-bottom: 2px;
		}
		.ccm-ui .ccm-attribute-type-select-autocomplete .well {
			margin-bottom: 5px;
			max-width: 500px;
			padding-bottom: 12px;
		}
		.ccm-ui .ccm-attribute-type-select-autocomplete .text-error {
			color: #b94a48 !important;
			font-weight: bold;
		}
	</style>

<div class="ccm-attribute-type-select-autocomplete">

	<div id="selectedAttrValueRows_<?php echo $attrKeyID;?>" class="well well-small clearfix">
		<h6><?=t('Selected Options')?></h6>
		<?php
		foreach($selectedOptions as $optID) {
			$opt = SelectAttributeTypeOption::getByID($optID);

			?>
			<div class="existingAttrValue">
				<?=$form->hidden($this->field('atSelectOptionID') . '[]', $opt->getSelectAttributeOptionID(), array('style'=>'position:relative;')); ?>
				<span class="badge"><?=$opt->getSelectAttributeOptionValue()?></span>
				<a class="text-error" title="<?=$removeOptionText?>" href="javascript:void(0);" onclick="$(this).parent().remove()">x</a>
			</div>
		<? }

		// now we get items from the post
		$vals = $this->post('atSelectNewOption');
		if (is_array($vals)) {
			foreach($vals as $v) { ?>
				<div class="newAttrValue">
					<?=$form->hidden($this->field('atSelectNewOption') . '[]', $v)?>
					<span class="badge"><?=$v?></span>
					<a class="text-error" title="<?=$removeOptionText?>" onclick="ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.remove(this)" href="javascript:void(0)">x</a>
				</div>
			<?
			}
		}

		?>
	</div>
	<span style="position: relative">

	<?php
	echo $form->text('newAttrValueRows'.$attrKeyID, array('class' => 'ccm-attribute-type-select-autocomplete-text', 'style'=>'position:relative; width: 200px'));
	?>
	<input type="button" class="btn ccm-input-button" value="<?=t('Add')?>" onclick="ccmAttributeTypeSelectTagHelper<?=$attrKeyID?>.addButtonClick(); return false" />
	</span>
</div>

	<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var availableTags = <?=$json->encode($opt_values);?>;
		$("#newAttrValueRows<?php echo $attrKeyID?>").autocomplete({
			source: "<?=$this->action('load_autocomplete_values')?>",
			select: function( event, ui ) {
				ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.add(ui.item.value);
				$(this).val('');
				return false;
			}
		});

		$("#newAttrValueRows<?php echo $attrKeyID?>").bind("keydown", function(e) {
			if (e.keyCode == 13) { // comma or enter
				if($(this).val().length > 0) {
					ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.add($(this).val());
					$(this).val('');
					$("#newAttrValueRows<?php echo $this->attributeKey->getAttributeKeyID()?>").autocomplete( "close" );
				}
				return false;
			}
		});
	});

	var ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>={
			addButtonClick: function() {
				// Get and cache our row element
				var valrow = $("input[name=newAttrValueRows<?=$attrKeyID?>]");
				// Get and cache our row element's value
				var valrowval = valrow.val();

				// Any text actually entered? If value passed, continue!
				// A length check was being done on keydown (enter press) but not
				// when button clicked (!). Fixed now. Might avoid some of those ugly, empty
				// option rows appearing in the attribute select type options db.
				if(valrowval.length > 0) {
					ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.add(valrowval);
					valrow.val('');
					$("#newAttrValueRows<?php echo $this->attributeKey->getAttributeKeyID()?>").autocomplete( "close" );
				}
				return false;
			},
			add:function(value){
				var newRow=document.createElement('div');
				newRow.className='newAttrValue';
				newRow.innerHTML='<input name="<?=$this->field('atSelectNewOption')?>[]" type="hidden" value="'+value+'" /> ';
				newRow.innerHTML+='<span class="badge">'+value+'</span>';
				newRow.innerHTML+=' <a class="text-error" title="<?=$removeOptionText?>" onclick="ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.remove(this)" href="javascript:void(0)">x</a>';
				$('#selectedAttrValueRows_<?php echo $attrKeyID;?>').append(newRow);
			},
			remove:function(a){
				$(a.parentNode).remove();
			}
		}
	//]]>
	</script>
	<?php
} else {
	?>

	<style type="text/css">
		.ccm-ui .newAttrValueRow {
			padding-top: 2px;
		}
	</style>

	<?php

	$options = $this->controller->getOptions();

	if ($akSelectAllowMultipleValues) { ?>

		<? foreach($options as $opt) { ?>
			<label class="checkbox">
				<?=$form->checkbox($this->field('atSelectOptionID') . '[]', $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptions)); ?>
				<?=$opt->getSelectAttributeOptionValue()?></label>
		<? } ?>
	<? } else {
		$opts = array('' => t('** None'));
		foreach($options as $opt) {
			$opts[$opt->getSelectAttributeOptionID()] = $opt->getSelectAttributeOptionValue();
		}
		?>
		<?=$form->select($this->field('atSelectOptionID') . '[]', $opts, $selectedOptions[0]); ?>

	<? }

	if ($akSelectAllowOtherValues) { ?>
		<div id="newAttrValueRows<?=$this->attributeKey->getAttributeKeyID()?>" class="newAttrValueRows"></div>
		<div style="padding-top: 5px;">
			<a title="<?=t('Add Another Option')?>" class="btn btn-small" href="javascript:void(0)" onclick="ccmAttributeTypeSelectHelper.add(<?=$this->attributeKey->getAttributeKeyID()?>, '<?=$this->field('atSelectNewOption')?>[]')">
				<?=t('Add Another Option')?>
			</a>
		</div>
	<? } ?>

	<script type="text/javascript">
	//<![CDATA[
	var ccmAttributeTypeSelectHelper={
		add:function(akID, field){
			var newRow=document.createElement('div');
			newRow.className='newAttrValueRow';
			newRow.innerHTML='<input name="' + field + '" type="text" value="" /> ';
			newRow.innerHTML+='<a title="<?=$removeOptionText?>" class="btn btn-mini btn-danger" onclick="ccmAttributeTypeSelectHelper.remove(this)" href="javascript:void(0)"><i class="icon icon-white icon-trash"></i></a>';
			$('#newAttrValueRows'+akID).append(newRow);
		},
		remove:function(a){
			$(a.parentNode).remove();
		}
	}
	//]]>
	</script>
<?php } ?>
