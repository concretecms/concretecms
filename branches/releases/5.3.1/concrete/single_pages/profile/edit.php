<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="central" class="central-left">
    <div id="sidebar">
    	<div class="ccm-profile-header">
        	<a href="<?php echo View::url('/profile',$ui->getUserID())?>"><?php echo  $av->outputUserAvatar($ui)?></a><br />
            <a href="<?php echo View::url('/profile',$ui->getUserID())?>"><?php echo  $ui->getUsername()?></a>
        </div>
        <h4 style="margin-top: 0px"><?php echo t('Member Since')?></h4>
        <?php echo date('F d, Y', strtotime($ui->getUserDateAdded()))?>
        <?php  
		$bt = BlockType::getByHandle('autonav');
		$bt->controller->displayPages = 'current';
		$bt->controller->orderBy = 'display_asc';
		$bt->controller->displaySubPages = 'relevant';
		$bt->controller->displaySubPageLevels = 'enough';
		$bt->controller->displaySystemPages = true;
		$bt->render('view');
		?>
    </div>
    
    <div id="body">	

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
        
        
        <h1><?php echo t('Edit Profile')?></h1>
        <div class="ccm-form">
            <form method="post" action="<?php echo $this->action('save')?>" id="profile-edit-form">
            <?php  $attribs = UserAttributeKey::getRegistrationList(); 
            if(is_array($attribs) && count($attribs)) { 
            ?>
                <fieldset>
                <div>
                    <?php echo $form->label('uEmail', t('Email'))?>
                    <span class="required">*</span> <?php echo $form->text('uEmail',$ui->getUserEmail())?>
                </div>	
                <?php 
                foreach($attribs as $ak) { 
                    if ($ak->getKeyType() == 'HTML') { ?>
                        <div><?php echo $ak->outputHTML()?></div>
                    <?php  } else { ?>
                        <div>
                            <?php echo $form->label($ak->getFormElementName(), $ak->getKeyName())?> <?php  if ($ak->isKeyRequired()) { ?><span class="required">*</span><?php  } ?>
                            <?php echo $ak->outputHTML($ui->getUserID())?>
                        </div>
                    <?php  } ?>
                <?php  } ?>
                </fieldset>
            <?php  } ?>
            <h3><?php echo t('Change Password')?></h3>
            <p><?php echo t("Leave blank if you'd like your password to remain the same")?></p>
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
</div>