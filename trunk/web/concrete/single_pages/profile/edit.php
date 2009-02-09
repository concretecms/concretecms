<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="central" class="central-left">
    <div id="sidebar">
    	<div class="ccm-profile-header">
        	<a href="<?=View::url('/profile',$ui->getUserID())?>"><?= $av->outputUserAvatar($ui)?></a><br />
            <a href="<?=View::url('/profile',$ui->getUserID())?>"><?= $ui->getUsername()?></a>
        </div>
        <h4 style="margin-top: 0px"><?=t('Member Since')?></h4>
        <?=date('F d, Y', strtotime($ui->getUserDateAdded()))?>
        <? 
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
            <form method="post" action="<?=$this->action('save')?>" id="profile-edit-form">
            <? $attribs = UserAttributeKey::getRegistrationList(); 
            if(is_array($attribs) && count($attribs)) { 
            ?>
                <fieldset>
                <div>
                    <?=$form->label('uEmail', t('Email'))?>
                    <span class="required">*</span> <?=$form->text('uEmail',$ui->getUserEmail())?>
                </div>	
                <?
                foreach($attribs as $ak) { 
                    if ($ak->getKeyType() == 'HTML') { ?>
                        <div><?=$ak->outputHTML()?></div>
                    <? } else { ?>
                        <div>
                            <?=$form->label($ak->getFormElementName(), $ak->getKeyName())?> <? if ($ak->isKeyRequired()) { ?><span class="required">*</span><? } ?>
                            <?=$ak->outputHTML($ui->getUserID())?>
                        </div>
                    <? } ?>
                <? } ?>
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
</div>