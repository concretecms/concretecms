<? defined('C5_EXECUTE') or die("Access Denied.");
$token = \Core::make('token');
?>
    <form method="post" id="public-profiles-form" action="<?php echo $view->url('/dashboard/system/registration/profiles', 'update_profiles')?>">
		<?php
		$token->output('update_profile');
		?>

	<div class="container">
    	<div class="row">
    		<div class="col-sm-12">
		    	<div class="form-group">
		            <label id="optionsCheckboxes" for="public_profiles" class="control-label"><?php echo t('Profile Options')?></label>
					      <div class="checkbox">
					      	<label>
					        <input type="checkbox" id="public_profiles" name="public_profiles" value="1" <?php  if ($public_profiles) { ?> checked <?php  } ?> />
					        <span><?php echo t('Enable public profiles.')?></span>
						    </label>
						 </div>
			 	</div>
			 	<div class="form-group">
			 		<?php print $form->label('gravatar_fallback', t('Fall Back To Gravatar')); ?>
			 		<div class="checkbox">
			 			<label>
			 			<?php print $form->checkbox('gravatar_fallback', 1, $gravatar_fallback); ?> <span><?php print t('Use image from <a href="http://gravatar.com" target="_blank">gravatar.com</a> if the user has not uploaded one')?></span>
				 		</label>
			 		</div>
			 	</div>
			 </div>
		</div>
	 	<div class="row">
	 	<div id="gravatar-options" class="col-sm-3">
	 		<div class="form-group">
		 		<?php print $form->label('gravatar_max_level', t('Maximum Gravatar Rating')); ?>
	 			<?php print $form->select('gravatar_max_level', $gravatar_level_options, $gravatar_max_level); ?>
			</div>
			<div class="control-group">
		 		<?php print $form->label('gravatar_image_set', t('Gravatar Image Set')); ?>
	 			<?php print $form->select('gravatar_image_set', $gravatar_set_options, $gravatar_image_set); ?>
		 	</div>
	 	</div>
	 </div>
	</div>

	<div class="ccm-dashboard-form-actions-wrapper">
	<div class="ccm-dashboard-form-actions">
		<button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
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
