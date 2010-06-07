<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-profile-wrapper">
    <? Loader::element('profile/sidebar', array('profile'=> $ui)); ?>    
    <div id="ccm-profile-body">	

		<? if (isset($error) && $error->has()) {
            $error->output();
        } else if (isset($message)) { ?>
            <div class="message"><?=$message?></div>
            <script type="text/javascript">
            $(function() {
                $("div.message").show('highlight', {}, 500);
            });
            </script>
        <? } ?>
        
        
        <h1><?=t('Edit Profile')?></h1>
        <div class="ccm-form">
            <form method="post" action="<?=$this->action('save')?>" id="profile-edit-form" enctype="multipart/form-data">
            <? $attribs = UserAttributeKey::getEditableInProfileList(); 
            if(is_array($attribs) && count($attribs)) { 
            ?>
                <fieldset>
                <div class="ccm-profile-attribute">
                    <?=$form->label('uEmail', t('Email'))?> <span class="ccm-required">*</span><br/>
                    <?=$form->text('uEmail',$ui->getUserEmail())?>
                </div>
                <? if(ENABLE_USER_TIMEZONES) { ?>
                    <div class="ccm-profile-attribute">
                        <?= $form->label('uTimezone', t('Time Zone'))?> <span class="ccm-required">*</span><br/>
                        <?= $form->select('uTimezone', 
							$date->getTimezones(), 
							($ui->getUserTimezone()?$ui->getUserTimezone():date_default_timezone_get())
					); ?>
                    </div>
 				<? } ?>               
                <?
                $af = Loader::helper('form/attribute');
                $af->setAttributeObject($ui);
                foreach($attribs as $ak) {
                	print '<div class="ccm-profile-attribute">';
                	print $af->display($ak, $ak->isAttributeKeyRequiredOnProfile());
                	print '</div>';
                } ?>
                </fieldset>
            <? } ?>
            <h3><?=t('Change Password')?></h3>
            <p><?=t("Leave blank if you'd like your password to remain the same")?></p>
            <fieldset>
                <div>
                    <?=$form->label('uPasswordNew', t('New Password'))?>
                    <?=$form->password('uPasswordNew')?>
                </div>	
                <div>
                    <?=$form->label('uPasswordNewConfirm', t('Confirm New Password'))?>
                    <?=$form->password('uPasswordNewConfirm')?>
                </div>   
            </fieldset>
            <div class="spacer" style="margin-top:20px">&nbsp;</div>
            <?=$form->submit('save', t('Save'))?>
            </form>
            <div class="spacer">&nbsp;</div>
        </div>
        
    </div>
	
	<div class="ccm-spacer"></div>
</div>