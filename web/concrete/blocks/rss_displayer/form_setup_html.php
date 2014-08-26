<?php
defined('C5_EXECUTE') or die("Access Denied.");
if (!$rssObj->dateFormat) {
    $rssObj->dateFormat = t('F jS');
}
?>
<div class="form-group">
    <?= $form->label('url', t('Feed URL')) ?>
    <input name="url" class="form-control" placeholder="Feed URL" value="<?= h($rssObj->url) ?>"/>
</div>
<div class="form-group">
    <label for="title">
        <?= t('Feed Title') ?>
        <span class="help-block" style="font-weight: normal;display: inline">(<?= t('Optional') ?>)</span>
    </label>
    <input name="title" class="form-control" placeholder="Feed Title" value="<?= h($rssObj->title) ?>"/>
</div>
<div class="form-group">
    <?= $form->label('dateFormat', t('Date Format')) ?>
    <input name="dateFormat" class="form-control" placeholder="Date Format" value="<?= h($rssObj->dateFormat) ?>"/>
</div>
<div class="form-group">
    <?= $form->label('itemsToDisplay', t('Items to Show')) ?>
    <input name="itemsToDisplay" class="form-control" placeholder="10" value="<?= h($rssObj->itemsToDisplay) ?>"/>
</div>
<div class="form-group">
    <div class="checkbox">
    <label>
        <input type="checkbox" value="1" name="showSummary"<?= (!!$rssObj->showSummary ? ' checked' : '') ?> />
        <?= t('Include Summary') ?>
    </label>
    </div>
</div>
<div class="form-group">
    <div class="checkbox">
    <label>
        <input type="checkbox" value="1"
               name="launchInNewWindow"<?= (!!$rssObj->launchInNewWindow ? ' checked' : '') ?> />
        <?= t('Open links in a new window') ?>
    </label>
    </div>
</div>
