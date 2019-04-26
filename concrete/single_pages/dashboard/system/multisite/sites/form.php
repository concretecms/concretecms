<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" action="<?=$view->action('submit')?>">
    <?=$token->output('submit')?>
    <fieldset>
        <legend><?=t('Standard Details')?></legend>
        <div class="form-group">
            <?=$form->label('handle', t('Handle'))?>
            <?=$form->text('handle')?>
        </div>
        <div class="form-group">
            <?=$form->label('name', t('Name'))?>
            <?=$form->text('name')?>
        </div>
        <div class="form-group">
            <?=$form->label('canonical_url', t('Canonical URL'), ['class' => 'launch-tooltip', 'title' => t('The full URL at which this site will live. e.g. http://www.my-website.com')])?>
            <?=$form->text('canonical_url')?>
        </div>
        <div class="form-group">
            <label class="control-label launch-tooltip" data-placement="right" title="<?= t(
                'This will control the default timezone that will be used to display date/times.'
            ) ?>">
                <?php echo t('Default Timezone') ?>
            </label>
            <select class="form-control" name="timezone">
                <?php
                foreach ($timezones as $areaName => $namedTimezones) {
                    ?>
                    <optgroup label="<?= h($areaName) ?>">
                        <?php
                        foreach ($namedTimezones as $tzID => $tzName) {
                            ?>
                            <option value="<?= h($tzID) ?>"<?= strcasecmp($tzID, $timezone) === 0 ? ' selected="selected"' : '' ?>>
                                <?= h($tzName) ?>
                            </option>
                            <?php
                        } ?>
                    </optgroup>
                    <?php
                }
                ?>
            </select>
        </div>
        <input type="hidden" name="siteTypeID" value="<?=$type->getSiteTypeID()?>" />
    </fieldset>

    <?php
    if ($optionsForm->formExists()) { ?>
    <fieldset>
        <legend><?=t('%s Options', $type->getSiteTypeName())?></legend>
        <?php
            $optionsForm->renderForm();
        ?>
    </fieldset>
    <?php } ?>



    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a class="pull-left btn btn-default" href="<?=URL::to('/dashboard/system/multisite/sites')?>"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=$buttonLabel?></button>
        </div>
    </div>
</form>
