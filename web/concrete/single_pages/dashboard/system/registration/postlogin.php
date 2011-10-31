<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Post Login'), t('Control the options available for Post Login behavior.'), false, false);?>
<?php
$h = Loader::helper('concrete/interface');
?>
	<br />
    <form method="post" id="login-redirect-form" action="<?php echo $this->url('/dashboard/system/registration/postlogin', 'update_login_redirect')?>">  
    	<?php echo $this->controller->token->output('update_login_redirect')?>
    <div class="ccm-dashboard-inner"> 
    	
    	<div class="clearfix">
            <label id="optionsCheckboxes"><strong><?php echo t('Where to send users on login?')?></strong></label>
            <div class="input">
			  <ul class="inputs-list">
			    <li>
			      <label>
			        <input type="radio" name="LOGIN_REDIRECT" value="HOMEPAGE"  <?php echo (!strlen($site_login_redirect) || $site_login_redirect=='HOMEPAGE')?'checked':''?> />
			        <span><?php echo t('Homepage')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="LOGIN_REDIRECT" value="PROFILE" <?php echo ($site_login_redirect=='PROFILE')?'checked':''?> />
			        <span><?php echo t('Member profile (if enabled)')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="LOGIN_REDIRECT" value="CUSTOM" <?php echo ($site_login_redirect=='CUSTOM')?'checked':''?> />
			        <span><?php echo t('Custom page')?></span>
			      </label>
			      	<div id="login_redirect_custom_cid_wrap" style="display:<?php echo ( $site_login_redirect=='CUSTOM' )?'block':'none'?>">
					<?php  
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
			    </li>
			    <li>
			      <label>
			       <input type="checkbox" name="LOGIN_ADMIN_TO_DASHBOARD" value="1" <?php echo ($site_login_admin_to_dashboard)?'checked':''?> />
			        <span><?php echo t('Redirect administrators to dashboard')?></span>
			      </label>
			    </li>
			  </ul>
			</div>
		<br />
		<?php 
		$b1 = $h->submit(t('Save'), 'login-redirect-form');
		print $h->buttons($b1);
		?>   
	 	</div>
	</div>
</form> 	

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
