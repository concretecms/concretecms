<? defined('C5_EXECUTE') or die("Access Denied."); 
$h = Loader::helper('concrete/interface');
?>

<style type="text/css">
.ccm-module form{ width:auto; height:auto; padding:0px; padding-bottom:10px; display:block; }
.ccm-module form div.ccm-dashboard-inner{ margin-bottom:0px !important; }
</style>

<div class="ccm-module">
	<form method="post" id="login-redirect-form" action="<?=$this->url('/dashboard/users/registration', 'update_login_redirect')?>">
		<?=$this->controller->token->output('update_login_redirect')?>
		
		<h1><span><?=t('Where to send users on login?')?></span></h1>
		<div class="ccm-dashboard-inner"> 
			
			<div class="ccm-dashboard-radio">
				<input type="radio" name="LOGIN_REDIRECT" value="HOMEPAGE"  <?=(!strlen($site_login_redirect) || $site_login_redirect=='HOMEPAGE')?'checked':''?> /> <?=t('Homepage')?>
			</div> 
			
			<div class="ccm-dashboard-radio">
				<input type="radio" name="LOGIN_REDIRECT" value="PROFILE" <?=($site_login_redirect=='PROFILE')?'checked':''?> /> <?=t('Member profile (if enabled)')?>
			</div>			
			
			<div class="ccm-dashboard-radio">
				<input type="radio" name="LOGIN_REDIRECT" value="CUSTOM" <?=($site_login_redirect=='CUSTOM')?'checked':''?> /> <?=t('Custom page')?>
				
				<div id="login_redirect_custom_cid_wrap" style="display:<?=( $site_login_redirect=='CUSTOM' )?'block':'none'?>">
				<? 
				$formPageSelector = Loader::helper('form/page_selector'); 
				echo $formPageSelector->selectPage('LOGIN_REDIRECT_CID', $login_redirect_cid ); 
				?> 
				</div>
				
				<script>			
				$(function(){ 
					$("#login_redirect_custom_cid_wrap .dialog-launch").dialog(); 
				
					$("input[name='LOGIN_REDIRECT']").each(function(i,el){ 
						el.onchange=function(){isLoginRedirectCustom();}
					})	 	
				});	
				function isLoginRedirectCustom(){
					if($("input[name='LOGIN_REDIRECT']:checked").val()=='CUSTOM'){
						$('#login_redirect_custom_cid_wrap').css('display','block');
					}else{
						$('#login_redirect_custom_cid_wrap').css('display','none');
					}
				}			
				</script>
			</div>
			
			<div class="ccm-dashboard-radio" style="margin-top:16px;" > 
				<input type="checkbox" name="LOGIN_ADMIN_TO_DASHBOARD" value="1" <?=($site_login_admin_to_dashboard)?'checked':''?> /> <?=t('Redirect administrators to dashboard')?>
			</div>		
	
			<?
			$b1 = $h->submit(t('Update Login Redirect'), 'login-redirect-form');
			print $h->buttons($b1);
			?>
			
			<br class="clear" />
		</div>
	</form>	
	
	
    <form method="post" id="public-profiles-form" action="<?=$this->url('/dashboard/users/registration', 'update_profiles')?>">
		<h1><span><?=t('Public Profiles')?></span></h1>
		<div class="ccm-dashboard-inner">
			
			<div class="ccm-dashboard-checkbox"><input type="checkbox" name="public_profiles" value="1" style="vertical-align: middle" <? if ($public_profiles) { ?> checked <? } ?> /> <?=t('Enable public profiles.')?></div>
			<div class="ccm-dashboard-description"><?=t('Enable public profile pages for site members.')?></div>
			
			<?
			$b1 = $h->submit(t('Save'), 'public-profiles-form');
			print $h->buttons($b1);
			?>
			<br class="clear" />    
		</div>
    </form>		

    <form method="post" id="user-timezone-form" action="<?=$this->url('/dashboard/users/registration', 'update_user_timezones')?>">
		<h1><span><?=t('Time Zone Support')?></span></h1>
		<div class="ccm-dashboard-inner">
			
			<div class="ccm-dashboard-checkbox"><input type="checkbox" name="user_timezones" value="1" style="vertical-align: middle" <? if ($user_timezones) { ?> checked <? } ?> /> <?=t('Enable user defined time zones.')?></div>
			<div class="ccm-dashboard-description"><?=t('Allows site members to display date/time information in their time zone.')?></div>
			
			<?
			$b1 = $h->submit(t('Save'), 'user-timezone-form');
			print $h->buttons($b1);
			?>
			<br class="clear" />    
		</div>
    </form>		
</div>

<div class="ccm-module">

    <form method="post" id="registration-type-form" action="<?=$this->url('/dashboard/users/registration', 'update_registration_type')?>">        
		<h1><span><?=t('Registration')?></span></h1>
		<div class="ccm-dashboard-inner">
			<div class="ccm-dashboard-radio"><input type="radio" name="registration_type" value="disabled" style="vertical-align: middle" <? if ($registration_type == "disabled") { ?> checked <? } ?> /> <?=t('Registration is disabled')?></div>
			<div class="ccm-dashboard-radio"><input type="radio" name="registration_type" value="validate_email" style="vertical-align: middle" <? if ($registration_type == "validate_email") { ?> checked <? } ?> /> <?=t('Registration is enabled, email must be validated.')?></div>
			<div class="ccm-dashboard-radio"><input type="radio" name="registration_type" value="manual_approve" style="vertical-align: middle" <? if ($registration_type == "manual_approve") { ?> checked <? } ?> /> <?=t('Registration is enabled, but must be manually approved.')?></div>
			<div class="ccm-dashboard-radio"><input type="radio" name="registration_type" value="enabled" style="vertical-align: middle" <? if ($registration_type == "enabled") { ?> checked <? } ?> /> <?=t('Registration is enabled.')?></div>
				
			<br />
			<div class="ccm-dashboard-checkbox"><input type="checkbox" name="enable_registration_captcha" value="1" style="vertical-align: middle" <? if ($enable_registration_captcha) { ?> checked <? } ?> /> <?=t('Solving a <a href="%s" target="_blank">CAPTCHA</a> is required to register.', 'http://en.wikipedia.org/wiki/Captcha')?></div>
			<br />
			
			<div class="ccm-dashboard-checkbox"><input type="checkbox" name="enable_openID" value="1" style="vertical-align: middle" <? if ($enable_openID) { ?> checked <? } ?> /> <?=t('Enable OpenID')?></div>
			<br />
			<div class="ccm-dashboard-checkbox"><input type="checkbox" name="email_as_username" value="1" style="vertical-align: middle" <? if ($email_as_username) { ?> checked <? } ?> /> <?=t('Login using email address.')?></div>
			
			<?
			$b1 = $h->submit(t('Save'), 'registration-type-form');
			print $h->buttons($b1);
			?>
			<br class="clear" />	   
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
</div>