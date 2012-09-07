<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Public Profiles'), t('Control the options available for Public Profiles.'), 'span8 offset2', false);?>
<?php
$h = Loader::helper('concrete/interface');
$form = Loader::helper('form');
?>
    <form method="post" class="form-horizontal" id="public-profiles-form" action="<?php echo $this->url('/dashboard/system/registration/profiles', 'update_profiles')?>">  
    
    <div class="ccm-pane-body"> 
    	
    	<div class="control-group">
            <label id="optionsCheckboxes" for="public_profiles" class="control-label"><?php echo t('Profile Options')?></label>
            <div class="controls">
			      <label class="checkbox">
			        <input type="checkbox" id="public_profiles" name="public_profiles" value="1" <?php  if ($public_profiles) { ?> checked <?php  } ?> />
			        <span><?php echo t('Enable public profiles.')?></span>
			      </label>
			</div>
	 	</div>
	 	<div class="control-group">
	 		<?php print $form->label('gravatar_fallback', t('Fall Back To Gravatar')); ?>
	 		<div class="controls">
	 		<label class="checkbox">
	 			<?php print $form->checkbox('gravatar_fallback', 1, $gravatar_fallback); ?> <span><?php print t('Use image from <a href="http://gravatar.com" target="_blank">gravatar.com</a> if the user has not uploaded one')?></span>
	 		</label>
	 		</div>
	 	</div>

	 	<div id="gravatar-options">
	 		<div class="control-group">
		 		<?php print $form->label('gravatar_max_level', t('Maximum Gravatar Rating')); ?>
		 		<div class="controls">
		 			<?php print $form->select('gravatar_max_level', $gravatar_level_options, $gravatar_max_level); ?>
		 		</div>
			</div>
			<div class="control-group">
		 		<?php print $form->label('gravatar_image_set', t('Gravatar Image Set')); ?>
		 		<div class="controls">
		 			<?php print $form->select('gravatar_image_set', $gravatar_set_options, $gravatar_image_set); ?>
		 		</div>
		 	</div>
	 	</div>

	</div>
<div class="ccm-pane-footer">
<? 
print $h->submit(t('Save'), 'public-profiles-form', 'right', 'primary');
?>
</div>
</div>

</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#gravatar_fallback').change(function(){
		if($(this).prop('checked') == true) {
			$('#gravatar-options').css('display', 'block');
		} else {
			$('#gravatar-options').css('display', 'none');
		}
	})
})
</script>
<style type="text/css">
#gravatar-options {
	display: <?php print $gravatar_fallback ? 'block' : 'none'; ?>;
}
</style>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>