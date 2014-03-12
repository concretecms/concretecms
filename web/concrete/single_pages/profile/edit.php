<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-wrapper">
    <?php  Loader::element('profile/sidebar', array('profile'=> $ui)); ?>    
    <div id="ccm-profile-body">	

		<?php  if (isset($error) && $error->has()) {
            $error->output();
        } else if (isset($message)) { ?>
            <div class="message"><?php echo $message?></div>
            <script type="text/javascript">
            $(function() {
                $("div.message").show('highlight', {}, 500);
            });
            </script>
        <?php  } ?>
        
        
        <h1 class="profilin"><?php echo t('Edit Profile')?></h1>
        <div class="ccm-form">
            <form method="post" action="<?php echo $this->action('save')?>" id="profile-edit-form" enctype="multipart/form-data">
<?php
						$valt->output('profile_edit');
						$attribs = UserAttributeKey::getEditableInProfileList(); 
            if(is_array($attribs) && count($attribs)) { 
            ?>
                <fieldset>
                <div class="ccm-profile-attribute">
                    <?php echo $form->label('uEmail', t('Email'))?> <span class="ccm-required">*</span><br/>
                    <?php echo $form->text('uEmail',$ui->getUserEmail())?>
                </div>
                <?php  if(ENABLE_USER_TIMEZONES) { ?>
                    <div class="ccm-profile-attribute">
                        <?php echo  $form->label('uTimezone', t('Time Zone'))?> <span class="ccm-required">*</span><br/>
                        <?php echo  $form->select('uTimezone', 
							$date->getTimezones(), 
							($ui->getUserTimezone()?$ui->getUserTimezone():date_default_timezone_get())
					); ?>
                    </div>
 				<?php  } ?>               
                <?php 
                $af = Loader::helper('form/attribute');
                $af->setAttributeObject($ui);
                foreach($attribs as $ak) {
                	print '<div class="ccm-profile-attribute">';
                	print $af->display($ak, $ak->isAttributeKeyRequiredOnProfile());
                	print '</div>';
                } ?>
                </fieldset>
            <?php  } ?>
            <h3><?php echo t('Change Password')?></h3>
            <p><?php echo t("Leave this blank to keep your current password.")?></p>
            <fieldset>
                <div>
                    <?php echo $form->label('uPasswordNew', t('New Password'))?>
                    <?php echo $form->password('uPasswordNew')?>
                </div>	
                <div>
                    <?php echo $form->label('uPasswordNewConfirm', t('Confirm New Password'))?>
                    <?php echo $form->password('uPasswordNewConfirm')?>
                </div>   
            </fieldset>
            <div class="spacer" style="margin-top:20px">&nbsp;</div>
            <?php echo $form->submit('save', t('Save'))?>
            </form>
            <div class="spacer">&nbsp;</div>
        </div>
        
    </div>
	
	<div class="ccm-spacer"></div>
</div>
