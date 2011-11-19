<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Rich Text Editor'), t('Control the options available for TinyMCE.'), false, false);?>
<?php
$h = Loader::helper('concrete/interface');
?>	
<form method="post" id="txt-editor-form" action="<?php echo $this->url('/dashboard/system/basics/editor', 'txt_editor_config')?>">
<div class="ccm-pane-body">
	<?php echo $this->controller->token->output('txt_editor_config')?>
	
	<div class="row">
      <div class="span7">
      	<legend><h3><?=t('Toolbar Set')?></h3></legend>
		<div class="clearfix">
            <label id="optionsCheckboxes"></label>
            <div class="input">
			  <ul class="inputs-list">
			    <li>
			      <label>
			        <input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="SIMPLE" style="vertical-align: middle" <?php echo ( $txtEditorMode=='SIMPLE' || !strlen($txtEditorMode) )?'checked':''?> />
			        <span><?php echo t('Simple')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="ADVANCED" style="vertical-align: middle" <?php echo ($txtEditorMode=='ADVANCED')?'checked':''?> />
			        <span><?php echo t('Advanced')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="OFFICE" style="vertical-align: middle" <?php echo ($txtEditorMode=='OFFICE')?'checked':''?> />
			        <span><?php echo t('Office')?></span>
			      </label>
			    </li>
			    <li>
			      <label class="disabled">
			        <input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="CUSTOM" style="vertical-align: middle" <?php echo ($txtEditorMode=='CUSTOM')?'checked':'' ?> /> 
			        <span><?php echo t('Custom')?></span>
			      </label>
			    </li>
			  </ul>
            </div>
          </div>

      </div>
      <div class="span7">
      	<legend><h3><?=t('Editor Dimensions')?></h3></legend>

			<div class="clearfix">
				<label for="xlInput"><?php echo t('Width ')?></label>
				<div class="input">
				  <input type="text" name="CONTENTS_TXT_EDITOR_WIDTH" size="3" value="<?php echo ($textEditorWidth<580) ? 580 : intval($textEditorWidth) ?>"/>&nbsp;px
				</div>
			</div>
			
			<div class="clearfix">
				<label for="xlInput"><?php echo t('Height ')?></label>
				<div class="input">
				  <input type="text" name="CONTENTS_TXT_EDITOR_HEIGHT" size="3" value="<?php echo ($textEditorHeight<100) ? 380 : intval($textEditorHeight) ?>"/>&nbsp;px
				</div>
			</div>
 
        <span class="help-block">
            <strong><?php echo t('Note:')?></strong> <?php echo t('The minimum width is 580px.')?>
        </span>
      </div>
    </div>
	<br/>
			
	<div id="cstmEditorTxtAreaWrap" style=" display:<?php echo ($txtEditorMode=='CUSTOM')?'block':'none' ?>" >
		<textarea wrap="off" name="CONTENTS_TXT_EDITOR_CUSTOM_CODE" cols="25" rows="20" style="width: 97%; height: 250px;"><?php echo $txtEditorCstmCode?></textarea>
		<div class="ccm-note"><a target="_blank" href="http://tinymce.moxiecode.com/"><?php echo t('TinyMCE Reference')?></a></div>
	</div>
		

	<script>		
		$(function(){ 
			$("input[name='CONTENTS_TXT_EDITOR_MODE']").each(function(i,el){ 
				el.onchange=function(){isTxtEditorModeCustom();}
			})	 	
		});
		function isTxtEditorModeCustom(){
			if($("input[name='CONTENTS_TXT_EDITOR_MODE']:checked").val()=='CUSTOM'){
				$('#cstmEditorTxtAreaWrap').css('display','block');
			}else{
				$('#cstmEditorTxtAreaWrap').css('display','none');
			}
		}
	</script>
</div>
<div class="ccm-pane-footer">
		<?php  
		$b1 = $h->submit(t('Save'), 'txt-editor-form', 'right', 'primary');
		print $b1;
		?>
</div>
</form>



<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>