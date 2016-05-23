<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<?php if (count($interfacelocales) <= 1) {
    ?>
<fieldset>
	<?=t("You don't have any interface languages installed. You must run concrete5 in English.");
    ?>
</fieldset>
<?php 
} else {
    ?>

<form method="post" class="form-horizontal" action="<?=$view->action('save_interface_language')?>">
    <fieldset>

        <div class="form-group">
        <?=$form->label('LANGUAGE_CHOOSE_ON_LOGIN', t('Login'))?>
            <div class="checkbox">
                <label><?=$form->checkbox('LANGUAGE_CHOOSE_ON_LOGIN', 1, $LANGUAGE_CHOOSE_ON_LOGIN)?><?=t('Offer choice of language on login.')?></label>
            </div>
        </div>
        <div class="form-group">
            <?=$form->label('SITE_LOCALE', t('Default Language'))?>
            <div class="checkbox">
                <?=$form->select('SITE_LOCALE', $interfacelocales, $SITE_LOCALE);
    ?>
            </div>
        </div>

        <br/>
        <?=Loader::helper('validation/token')->output('save_interface_language')?>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?= Loader::helper('concrete/ui')->submit(t('Save'), 'save', 'right', 'btn-primary')?>
        </div>
    </div>
</form>
	
<?php 
} ?>