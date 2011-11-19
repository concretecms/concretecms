<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Public Profiles'), t('Control the options available for Public Profiles.'), false, false);?>
<?php
$h = Loader::helper('concrete/interface');
?>
    <form method="post" id="public-profiles-form" action="<?php echo $this->url('/dashboard/system/registration/profiles', 'update_profiles')?>">  
    
    <div class="ccm-pane-body"> 
    	
    	<div class="clearfix">
            <label id="optionsCheckboxes" for="public_profiles"><strong><?php echo t('Profile Options')?></strong></label>
            <div class="input">
			  <ul class="inputs-list">
			    <li>
			      <label>
			        <input type="checkbox" id="public_profiles" name="public_profiles" value="1" <?php  if ($public_profiles) { ?> checked <?php  } ?> />
			        <span><?php echo t('Enable public profiles.')?></span>
			      </label>
			    </li> 
			  </ul>
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

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>