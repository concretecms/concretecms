<h1><?=t('Edit Profile')?></h1>

<div class="ccm-form">
    <form method="post" action="<?=$this->action('save')?>" id="profile-edit-form">
    <?
    $attribs = UserAttributeKey::getRegistrationList();
    foreach($attribs as $ak) { 
        if ($ak->getKeyType() == 'HTML') { ?>
            <div><?=$ak->outputHTML()?></div>
        <? } else { ?>
            <div>
            <?=$form->label($ak->getFormElementName(), $ak->getKeyName())?> <? if ($ak->isKeyRequired()) { ?><span class="required">*</span><? } ?>
            <?=$ak->outputHTML($ui->getUserID())?>
            </div>
            <br/>
            
        <? } ?>
    <? } ?>
    
    <div class="ccm-button">
        <?=$form->submit('save', t('Save'))?>
    </div>
    </form>
    
    <div class="spacer">&nbsp;</div>

</div>