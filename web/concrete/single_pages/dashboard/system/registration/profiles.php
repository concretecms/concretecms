<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Public Profiles'), t('Control the options available for Public Profiles.'), false, false);?>
<?php
$h = Loader::helper('concrete/interface');
?>
	<br />
    <form method="post" id="public-profiles-form" action="<?php echo $this->url('/dashboard/system/registration/profiles', 'update_profiles')?>">  
    
    <div class="ccm-dashboard-inner"> 
    	
    	<div class="clearfix">
            <label id="optionsCheckboxes"><strong><?php echo t('Profile Options')?></strong></label>
            <div class="input">
			  <ul class="inputs-list">
			    <li>
			      <label>
			        <input type="checkbox" name="public_profiles" value="1" style="vertical-align: middle" <?php  if ($public_profiles) { ?> checked <?php  } ?> />
			        <span><?php echo t('Enable public profiles.')?></span>
			      </label>
			    </li> 
			  </ul>
			</div>
		<br />
		<?php 
		$b1 = $h->submit(t('Save'), 'public-profiles-form');
		print $h->buttons($b1);
		?>   
	 	</div>
	</div>
</form> 	

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>