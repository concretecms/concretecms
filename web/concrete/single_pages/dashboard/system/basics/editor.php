<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Rich Text Editor'), t('Control the options available for TinyMCE.'), false, false);?>
<?php
$h = Loader::helper('concrete/interface');
?>	
<form method="post" id="txt-editor-form" class="form-horizontal" action="<?php echo $this->url('/dashboard/system/basics/editor', 'txt_editor_config')?>">
<div class="ccm-pane-body">
	<?php echo $this->controller->token->output('txt_editor_config')?>
	
	<div class="row">
      <div class="span5">
      	<legend><h3><?=t('Toolbar Set')?></h3></legend>
		<div class="control-group">
            <label id="optionsCheckboxes"></label>
            <div class="controls">
			  <ul class="inputs-list">
			    <li>
			      <label>
			        <input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="SIMPLE" <?php echo ( $txtEditorMode=='SIMPLE' || !strlen($txtEditorMode) )?'checked':''?> />
			        <span><?php echo t('Simple')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="ADVANCED" <?php echo ($txtEditorMode=='ADVANCED')?'checked':''?> />
			        <span><?php echo t('Advanced')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="OFFICE" <?php echo ($txtEditorMode=='OFFICE')?'checked':''?> />
			        <span><?php echo t('Office')?></span>
			      </label>
			    </li>
			    <li>
			      <label class="disabled">
			        <input type="radio" name="CONTENTS_TXT_EDITOR_MODE" value="CUSTOM" <?php echo ($txtEditorMode=='CUSTOM')?'checked':'' ?> /> 
			        <span><?php echo t('Custom')?></span>
			      </label>
			    </li>
			  </ul>
            </div>
          </div>

      </div>
      <div class="span6">
      	<legend><h3><?=t('Editor Dimensions')?></h3></legend>

			<div class="clearfix">
				<label for="xlInput"><?php echo t('Width ')?></label>
				<div class="input"><?
					if (!$textEditorWidth) { 
						$textEditorWidth = 580;
					}
					?>
				  <?=Loader::helper('form')->text('CONTENTS_TXT_EDITOR_WIDTH', $textEditorWidth, array('class' => 'span1'))?>
				</div>
			</div>
			
			<div class="clearfix">
				<label for="xlInput"><?php echo t('Height ')?></label>
				<div class="input"><?
					if (!$textEditorHeight) { 
						$textEditorHeight = 380;
					}
					?>
				  <?=Loader::helper('form')->text('CONTENTS_TXT_EDITOR_HEIGHT', $textEditorHeight, array('class' => 'span1'))?>
				</div>
			</div>
 
      </div>
    </div>
	<br/>
		
	<div id="text-editor-simple" style=" display:<?php echo ($txtEditorMode=='SIMPLE' || $txtEditorMode == '')?'block':'none' ?>">
		<h4><?=t('Preview')?></h4>
		<img src="<?=ASSETS_URL_IMAGES?>/editor_simple.png" width="630" height="65"  />
	</div>
	
	<div id="text-editor-advanced" style=" display:<?php echo ($txtEditorMode=='ADVANCED')?'block':'none' ?>">
		<h4><?=t('Preview')?></h4>
		<img src="<?=ASSETS_URL_IMAGES?>/editor_advanced.png" width="630" height="81"  />
	</div>
	
	<div id="text-editor-office" style=" display:<?php echo ($txtEditorMode=='OFFICE')?'block':'none' ?>">
		<h4><?=t('Preview')?></h4>
		<img src="<?=ASSETS_URL_IMAGES?>/editor_office.png" width="630" height="107"  />
	</div>
	
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
			$("#text-editor-simple").hide();
			$("#text-editor-advanced").hide();
			$("#text-editor-office").hide();
			if($("input[name='CONTENTS_TXT_EDITOR_MODE']:checked").val()=='SIMPLE'){
				$('#text-editor-simple').css('display','block');
			}
			if($("input[name='CONTENTS_TXT_EDITOR_MODE']:checked").val()=='ADVANCED'){
				$('#text-editor-advanced').css('display','block');
			}
			if($("input[name='CONTENTS_TXT_EDITOR_MODE']:checked").val()=='OFFICE'){
				$('#text-editor-office').css('display','block');
			}
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