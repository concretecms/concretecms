<?php defined('C5_EXECUTE') or die("Access Denied.");

/* @var $form \Concrete\Core\Form\Service\Form */

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
    <?php
    $dateFormats = $rssObj->getDefaultDateTimeFormats();
    $dateFormats[':custom:'] = t('Custom date/time format');
    $standardDateFormat = $rssObj->dateFormat;
    $customDateFormat = '';
    if(!$standardDateFormat) {
        reset($dateFormats);
        $standardDateFormat = key($dateFormats);
    }
    if(!array_key_exists($standardDateFormat, $dateFormats)) {
        $customDateFormat = $standardDateFormat;
        $standardDateFormat = ':custom:';
    }
    echo $form->select('standardDateFormat', $dateFormats, $standardDateFormat);
    ?>
</div>
<?php
$now = new \DateTime();
foreach(array_keys($dateFormats) as $dateFormat) {
    ?><div class="form-group ccm-dateFormat-case" data-format="<?php echo h($dateFormat) ?>" style="display: none"><?php
        switch($dateFormat) {
            case ':custom:':
                echo $form->label('customDateFormat', t('Custom date format'));
                echo $form->text('customDateFormat', $customDateFormat);
                ?><a href="http://php.net/manual/function.date.php" target="_blank"><?php echo t('See the PHP manual')?></a><?php
                break;
            default:
                echo $form->label('', t('Example'));
                ?><div class="form-control"><?php echo h($rssObj->formatDateTime($now, $dateFormat)); ?></div><?php
                break;
        }
    ?></div><?php
}
?>
<script>$(document).ready(function() {
	function update() {
		console.log($('#standardDateFormat').val());
		$('.ccm-dateFormat-case').hide().filter('[data-format="' + $('#standardDateFormat').val() + '"]').show();
	}
	$('#standardDateFormat').on('change', function() { update(); });
	update();
});</script>
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
