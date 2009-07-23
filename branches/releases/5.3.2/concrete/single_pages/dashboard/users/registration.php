<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
$h = Loader::helper('concrete/interface');
?>

<div class="ccm-module">
    <h1><span><?php echo t('Registration')?></span></h1>
    <div class="ccm-dashboard-inner">
    <form method="post" id="registration-type-form" action="<?php echo $this->url('/dashboard/users/registration', 'update_registration_type')?>">        
        <div class="ccm-dashboard-radio"><input type="radio" name="registration_type" value="disabled" style="vertical-align: middle" <?php  if ($registration_type == "disabled") { ?> checked <?php  } ?> /> <?php echo t('Registration is disabled')?></div>
        <div class="ccm-dashboard-radio"><input type="radio" name="registration_type" value="validate_email" style="vertical-align: middle" <?php  if ($registration_type == "validate_email") { ?> checked <?php  } ?> /> <?php echo t('Registration is enabled, email must be validated.')?></div>
        <div class="ccm-dashboard-radio"><input type="radio" name="registration_type" value="manual_approve" style="vertical-align: middle" <?php  if ($registration_type == "manual_approve") { ?> checked <?php  } ?> /> <?php echo t('Registration is enabled, but must be manually approved.')?></div>
        <div class="ccm-dashboard-radio"><input type="radio" name="registration_type" value="enabled" style="vertical-align: middle" <?php  if ($registration_type == "enabled") { ?> checked <?php  } ?> /> <?php echo t('Registration is enabled.')?></div>
	        
    	<br />
<div class="ccm-dashboard-checkbox"><input type="checkbox" name="enable_openID" value="1" style="vertical-align: middle" <?php  if ($enable_openID) { ?> checked <?php  } ?> /> <?php echo t('Enable OpenID')?></div>
        <br />
        <div class="ccm-dashboard-checkbox"><input type="checkbox" name="email_as_username" value="1" style="vertical-align: middle" <?php  if ($email_as_username) { ?> checked <?php  } ?> /> <?php echo t('Use email address as username')?></div>
        
        <?php 
        $b1 = $h->submit(t('Save'), 'registration-type-form');
        print $h->buttons($b1);
        ?>
        <br class="clear" />
    </form>    
    </div>
</div>

<div class="ccm-module">
    <h1><span><?php echo t('Public Profiles')?></span></h1>
    <div class="ccm-dashboard-inner">
    <form method="post" id="public-profiles-form" action="<?php echo $this->url('/dashboard/users/registration', 'update_profiles')?>">
        
        <div class="ccm-dashboard-checkbox"><input type="checkbox" name="public_profiles" value="1" style="vertical-align: middle" <?php  if ($public_profiles) { ?> checked <?php  } ?> /> <?php echo t('Enable public profiles.')?></div>
        <div class="ccm-dashboard-description"><?php echo t('Enable public profile pages for site members.')?></div>
        
        <?php 
        $b1 = $h->submit(t('Save'), 'public-profiles-form');
        print $h->buttons($b1);
        ?>
        <br class="clear" />
    </form>    
    </div>
</div>