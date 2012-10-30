<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Assign Community Points'), false, false, false, array(), Page::getByPath('/dashboard/users/points', 'ACTIVE'))?>
<form method="post" action="<?=$this->action('save')?>" id="ccm-community-point-entry" class="form-horizontal">
<div class="ccm-pane-body">
	<?php if(isset($upID) && $upID > 0) {
		echo $form->hidden('upID',$upID);
	}?>
	<div class="control-group">
		<label class="control-label"><?php echo t('User');?></label>
		<div class="controls">
			<?php echo $form_user_selector->quickSelect('upUser',$upUser);?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo t('Action');?></label>
		<div class="controls">
			<?php echo $form->select('upaID',$userPointActions,$upaID,array('json-src'=>$this->action('getJsonDefaultPointAction'))); ?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo t('Points');?></label>
		<div class="controls">
			<?php echo $form->text('upPoints',$upPoints);?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo t('Comments');?></label>
		<div class="controls">
			<?php echo $form->textarea('upComments',$upComments);?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo t('Override Timestamp');?></label>
		<div class="controls">
		<div class="checkbox">
			<?php echo $form_date_time->datetime('dtoverride',$timestamp, true);?>
		</div>
		</div>
	</div>
</div>
<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/users/points')?>" class="btn"><?=t('Back to List')?></a>
	<button type="submit" class="ccm-button-right btn primary"><?=t('Assign')?> <i class="icon-white icon-ok"></i></button>
</div>
</form>

<script type="text/javascript">
$(function() {
	
	$('#upaID').change(function() {
		var src = $('#upaID').attr('json-src')+'-/'+$('#upaID').val();
		$.getJSON(src,function(j) {
			$('#upPoints').val(j);
		});
	});

	
});

</script>		
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>