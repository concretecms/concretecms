<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Public Registration'), t('Control the options available for Public Registration.'), false, false);?>
<?php
$h = Loader::helper('concrete/interface');
?>	
	<br />
    <form method="post" id="registration-type-form" action="<?php echo $this->url('/dashboard/system/registration/public_registration', 'update_registration_type')?>">  
    
    <div class="ccm-dashboard-inner"> 
    	
    	<div class="clearfix">
            <label id="optionsCheckboxes"><strong><?php echo t('Registration Options')?></strong></label>
            <div class="input">
			  <ul class="inputs-list">
			    <li>
			      <label>
			        <input type="radio" name="registration_type" value="disabled" style="vertical-align: middle" <?php echo ( $registration_type == "disabled" || !strlen($registration_type) )?'checked':''?> />
			        <span><?php echo t('Registration is disabled')?></span>
			      </label>
			    </li> 
			    <li>
			      <label>
			        <input type="radio" name="registration_type" value="validate_email" style="vertical-align: middle" <?php echo ( $registration_type == "validate_email" )?'checked':''?> />
			        <span><?php echo t('Registration is enabled, email must be validated.')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="registration_type" value="manual_approve" style="vertical-align: middle" <?php echo ( $registration_type == "manual_approve" )?'checked':''?> />
			        <span><?php echo t('Registration is enabled, but must be manually approved.')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="registration_type" value="enabled" style="vertical-align: middle" <?php echo ( $registration_type == "enabled" )?'checked':''?> />
			        <span><?php echo t('Registration is enabled.')?></span>
			      </label>
			    </li>  
			  </ul>
			</div>
		</div>  
		
		<div class="clearfix">
            <label id="optionsCheckboxes"><strong><?php echo t('Additional Options')?></strong></label>
            <div class="input">
			  <ul class="inputs-list">
			    <li>
			      <label>
			        <input type="checkbox" name="enable_registration_captcha" value="1" style="vertical-align: middle" <?php echo ( $enable_registration_captcha )?'checked':''?> />
			        <span><?php echo t('Solving a <a href="%s" target="_blank">CAPTCHA</a> is required to register.', 'http://en.wikipedia.org/wiki/Captcha')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="checkbox" name="enable_openID" value="1" style="vertical-align: middle" <?php echo ( $enable_openID )?'checked':''?> />
			        <span><?php echo t('Enable OpenID')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			       <input type="checkbox" name="email_as_username" value="1" style="vertical-align: middle" <?php echo ( $email_as_username )?'checked':''?> />
			        <span><?php echo t('Login using email address.')?></span>
			      </label>
			    </li>  
			  </ul>
			</div>
        </div>  
		
		<br />
		<?php 
		$b1 = $h->submit(t('Save'), 'registration-type-form');
		print $h->buttons($b1);
		?>   
	</div>
</form> 	
    
   
 <script type="text/javascript">
 $(function() {
 	var val = $("input[name=registration_type]:checked").val();
	if (val == 'disabled') {
		$("input[name=enable_registration_captcha]").attr('disabled', true);
	}
	$("input[name=registration_type]").click(function() {
		if ($(this).val() == 'disabled') { 
			$("input[name=enable_registration_captcha]").attr('disabled', true);
			$("input[name=enable_registration_captcha]").attr('checked', false);
		} else {
			$("input[name=enable_registration_captcha]").attr('disabled', false);
		}	
	});
 });
 </script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>